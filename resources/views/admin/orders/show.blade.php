<div class="box box-info">
	<div class="box-header with-border">
		<h3 class="box-title">Order No.: {{ $order->no }}</h3>
    <div class="box-tools">
    	<div class="btn-group pull-right" style="margin-right: 10px">
    		<a href="{{ route('admin.orders.index') }} class="btn btn-sm btn-default" ">
    			<i class="fa fa-list"></i>
    			List
    		</a>
    	</div>
      </div>
	</div>
	<div class="box-body">
		<table class="table table-bordered">

			<tbody>
				<tr>
					<td>Buyer: </td>
					<td>{{ $order->user->name}}</td>
					<td>Paid on</td>
					<td>{{ $order->paid_at->format('Y-m-d H:i:s')}}</td>
				</tr>
				<tr>
					<td>Payment method: </td>
					<td>{{ $order->payment_method }}</td>
					<td>Payment No.:</td>
					<td>{{ $order->payment_no}}</td>
				</tr>
				<tr>
					<td>Receiving Address</td>
					<td colspan="3">{{ $order->address['address']}} 
						{{ $order->address['postcode']}}
						{{ $order->address['contact_name']}}
						{{ $order->address['contact_phone']}}
					</td>
				</tr>
				<tr>
					<td rowspan="{{ $order->items->count() + 1}}">Products List</td>
					<td>Product Name</td>
					<td>Price</td>
					<td>Quantity</td>
				</tr>
				@foreach($order->items as $item)
                <tr>
                	<td>{{ $item->product->title}} {{ $item->productSku->title}}</td>
                	<td>${{ $item->price}}</td>
                	<td>{{ $item->amount}}</td>
                </tr>
                @endforeach
                <tr>
                	<td>Order amount: </td>
                	<td colspan="3">${{ $order->total_amount}}</td>
                </tr>
			<tr>
				<td>Order Amount:</td>
				<td>${{ $order->total_amount}}</td>
				<td>Shipping Status</td>
				<td>{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</td>
			</tr>

			@if($order->ship_status === \App\Models\Order::SHIP_STATUS_PENDING)
			<tr>
				<td colspan="4">
					<form action="{{ route('admin.orders.ship', [$order->id]) }}" method="post" accept-charset="utf-8" class="form-inline">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="form-group {{ $errors->has('express_company')? 'has-error' : ''}}">
						<label for="express_company" class="control-label">
							Shipping Company
						</label>
						<input type="text" name="express_company" id="express_company" value="" class="form-control" placeholder="Input shipping company">
						@if($errors->has('express_company'))
						  @foreach ($errors->get('express_company') as $msg)
						    <span class="help-block">{{ $msg }}</span>
						  @endforeach
						@endif
					</div>
					
					<div class="form-group {{ $errors->has('express_no')? 'has-error': ''}}">
						<label for="express_no" class="control-label">
							Shipping Number
						</label>
						<input type="text" name="express_no" value="" id="express_no" class="form-control" placeholder="Input shipping number">

						@if($errors->has('express_no'))
						  @foreach ($errors->get('express_no') as $msg)
						  	<span class="help-block">{{ $msg }}</span>
						  @endforeach
						@endif
					</div>
					<button type="submit" class="btn btn-success" id="ship-btn">Ship</button>
					</form>
				</td>
			</tr>
			@else 
			<tr>
				<td>Shipping Company:</td>
				<td>{{ $order->ship_data['express_company'] }}</td>
				<td>Shipping Nubmer: </td>
				<td>{{ $order->ship_data['express_no']}}</td>
			</tr>
			@endif

			@if($order->refund_status !== \App\Models\Order::REFUND_STATUS_PENDING)
			<tr>
				<td>Refund Status</td>
				<td colspan="2">{{ \App\Models\Order::$refundStatusMap[$order->refund_status]}}, Reason: {{ $order->extra['refund_reason']}}
				</td>
				<td>
					@if($order->refund_status === \App\Models\Order::REFUND_STATUS_APPLIED)
					  <button class="btn btn-sm btn-success" id="btn-refund-agree">Agree</button>
					  <button class="btn btn-sm btn-danger" id="btn-refund-disagree">Disagree</button>
					@endif
				</td>
			</tr>
			@endif
			</tbody>
		</table>
	</div>
</div>
<script>
	$(document).ready(function () {
      $('#btn-refund-disagree').click(function () {
        swal({
        	title: 'Please input reason for refusal of refund',
        	type: 'input',
        	showCancelButton: true,
        	closeOnConfirm: false,
        	confirmButtonText: 'Confirm',
        	cancelButtonText: 'Cancel',
        }, function (inputValue) {
          if (inputValue === false) {
          	return;
          }
          if (!inputValue) {
          	swal('Reason should not be blank', '', 'error')
          	return;
          }
          $.ajax({
          	url: '{{ route('admin.orders.handle_refund', [$order->id]) }}',
          	type: 'POST',
          	data: JSON.stringify({
          		agree: false,
          		reason: inputValue,
          		_token: LA.token,
          	}),
          	contentType: 'application/json',
          	success: function (data) {
          	  swal({
          	  	title: 'Done',
          	  	type: 'success'
          	  }, function () {
                location.reload();
          	  });
          	}

          });
        });
      });
	});
</script>