<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Services\OrderService;

class OrdersController extends Controller
{
	public function store(OrderRequest $request, OrderService $orderService)
	{
		$user = $request->user();
		
		$address = UserAddress::find($request->input('address_id'));

		return $orderService->store($user, $address, $request->input('remark'), $request->input('items'));
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

}

