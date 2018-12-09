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

/**
 * 
 */
class OrderService
{
	public function store(User $user, UserAddress $address, $remark, $items, CouponCode $coupon = null)
	{
		if ($coupon) {
		    $coupon->checkAvailable();
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
			    $coupon->checkAvailable($totalAmount);
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

}


 ?>