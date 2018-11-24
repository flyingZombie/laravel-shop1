<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;

class PaymentController extends Controller
{
	public function payByAlipay(Order $order, Request $request)
	{
		$this->authorize('own', $order);

		if ($order->paid_at || $order->closed) {
			throw new InvalidRequestException('Order status is incorrect');
		}

		return app('alipay')->web([
			'out_trade_no' => $order->no,
			'total_amount' => $order->total_amount,
			'subject' => 'Pay for the order :'.$order->no,
		]);
	}

	//frontend callback
	public function alipayReturn()
	{
		try {
			app('alipay')->verify();
		} catch (\Exception $e) {
			return view('pages.error', ['msg' => 'data is incorrect']);
		}

		return view('pages.success', ['msg' => 'Payment is successful!']);
	}

	public function alipayNotify()
	{
		$data = app('alipay')->verify();
		if(!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
			return app('alipay')->success();
		}
		$order = Order::where('no', $data->out_trade_no)->first();
		if (!$order) {
			return 'fail';
		}
		if ($order->paid_at) {
			return app('alipay')->success();
		}

		$order->update([
			'paid_at' => Carbon::now(),
			'payment_method' => 'alipay',
			'payment_no' => $data->trade_no,
		]);

		return app('alipay')->success();
	}
}
