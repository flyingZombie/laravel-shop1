<?php 

namespace App\Services;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Carbon\Carbon;
use App\Models\CouponCode;
use App\Exceptions\CouponCodeUnavailableException;
use Elasticsearch\Endpoints\Indices\Close;
use http\Exception\InvalidArgumentException;
use App\Exceptions\InternalException;
use App\Jobs\RefundInstallmentOrder;
/**
 * 
 */
class OrderService
{
    public function refundOrder(Order $order) {

        switch ($order->payment_method) {
            case 'wechat':

                $refundNo = Order::getAvailableRefundNo();

                app('wechat_pay')->refund([
                    'out_trade_no' => $order->no,
                    'total_fee' => $order->total_amount * 100,
                    'refund_fee' => $order->total_amount * 100,
                    'out_refund_no' => $refundNo,
                    //'notify_url' => 'http://requestbin.leo108.com/1ewu0zq1'
                    //'notify_url' => route('payment.wechat.refund_notify'),
                    'notify_url' => ngrok_url('payment.wechat.refund_notify'),
                ]);

                $order->update([
                    'refund_no' => $refundNo,
                    'refund_status' => Order::REFUND_STATUS_PROCESSING,
                ]);

                break;

            case 'alipay':

                $refundNo = Order::getAvailableRefundNo();

                $ret = app('alipay')->refund([
                    'out_trade_no' => $order->no,
                    'refund_amount' => $order->total_amount,
                    'out_request_no' => $refundNo,
                ]);

                if ($ret->sub_code) {
                    $extra = $order->extra;
                    $extra['refund_failed_code'] = $ret->sub_code;
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                        'extra' => $extra,
                    ]);
                } else {
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
                    ]);
                }
                break;

            case 'installment':

                $order->update([
                  'refund_no' => Order::getAvailableRefundNo(),
                    'refund_status' => Order::REFUND_STATUS_PROCESSING,
                ]);

                dispatch(new RefundInstallmentOrder($order));

                break;

            default:
                throw new InternalException('Unknown order payment: '.$order->payment_method);
                break;
        }
    }

    public function store(User $user, UserAddress $address, $remark, $items, CouponCode $coupon = null)
	{
		if ($coupon) {
		    $coupon->checkAvailable($user);
        }

	    $order = \DB::transaction(function () use ($user, $address, $remark, $items, $coupon)
		{
			$address->update(['last_used_at' => Carbon::now()]);
			$order = new Order([
				'address' => [
					'address' => $address->full_address,
					'postcode' => $address->postcode,
					'contact_name' => $address->contact_name,
					'contact_phone' => $address->contact_phone,
				],
				'remark' => $remark,
				'total_amount' => 0,
                'type' => Order::TYPE_NORMAL,
			]);
			$order->user()->associate($user);
			$order->save();
			$totalAmount = 0;

			foreach ($items as $data) {
				$sku = ProductSku::find($data['sku_id']);
				$item = $order->items()->make([
					'amount' => $data['amount'],
					'price' => $sku->price,
				]);
				$item->product()->associate($sku->product_id);
				$item->productSku()->associate($sku);
				$item->save();
				$totalAmount += $sku->price * $data['amount'];
				if ($sku->decreaseStock($data['amount']) <= 0) {
					throw new InvalidRequestException('this product is out of stock!');
				}
			}

			if ($coupon) {
			    $coupon->checkAvailable($user, $totalAmount);
			    $totalAmount = $coupon->getAdjustedPrice($totalAmount);
			    $order->couponCode()->associate($coupon);
			    if ($coupon->changeUsed() <= 0) {
			        throw new CouponCodeUnavailableException('This coupon code has been used up');
                }
            }

			$order->update(['total_amount' => $totalAmount]);
			$skuIds = collect($items)->pluck('sku_id')->all();
			app(CartService::class)->remove($skuIds);

			return $order;

		});
		dispatch(new CloseOrder($order, config('app.order_ttl')));

		return $order;

	}

	public function crowdfunding(User $user, UserAddress $address, ProductSku $sku, $amount)
    {
	    $order = \DB::transaction(function () use ($amount, $sku, $user, $address) {

	        $address->update(['last_used_at' => Carbon::now()]);

	        $order = new Order([
              'address' => [
                      'address' => $address->full_address,
                      'postcode' => $address->postcode,
                      'contact_name' => $address->contact_name,
                      'contact_phone' => $address->contact_phone,
                  ],
                  'remark' => '',
                  'total_amount' => $sku->price * $amount,
                  'type' => Order::TYPE_CROWDFUNDING,
            ]);

	        $order->user()->associate($user);
            $order->save();

            $item = $order->items()->make([
                'amount' => $amount,
                'price' => $sku->price,
            ]);

            $item->product()->associate($sku->product_id);

            $item->productSku()->associate($sku);

            $item->save();

            if ($sku->decreaseStock($amount) <= 0) {
                throw new InvalidArgumentException('this product is out of stock');
            }

            return $order;
        });

	    $crowdfundingTtl = $sku->product->crowdfunding->end_at->getTimestamp() - time();

	    dispatch(new CloseOrder($order, min(config('app.order_ttl'), $crowdfundingTtl)));

	    return $order;
    }

    public function seckill(User $user, array $addressData, ProductSku $sku)
    {
        $order = \DB::transaction(function () use ($user, $addressData, $sku ) {

            //$address->update(['last_used_at' => Carbon::now()]);

            $order = new Order([
                'address' => [
                    'address' => $addressData['address'].' '.$addressData['suburb'].' '.$addressData['state'],
                    'post_code' => $addressData['postcode'],
                    'contact_name' => $addressData['contact_name'],
                    'contact_phone' => $addressData['contact_phone'],
                ],
                'remark' => '',
                'total_amount' => $sku->price,
                'type' => Order::TYPE_SECKILL,
            ]);

            $order->user()->associate($user);

            $order->save();

            $item = $order->items()->make([
                'amount' => 1,
                'price' => $sku->price,
            ]);

            $item->product()->associate($sku->product_id);

            $item->productSku()->associate($sku);

            $item->save();

            if ($sku->decreaseStock(1) <= 0) {
                throw new InvalidRequestException('This product is out of stock');
            }

            return $order;
        });

        dispatch(new CloseOrder($order, config('app.seckill_order_ttl')));

        return $order;
    }

}


