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
        <div class="line">
        	<div class="line-label">Receiving Address: </div>
        	<div class="line-value">{{ join(" ", $order->address) }}</div>
        </div>
        <div class="line">
        	<div class="line-label">Order Remark:</div>
        	<div class="line-value">{{ $order->remark ?: '-' }}</div></div>
        <div class="line">
        	<div class="line-label">Order No. : </div>
        	<div class="line-value">{{ $order->no }}</div>
        </div>
        <div class="line">
        	<div class="line-label">
        		Shipping Status:
        	</div>
        	<div class="line-value">
        		{{ \App\Models\Order::$shipStatusMap[$order->ship_status]  }}
        	</div>
        	@if($order->ship_data)
			  <div class="line">
			  	<div class="line-label">
			  		Shipping Infor:
			  	</div>
			  	<div class="line-value">
			  		{{ $order->ship_data['express_company']}}
			  		  {{ $order->ship_data['express_no'] }}
			  	</div>
			  </div>
        	@endif

			@if($order->paid_at && $order->refund_status !== \App\Models\Order::REFUND_STATUS_PENDING)
				<div class="line">
					<div class="line-label">
						Refund Status:
					</div>
					<div class="line-value">
						{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
					</div>
				</div>
				<div class="line">
					<div class="line-label">
						Refund Reason:
					</div>
					<div class="line-value">
						{{ $order->extra['refund_reason'] }}
					</div>
				</div>
			@endif

        </div>
      </div>
      <div class="order-summary text-right">
		  @if($order->couponCode)
			<div class="text-primary">
			  <span>Coupon Info:</span>
				<div class="value">{{ $order->couponCode->description }}</div>
			</div>
		  @endif

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
        @if(isset($order->extra['refund_disagree_reason']))
			<div>
				<span>The reason for refusal of refund:</span>
				<div class="value">
					{{ $order->extra['refund_disagree_reason']}}
				</div>
			</div>
        @endif
		
		@if(!$order->paid_at && !$order->closed)
		<div class="payment-buttons">
		<a class="btn btn-primary btn-sm" href="{{ route('payment.alipay',['order' => $order->id]) }}">Pay via Alipay</a>
	    <a class="btn btn-primary btn-sm" id = 'btn-wechat'>Pay via Weichat</a>
			@if ($order->total_amount >= config('app.min_installment_amount'))
			<button class="btn btn-sm btn-info" id='btn-installment'>
			  By installments
			</button>
				@endif
		</div>
		@endif

		@if($order->ship_status === \App\Models\Order::SHIP_STATUS_DELIVERED)
		  <div class="receive-button">
		  	<button type="button" id="btn-receive" class="btn btn-sm btn-success">Confirm Received</button>
		  </div>
		@endif

		@if($order->type !== \App\Models\Order::TYPE_CROWDFUNDING &&
			$order->paid_at && $order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
			<div class="refund_button">
				<button class="btn btn-sm btn-danger" id="btn-apply-refund">Apply for refund</button>
			</div>
		@endif

      </div>
    </div>
  </div>
</div>
</div>
</div>

	<div class="modal fade" id="installment-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">x</span>
					</button>
					<h4>Select timing for installments </h4>
				</div>
				<div class="modal-body">
					<table class="table table-bordered table-striped text-center">
						<thead>
						<tr>
							<th class="text-center">Periods</th>
							<th class="text-center">Rates</th>
						</tr>
						</thead>
							<tbody>
							@foreach(config('app.installment_fee_rate') as $count => $rate)
								<tr>
									<td>Period {{ $count }}</td>
									<td>{{ $rate }}%</td>
									<td>
										<button class="btn btn-sm btn-primary btn-select-installment" data-count="{{ $count }}">Select</button>
									</td>
								</tr>
								@endforeach
							</tbody>
					</table>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>

					</div>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('scriptsAfterJs')
<script>
	$(document).ready(function () {

		$('#btn-wechat').click(function () {
			swal({
				content: $('<img src="{{ route('payment.wechat', ['order' => $order->id]) }}" />')[0],
				buttons: ['Close', 'Payment finished'],
			})
			.then(function (result) {
				if (result) {
					location.reload();
				}
			})
		});

		$('#btn-receive').click(function () {
			swal({
				title: 'Received the product ordered?',
				icon: 'warning',
				buttons: true,
				dangerMode: true,
				buttons: ['Cancel', 'Confirm received'],
			})
			.then(function (ret) {
				if (!ret) {
					return;
				}
				axios.post(' {{ route('orders.received', ['$order->id']) }}')
				.then(function () {
					location.reload();
				})
			});
		});

		$('#btn-apply-refund').click(function () {
			swal({
				text: 'Please input refund reason',
				content: "input"
			}).then(function (input) {
				if(!input) {
					swal('Refund reason can\'t be blank. ')
					return;
				}
				axios.post('{{ route('orders.apply_refund', [$order->id]) }}', {reason: input})
				  .then(function () {
				  	swal('Apply for refund is successful', '', 'success').then(function () {
				  		location.reload();
				  	});
				  });
			});
		});

		$('#btn-installment').click(function () {
		    $('#installment-modal').modal();
		});

		$('.btn-select-installment').click(function () {
			axios.post('{{ route('payment.installment', ['order' => $order->id ]) }}', { count: $(this).data('count')})
				.then(function (response) {
					console.log(response.data);
                })
        });
	});
</script>
@endsection

