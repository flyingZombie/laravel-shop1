@extends('layouts.app')
@section('title', 'View Order')

@section('content')
<div class="row">
	<div class="col-lg-10 col-lg-offset-1">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4>Order Details</h4>
			</div>
			<div class="panel-body">
				<table class="table">
					<thead>
						<tr>
							<th>Product Information</th>
							<th class="text-center">Price</th>
							<th class="text-center">Quantity</th>
							<th class="text-right item-amount">Item Amount</th>
						</tr>
					</thead>

					@foreach($order->items as $index => $item)
					<tr>
						<td class="product-info">
							<div class="preview">
								<a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">
									<img src="{{ $item->product->image_url }}" >
								</a>
							</div>
							<div>
								<span class="product-title">
									<a href="{{ route('products.show', ['$item->product_id']) }}">{{ $item->product->title }}</a>
								</span>
								<span class="sku-title">{{ $item->productSku->title }}</span>
							</div>
						</td>
						<td class="sku-price text-center vertical-middle">$ {{ $item->price }}</td>
						<td class="sku-amount text-center vertical-middle">{{ $item->amount }}</td>
						<td class="item-amount text-right vertical-middle">${{ number_format($item->price * $item->amount, 2, '.', '') }}</td>
					</tr>
					@endforeach
					<tr><td colspan="4"></td></tr>
				</table>
	
	<div class="order-bottom">
      <div class="order-info">
        <div class="line"><div class="line-label">Receiving Address: </div><div class="line-value">{{ join(' ', $order->address) }}</div></div>
        <div class="line"><div class="line-label">Order Remark:</div><div class="line-value">{{ $order->remark ?: '-' }}</div></div>
        <div class="line"><div class="line-label">Order No.</div><div class="line-value">{{ $order->no }}</div></div>
      </div>
      <div class="order-summary text-right">
        <div class="total-amount">
          <span>Order Total Amout：</span>
          <div class="value">${{ $order->total_amount }}</div>
        </div>

        <div>
          <span>Order Status：</span>
          <div class="value">
            @if($order->paid_at)
              @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                Paid
              @else
                {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
              @endif
            @elseif($order->closed)
              Closed
            @else
              Unpaid
            @endif
          </div>
        </div>
		
		@if(!$order->paid_at && !$order->closed)
		<div class="payment-buttons">
			<a class="btn btn-primary btn-sm" href="{{ route('payment.alipay',['order' => $order->id]) }}">Pay via Alipay</a>
		</div>
		@endif
      </div>
    </div>
  </div>
</div>
</div>
</div>
@endsection