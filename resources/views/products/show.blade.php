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

        @if($product->type === \App\Models\Product::TYPE_CROWDFUNDING)

          <div class="crowdfunding-info">
            <div class="have-text">
              Enough funded!
            </div>
            <div class="total-amount">
              <span class="symbol">
                $
              </span>
              {{ $product->crowdfunding->total_amount }}
            </div>
            <div class="progress">
              <div class="progress-bar progress-bar-success progress-bar-striped"
                role="progressbar"
                aria-valuenow="{{ $product->crowdfunding->percent }}"
                  aria-valuemin="0"
                   aria-valuemax="100"
                   style="min-width: 1em; width: {{ min($product->crowdfunding->percent, 100 ) }}%">
              </div>
            </div>
            <div class="progress-info">
              <span class="current-progress">
                Current progress: {{ $product->crowdfunding->percent }}%
              </span>
              <span class="pull-right user-count">
                {{ $product->crowdfunding->user_count }} Supporters
              </span>
            </div>

            @if ($product->crowdfunding->status === \App\Models\CrowdfundingProduct::STATUS_FUNDING)

              <div>This project must get
                <span class="text-red"> $
                  {{ $product->crowdfunding->target_amount }}
                </span> before
                <span class="text-red">
                  {{ $product->crowdfunding->end_at->format('Y-m-d H:i:s') }}
                </span>
                The funding will be ended by <span class="text-red">{{ $product->crowdfunding->end_at->diffForHumans(now()) }}</span>
              </div>
            @endif
          </div>
          @else

        <div class="price"><label>Price</label><em>$</em><span>{{ $product->price }}</span></div>
        <div class="sales_and_reviews">
          <div class="sold_count">Sold count <span class="count">{{ $product->sold_count }}</span></div>
          <div class="review_count">Review count <span class="count">{{ $product->review_count }}</span></div>
          <div class="rating" title="Rating {{ $product->rating }}">Rating <span class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span></div>
        </div>
      </div>
      @endif

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

          @if($product->type === \App\Models\Product::TYPE_CROWDFUNDING)
            @if(Auth::check())
              @if($product->crowdfunding->status === \App\Models\CrowdfundingProduct::STATUS_FUNDING)
                <button class="btn btn-primary btn-crowdfunding">Join</button>
              @else
                <button class="btn btn-primary disabled">
                {{ \App\Models\CrowdfundingProduct::$statusMap[$product->crowdfunding->status] }}
                </button>
              @endif
              @else
                <a class="btn btn-primary" href="{{ route('login') }}">
                  Please log in first
                </a>
              @endif
            @else
              <button class="btn btn-primary btn-add-to-cart">Add to shopping cart</button>
            @endif
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
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <td>User</td>
                <td>Product</td>
                <td>Rating</td>
                <td>Review</td>
                <td>Time</td>
              </tr>
            </thead>
            <tbody>
              @foreach ($reviews as $review)
                <tr>
                <td>{{ $review->order->user->name }}</td>
                <td>{{ $review->productSku->title }}</td>
                <td>
                  {{ str_repeat('⭐️', $review->rating )}} 
                  {{ str_repeat('☆', 5 - $review->rating ) }}
                </td>
                <td>{{ $review->review }}</td>
                <td>{{ $review->reviewed_at->format('Y-m-d H:i') }}</td>

              </tr>
              @endforeach
              
            </tbody>
          </table>
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
        swal('Successfully added into shopping cart', '', 'success')
        .then(function () {
          location.href = '{{ route('cart.index') }}'
        });
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

    $('.btn-crowdfunding').click(function () {

        if (!$('label.active input[name=skus]').val()) {
            swal('Please select product first');
            return;
        }

        var addresses = {!! json_encode(Auth::check() ? Auth::user()->addresses : []) !!};

        var $form = $('<form class="form-horizontal" role="form"></form>');

        $form.append('<div class="form-group">' +
            '<label class="control-label col-sm-3">Select Address</label>' +
            '<div class="col-sm-9">' +
            '<select class="form-control" name="address_id"></select>' +
            '</div></div>');
        addresses.forEach(function (address) {
            $form.find('select[name=address_id]')
                .append("<option value='" + address.id + "'>" +
                    address.full_address + ' ' + address.contact_name + ' ' + address.contact_phone +
                    '</option>');
        });
        $form.append('<div class="form-group">' +
            '<label class="control-label col-sm-3">Amount</label>' +
            '<div class="col-sm-9"><input class="form-control" name="amount">' +
            '</div></div>');

        swal({
            text: 'Join',
            content: $form[0],
            buttons: ['Cancel', 'Yes']
        }).then(function (ret) {

            if (!ret) {
                return;
            }

            var req = {
                address_id: $form.find('select[name=address_id]').val(),
                amount: $form.find('input[name=amount]').val(),
                sku_id: $('label.active input[name=skus]').val()
            };
            axios.post('{{ route('crowdfunding_orders.store') }}', req)
                .then(function (response) {

                    swal('Submitted', '', 'success')
                        .then(() => {
                            location.href = '/orders/' + response.data.id;
                        });
                }, function (error) {
                    if (error.response.status === 422) {
                        var html = '<div>';
                        _.each(error.response.data.errors, function (errors) {
                            _.each(errors, function (error) {
                                html += error+'<br>';
                            })
                        });
                        html += '</div>';
                        swal({content: $(html)[0], icon: 'error'})
                    } else if (error.response.status === 403) {
                        swal(error.response.data.msg, '', 'error');
                    } else {
                        swal('System error', '', 'error');
                    }
                });
        });
    });

  });
</script>
@endsection