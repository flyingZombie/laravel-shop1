@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
	
<div class="row">

	<div class="col-lg-10 col-lg-offset-1" >
		<div class="panel panel-default">
			<div class="panel-heading">
				My shopping cart
			</div>
			<div class="panel-body">
				<table class="table table-striped">
					<thead>
						<tr>
							<th><input type="checkbox" id="select-all"></th>
							<th>Product information</th>
							<th>Price</th>
							<th>Quantity</th>
							<th>Operation</th>
						</tr>
					</thead>
					
					<tbody class="product_list">
						@foreach ($cartItems as $item)
							<tr data-id="{{ $item->productSku->id }}">
							<td>
								<input type="checkbox" name="select" value="{{ $item->productSku->id }}" {{ $item->productSku->product->on_sale ? 'checked' : 'disabled'}}>
							</td>
                            
                            <td class="product_info">
                            	<div class="preview">
                            		<a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">
                            		<img src="{{ $item->productSku->product->image_url}}">
                            	</a>
                            	</div>
                            	<div @if(!$item->productSku->product->on_sale) class="not_on_sale" @endif>
                            		<span class="product_title">
                            			<a target="_blank" href="{{ route('products.show',[$item->productSku->product_id]) }}">
                            				{{ $item->productsku->product->title }}
                            			</a>
                            		</span>
                            		<span class="sku_title"> 
                            			{{ $item->productSku->title }}
                            		</span>
                            		@if (!$item->productSku->product->on_sale)
                            			<span class="warning">This product is not for sale</span>
                            		@endif
                            	</div>
                            </td>
                            <td><span class="price">${{ $item->productSku->price}}</span></td>
                            <td>
                            	<input type="text" class="form-control input-sm amount" @if(!$item->productSku->product->on_sale) disabled @endif name="amount" value="{{ $item->amount }}">
                            </td>
							<td>
								<button class="btn btn-xs btn-danger btn-remove">Remove</button>
							</td>
						    </tr>
						@endforeach
					</tbody>

				</table>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scriptsAfterJs')
<script>
  $(document).ready(function () {

  	$('.btn-remove').click(function () {
  		var id = $(this).closest('tr').data('id');
  		swal({
  			title: "Are you sure to remove this product?",
  			icon: "warning",
  			buttons: ['Cancel', 'Yes'],
  			dangerMode: true,
  		})
  		.then(function (willDelete) {
  			if (!willDelete) {
  				return;
  			}
  			axios.delete('/cart/'+ id).then(function () {
  				location.reload();
  			})
  		});
  	});

  	$('#select-all').change(function () {
  		
  		var checked = $(this).prop('checked');
  		$('input[name=select][type=checkbox]:not([disabled])').each(function () {
  			$(this).prop('checked', checked);
  		});
  	});
  });
</script>
@endsection