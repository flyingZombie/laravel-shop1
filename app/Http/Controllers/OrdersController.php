<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\ProductSku;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use http\Exception\InvalidArgumentException;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Services\OrderService;
use App\Http\Requests\SendReviewRequest;
use App\Events\OrderReviewed;
use App\Http\Requests\ApplyRefundRequest;
use App\Exceptions\CouponCodeUnavailableException;
use App\Models\CouponCode;
use App\Http\Requests\CrowdFundingOrderRequest;
use App\Http\Requests\SeckillOrderRequest;


class OrdersController extends Controller
{
	public function store(OrderRequest $request, OrderService $orderService)
	{
		$user = $request->user();
		
		$address = UserAddress::find($request->input('address_id'));

		$coupon = null;

		if ($code = $request->input('coupon_code')) {
		    $coupon = CouponCode::where('code', $code)->first();
		    if(!$coupon) {
		        throw new CouponCodeUnavailableException('This coupon code does not exist');
            }
        }

		return $orderService->store($user, $address, $request->input('remark'), $request->input('items'), $coupon);
	}

	public function index(Request $request)
	{
		$orders = Order::query()
		->with(['items.product', 'items.productSku'])
		->where('user_id', $request->user()->id)
		->orderBy('created_at', 'desc')
		->paginate();
		return view('orders.index', ['orders' => $orders]);
	}

	public function show(Order $order, Request $request)
	{
		$this->authorize('own', $order);
		return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product'])]);
	}

	public function received(Order $order, Request $request)
	{
		$this->authorize('own', $order);

		if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
			throw new InvalidRequestException('incorrect shipping status');
		}

		$order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

		//return redirect()->back(); //apply when using form submit
		return $order; //apply when using AXIOS ajax
	}

	public function review(Order $order)
	{

		$this->authorize('own', $order);
		if (!$order->paid_at) {
			throw new InvalidRequestException('this order is not paid yet and should not be reviewed.');
		}
		return view('orders.review', ['order' => $order->load(['items.productSku', 'items.product'])]);
	}

	public function sendReview(Order $order, SendReviewRequest $request)
	{


		$this->authorize('own', $order);

		if (!$order->paid_at) {
			throw new InvalidRequestException('This order is not paid yet, can\'t be reviewed');
		}

		if ($order->reviewed) {
			throw new InvalidRequestException('This order had been reviewed, no 2nd review');
		}

		$reviews = $request->input('reviews');

		\DB::transaction(function () use ($reviews, $order)
		{
			foreach ($reviews as $review) {
				$orderItem = $order->items()->find($review['id']);

				$orderItem->update([
					'rating' => $review['rating'],
					'review' => $review['review'],
					'reviewed_at' => Carbon::now(),
				]);
			} 
			$order->update(['reviewed' => true ]);
			event(new OrderReviewed($order));
		});
		return redirect()->back();
	}

	public function applyRefund(Order $order, ApplyRefundRequest $request)
	{
		$this->authorize('own', $order);

		if (!$order->paid_at) {
			throw new InvalidRequestException('This order is not paid yet, no refund.');
		}

		if ($order->type === Order::TYPE_CROWDFUNDING) {
		    throw new InvalidArgumentException('The crowd-funding order doesn\'t support refund');
        }

		if ($order->refund_status !== Order::REFUND_STATUS_PENDING) {
			throw new InvalidRequestException('This order already got refunded. please don\'t apply refund.');
		}

		$extra = $order->extra ?: [];
		$extra['refund_reason'] = $request->input('reason');
		$order->update([
			'refund_status' => Order::REFUND_STATUS_APPLIED,
			'extra' => $extra,
		]);

		return $order;
	}

	public function crowdfunding(CrowdFundingOrderRequest $request, OrderService $orderService)
    {
	    $user = $request->user();
	    $sku = ProductSku::find($request->input('sku_id'));
	    $address = UserAddress::find($request->input('address_id'));
	    $amount = $request->input('amount');

	    return $orderService->crowdfunding($user, $address, $sku, $amount);

    }

    public function seckill(SeckillOrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();
        //$address = UserAddress::find($request->input('address_id'));
        $sku = ProductSku::find($request->input('sku_id'));

        return $orderService->seckill($user, $request->input('address'), $sku);
    }


}

