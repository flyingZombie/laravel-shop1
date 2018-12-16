@extends('layouts.app')
@section('title', 'Products List')

@section('content')
<div class="row">
	<div class="col-lg-10 col-lg-offset-1">
		<div class="panel panel-default">
			<div class="panel-body">

				<div class="row">
					<form action=" {{ route('products.index') }}" class="form-inline search-form">

						<a class="all-products" href="{{ route('products.index') }}">All</a> &gt;

						@if ($category)

							@foreach($category->ancestors as $ancestor)

								<span class="category">
								  <a href="{{ route('products.index',['category_id' => $ancestor->id]) }}">
									  {{ $ancestor->name }}
								  </a>
								</span>
								<span>></span>
							@endforeach
							<span class="category">{{ $category->name }}</span><span> ></span>
								<input type="hidden" name="category_id" value="{{ $category->id }}">

						@endif

					<input type="text" name="search" class="form-control input-sm" placeholder="Search">
					<button class="btn btn-primary btn-sm">Search</button>
					
					<select name="order" class="form-control input-sm pull-right">
						<option value="">Order By</option>
						<option value="price_asc">Price from low to high</option>
						<option value="price_desc">Price from high to low</option>
						<option value="sold_count_desc">Sold count from high to low</option>
						<option value="sold_count_asc">Sold count from low to high</option>
						<option value="rating_desc">Rating from high to low</option>
						<option value="rating_asc">Rating from low to high</option>
					</select>
					</form>
				</div>

				<div class="filters">
					@if ($category && $category->is_directory)
					  <div class="row">
						  <div class="col-xs-3 filter-key">Sub-categories</div>
						  <div class="col-xs-9 filter-values">
							  @foreach($category->children as $child)
								<a href="{{ route('products.index', ['category_id' => $child->id]) }}">
									{{ $child->name }}
								</a>
							  @endforeach
						  </div>
					  </div>
					@endif
				</div>

				<div class="row products-list">
					@foreach ($products as $product)
						<div class="col-xs-3 product-item">
							<div class="product-content">
								<div class="top">
									<div class="img">
			

									<a href="{{ route('products.show', ['product' => $product->id]) }}">
                					  <img src="{{ $product->image_url }}" alt="">
              						</a>
									</div>
									<div class="price">
										<b>$</b>{{ $product->price }} 
									</div>
									<div class="title">
										<a href="{{ route('products.show', ['product' => $product->id]) }}">{{ $product->title }}</a>
									</div>
								</div>
								<div class="bottom">
									<div class="sold_count">Sold Count
										<span>{{ $product->sold_count }}</span>
									</div>
									<div class="review_count">Review Count
										<span>{{ $product->review_count }}</span>
									</div>
								</div>
							</div>
						</div>
					@endforeach
				</div>
				<div class="pull-right">
					{{ $products->appends($filters)->render() }}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scriptsAfterJs')
<script>
  var filters = {!! json_encode($filters) !!};
  $(document).ready(function () {
  	$('.search-form input[name=search]').val(filters.search);
  	$('.search-form select[name=order]').val(filters.order);

  	$('.search-form select[name=order]').on('change', function() {
  		$('.search-form').submit();
  	})
  })
</script>
@endsection