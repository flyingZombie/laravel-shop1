@extends('layouts.app')
@section('title', $product->title)

@section('content')
<div class="row">
<div class="col-lg-10 col-lg-offset-1">
<div class="panel panel-default">
  <div class="panel-body product-info">
    <div class="row">
      <div class="col-sm-5">
        <img class="cover" src="{{ $product->image_url }}" alt="">
      </div>
      <div class="col-sm-7">
        <div class="title">{{ $product->title }}</div>
        <div class="price"><label>Price</label><em>￥</em><span>{{ $product->price }}</span></div>
        <div class="sales_and_reviews">
          <div class="sold_count">Sold count <span class="count">{{ $product->sold_count }}</span></div>
          <div class="review_count">Review count <span class="count">{{ $product->review_count }}</span></div>
          <div class="rating" title="Rating {{ $product->rating }}">Rating <span class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span></div>
        </div>
        <div class="skus">
          <label>Choose</label>
          <div class="btn-group" data-toggle="buttons">
            @foreach($product->skus as $sku)
              <label class="btn btn-default sku-btn" 
                     title="{{ $sku->description }}" 
                     data-price="{{ $sku->price }}"
                     data-stock="{{ $sku->stock }}"
                     data-toggle="tooltip"
                     data-placement="bottom">
                <input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}"> {{ $sku->title }}
              </label>
            @endforeach
          </div>
        </div>
        <div class="cart_amount"><label>Cart amount</label><input type="text" class="form-control input-sm" value="1"><span>Pieces</span><span class="stock"></span></div>
        <div class="buttons">

          @if($favored)
          <button class="btn btn-danger btn-disfavor">Disfavor</button>
          @else 
          <button class="btn btn-success btn-favor">❤ Add favor</button>
          @endif
          <button class="btn btn-primary btn-add-to-cart">Add to shopping cart</button>
        </div>
      </div>
    </div>
    <div class="product-detail">
      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-toggle="tab">Product details</a></li>
        <li role="presentation"><a href="#product-reviews-tab" aria-controls="product-reviews-tab" role="tab" data-toggle="tab">Product reviews</a></li>
      </ul>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
          {!! $product->description !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
@endsection

@section('scriptsAfterJs')
<script>
  $(document).ready(function () {

    $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
    $('.sku-btn').click(function () {
      $('.product-info .price span').text($(this).data('price'));
      $('.product-info .stock').text('Stock: ' + $(this).data('stock') + 'Piece');
    });

    $('.btn-favor').click(function () {
      axios.post('{{ route('products.favor', ['product' => $product->id ])}}')
      .then(function () {
        swal('Success', '', 'success')
        .then(function () {
          location.reload();
        });
      }, function(error) {
        if (error.response && error.response.status === 401) {
          swal('Please log in first', '', 'error');
        } else if ( error.response && error.response.data.msg) {
          swal(error.response.data.msg, '', 'error');
        } else {
          swal('System error', '', 'error');
        }
      });
    });

    $('.btn-disfavor').click(function () {
      axios.delete(' {{ route('products.disfavor', ['product' => $product->id ]) }}')
      .then(function () {
        swal('Success', '', 'success')
        .then(function () {
          location.reload();
        });
      });
    });

    $('.btn-add-to-cart').click(function () {
      axios.post('{{ route('cart.add') }}', {
        sku_id: $('label.active input[name=skus]').val(),
        amount: $('.cart_amount input').val(),
      }).then(function () {
        swal('Successfully added into shopping cart', '', 'success');
      }, function (error) {
        if (error.response.status === 401) {
          swal('Please log in first', '', 'error');
        } else if (error.response.status === 422) {
          var html = '<div>';
          _.each(error.response.data.errors, function (errors) {
            _.each(errors, function (error) {
              html += error+'<br>';
            })
          });
          html += '</div>';
          swal({content: $(html)[0], icon: 'error'})
        } else {
          swal('System error', '', 'error');
        }
      })
    });
  });

</script>
@endsection