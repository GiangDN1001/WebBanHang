@extends('layouts.app')
@section('title', 'Giỏ hàng')
@section('content')
<style>
  .test-danger{
    color: red;
  }

  .test-success{
    color: green;
  }
</style>
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
      <h2  style="font-family: 'Roboto'" class="page-title">Giỏ hàng</h2>
      <div class="checkout-steps">
        <a href="javascript:void(0)" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">01</span>
          <span class="checkout-steps__item-title">
            <span>Túi sản phẩm</span>
            <em>Quản lý danh sách sản phẩm của bạn</em>
          </span>
        </a>
        <a href="javascript:void(0)" class="checkout-steps__item">
          <span class="checkout-steps__item-number">02</span>
          <span class="checkout-steps__item-title">
            <span>Vận chuyển và thanh toán</span>
            <em>Kiểm tra danh sách sản phẩm của bạn</em>
          </span>
        </a>
        <a href="javascript:void(0)" class="checkout-steps__item">
          <span class="checkout-steps__item-number">03</span>
          <span class="checkout-steps__item-title">
            <span>Thông tin giao hàng</span>
            <em>Xem lại và gửi hàng</em>
          </span>
        </a>
      </div>
      <div class="shopping-cart">
        @if($items->count() > 0)
        <div class="cart-table__wrapper">
          <table class="cart-table">
            <thead>
              <tr>
                <th>Sản phẩm</th>
                <th></th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Tổng</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($items as $item)
                @php
                  $product = \App\Models\Product::find($item->options->product_id ?? null);
                @endphp
                <tr>
                  <td>
                    <div class="shopping-cart__product-item">
                      @if($product && $product->image)
                        <img loading="lazy" src="{{ asset('uploads/products/thumbnails/' . $product->image) }}" width="120" height="120" alt="{{ $item->name }}" />
                      @else
                        <img loading="lazy" src="{{ asset('images/no-image.jpg') }}" width="120" height="120" alt="No Image Available" />
                      @endif
                    </div>
                  </td>
                  <td>
                    <div class="shopping-cart__product-item__detail">
                      <h4 style="font-family: 'Roboto'">
                        {{ $item->options->variant_title ?? $item->name }}
                      </h4>
                      <ul class="shopping-cart__product-item__options">
                        {{-- Thông tin biến thể thêm nếu cần --}}
                      </ul>
                    </div>
                  </td>
                  <td>
                    <span class="shopping-cart__product-price">{{ number_format($item->price, 0, '.', '.') }}₫</span>
                  </td>
                  <td>
                    <div class="qty-control position-relative">
                      <input type="number" name="quantity" value="{{ $item->qty }}" min="1" class="qty-control__number text-center">
                      <form method="POST" action="{{ route('cart.qty.decrease', ['rowId' => $item->rowId]) }}">
                        @csrf
                        @method('PUT')
                        <div class="qty-control__reduce">-</div>
                      </form>
                      <form method="POST" action="{{ route('cart.qty.increase', ['rowId' => $item->rowId]) }}">
                        @csrf
                        @method('PUT')
                        <div class="qty-control__increase">+</div>
                      </form>
                    </div>
                  </td>
                  <td>
                    <span class="shopping-cart__subtotal">
                      {{ rtrim(rtrim($item->subTotal(), '0'), '.') }}₫
                    </span>
                  </td>
                  <td>
                    <form method="POST" action="{{ route('cart.item.remove', ['rowId' => $item->rowId]) }}">
                      @csrf
                      @method('DELETE')
                      <a href="javascript:void(0)" class="remove-cart">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="#767676" xmlns="http://www.w3.org/2000/svg">
                          <path d="M0.259435 8.85506L9.11449 0L10 0.885506L1.14494 9.74056L0.259435 8.85506Z" />
                          <path d="M0.885506 0.0889838L9.74057 8.94404L8.85506 9.82955L0 0.97449L0.885506 0.0889838Z" />
                        </svg>
                      </a>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>

          
          <div class="cart-table-footer">
              @if(!Session::has("coupon"))                       
                  <form class="position-relative bg-body" method="POST" action="{{route('cart.coupon.apply')}}">
                      @csrf                        
                      <input class="form-control" type="text" name="coupon_code" placeholder="Mã giảm giá">
                      <input style="background-color: #000; color: #fff" class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4" type="submit" value="Áp dụng">                                                        
                  </form>
              @else
                <form class="position-relative bg-body" method="POST" action="{{route('cart.coupon.remove')}}">
                  @csrf     
                  @method('DELETE')                   
                  <input class="form-control" type="text" name="coupon_code" placeholder="Coupon Code" value="{{session()->get('coupon')['code']}} Applied!" readonly>
                  <input class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4" type="submit" value="BỎ MÃ GIẢM GIÁ">                            
                </form> 
              @endif
              <form class="position-relative bg-body" method="POST" action="{{route('cart.empty')}}">
                @csrf
                @method('DELETE')
                <button class="btn btn-light" type="submit">DỌN GIỎ HÀNG</button>
              </form>
          </div>
          <div> 
            @if(Session::has('success'))
              <p class="test-success">{{ Session::get('success') }}</p>
              @elseif(Session::has('error'))
              <p class="test-danger">{{ Session::get('error') }}</p>
            @endif
          </div>
        </div>
        <div class="shopping-cart__totals-wrapper">
          <div class="sticky-content">
            <div class="shopping-cart__totals">
              <h3  style="font-family: 'Roboto'">Tổng giỏ hàng</h3>
                @if(Session::has('discounts'))
                  <table class="cart-totals">
                    <tbody>
                      <tr>
                        <th>Tạm tính</th>
                        <td>{{ number_format(floatval(str_replace(',', '', Cart::instance('cart')->subtotal())), 0, '.', '.') }}₫</td>
                      </tr> 
                      <tr>
                        <th>Giảm giá {{ Session('coupon')['code'] }}</th>
                        <td>-{{ number_format(floatval(str_replace(',', '', Session('discounts')['discount'])), 0, '.', '.') }}₫</td>
                      </tr> 
                      <tr>
                        <th>Tạm tính sau giảm giá</th>
                        <td>{{ number_format(floatval(str_replace(',', '', Session('discounts')['subtotal'])), 0, '.', '.') }}₫</td>
                      </tr>    
                      <tr>
                        <th>Phí giao hàng</th>
                        <td>Miễn phí</td>
                      </tr>                            
                      <tr class="cart-total">
                        <th>Tổng</th>
                        <td>{{ number_format(floatval(str_replace(',', '', Session('discounts')['total'])), 0, '.', '.') }}₫</td>
                      </tr>

                    </tbody>
                  </table>
                @else
                  <table class="cart-totals">
                      <tbody>
                          <tr>
                            <th>Tạm tính</th>
                            <td>{{Cart::instance('cart')->subtotal()}} ₫</td>
                          </tr>   
                          <tr>
                            <th>Phí giao hàng</th>
                            <td class="">Miễn phí</td>
                          </tr>                             
                          {{-- <tr>
                              <th>VAT</th>
                              <td>{{Cart::instance('cart')->tax()}} ₫</td>
                          </tr> --}}
                          <tr class="cart-total">
                            <th>Tổng</th>
                            <td>{{Cart::instance('cart')->total()}} ₫</td>
                          </tr>
                      </tbody>
                  </table>
                @endif
            </div>
            <div class="mobile_fixed-btn_wrapper">
              <div class="button-wrapper container">
                <a href="{{ route('cart.checkout') }}" class="btn btn-primary btn-checkout">TIẾN HÀNH THANH TOÁN</a>
              </div>
            </div>
          </div>
        </div>
        @else
            <div class="row">
                <div class="col-md-12 text-center pt-5 bp-5">
                    <p>Không có sản phẩm nào trong giỏ hàng</p>
                    <a href="{{ route('shop.index') }}" class="btn btn-info">Mua sắm ngay</a>
                </div>
            </div>
        @endif
      </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
  $(function() {
    $(".qty-control__increase").on("click", function() {
      $(this).closest('form ').submit();
    });

    $(".qty-control__reduce").on("click", function() {
      $(this).closest('form ').submit();
    });
  })


  $('.remove-cart').on('click', function() {
    $(this).closest('form').submit();
  })
</script>
@endpush 