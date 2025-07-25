@extends('layouts.app')
@section('content')
<style>
  .filled-heart {
    color: orange;
  }
  .variant-btn.active {
    background-color: #000000;
    color: white;
  }
  .thumb-slider {
    margin-top: 15px;
  }

  .thumb-slider .swiper-slide {
    width: 104px !important;
    height: 104px !important;
    cursor: pointer;
    border: 1px solid #ddd;
    border-radius: 5px;
    overflow: hidden;
    margin-right: 10px;
  }

  .thumb-slider .swiper-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .thumb-slider .swiper-slide-thumb-active {
    border: 2px solid #000;
  }

</style>
<main class="pt-90">
    <div class="mb-md-1 pb-md-3"></div>
    <section class="product-single container">
      <div class="row">
        <div class="col-lg-7">
          <div class="product-single__media" data-media-type="vertical-thumbnail">
            <div class="swiper-container main-slider">
              <div class="swiper-wrapper">
                <div class="swiper-slide product-single__image-item">
                  <img loading="lazy" class="h-auto" src="{{ asset('Uploads/products') }}/{{ $product->image }}" width="674" height="674" alt="" />
                  <a data-fancybox="gallery" href="{{ asset('Uploads/products') }}/{{ $product->image }}" data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_zoom" />
                    </svg>
                  </a>
                </div>
                @foreach(explode(",", $product->images) as $gimg)
                  <div class="swiper-slide product-single__image-item">
                    <img loading="lazy" class="h-auto" src="{{ asset('uploads/products/') }}/{{ $gimg }}" width="674" height="674" alt="" />
                    <a data-fancybox="gallery" href="{{ asset('uploads/products/') }}/{{ $gimg }}" data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
                      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_zoom" />
                      </svg>
                    </a>
                  </div>
                @endforeach
              </div>
              <div class="swiper-button-prev">
                <svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_prev_sm" />
                </svg>
              </div>
              <div class="swiper-button-next">
                <svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_next_sm" />
                </svg>
              </div>
            </div>
            <div class="product-single__thumbnail">
              <div class="swiper-container thumb-slider">
                <div class="swiper-wrapper">
                  <div class="swiper-slide product-single__image-item">
                    <img loading="lazy" class="h-auto" src="{{ asset('uploads/products/thumbnails') }}/{{ $product->image }}" width="104" height="104" alt="" />
                  </div>
                  @foreach(explode(",", $product->images) as $gimg)
                    <div class="swiper-slide product-single__image-item">
                      <img loading="lazy" class="h-auto" src="{{ asset('uploads/products/thumbnails') }}/{{ $gimg }}" width="104" height="104" alt="" />
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="d-flex justify-content-between mb-4 pb-md-2">
            <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
              <a style="font-family: 'Roboto'" href="{{ route('home.index') }}" class="menu-link menu-link_us-s text-uppercase fw-medium">Trang chủ</a>
              <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
              <a style="font-family: 'Roboto'" href="{{ route('shop.index') }}" class="menu-link menu-link_us-s text-uppercase fw-medium">Cửa hàng</a>
              <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
              <a style="font-family: 'Roboto'" href="#" class="menu-link menu-link_us-s text-uppercase fw-medium">Chi tiết sản phẩm</a>
            </div>
          </div>
          <h2 id="product-name">
            <span style="font-family: Roboto" id="default-product-name">{{ $product->name }}</span>
            <span style="font-family: Roboto" id="variant-product-title"></span>
          </h2>
          <div class="product-single__price">
            <span class="current-price" id="product-price">
              @if($product->sale_price > 0 && $product->sale_price < $product->regular_price)
                <s id="original-price">{{ number_format($product->regular_price, 0, ',', '.') }}₫</s>
                <span style="color: red" id="sale-price">{{ number_format($product->sale_price, 0, ',', '.') }}₫</span>
              @else
                <span id="regular-price">{{ number_format($product->regular_price, 0, ',', '.') }}₫</span>
              @endif
            </span>
          </div>
          <input type="hidden" id="selected-variant-id" name="variant_id" value="">
          @if($product->variants->count() > 0)
            <div class="mb-3">
              <label><strong>Chọn biến thể:</strong></label>
              <div id="variant-buttons" class="btn-group d-flex flex-wrap" role="group">
                @foreach($product->variants as $variant)
                  <button 
                    type="button" 
                    class="btn btn-outline-primary m-1 variant-btn" 
                    data-variant-id="{{ $variant->id }}"
                    {{-- data-name="{{ $variant->variant_name }}" --}}
                    data-variant-title="{{ $variant->variant_title }}"
                    data-price="{{ $variant->sale_price > 0 ? $variant->sale_price : $variant->regular_price }}"
                    data-variant-title="{{ $variant->variant_name }}">
                    {{ $variant->variant_name }}
                  </button>
                @endforeach
              </div>
            </div>
          @endif

          <div class="product-single__short-desc">
            {!! $product->short_description !!}
          </div>
          @if(Cart::instance('cart')->content()->where('id', $product->id)->count() > 0)
            <a style="font-family: 'Roboto'" href="{{ route('cart.index') }}" class="btn btn-warning mb-3">Tới giỏ hàng</a>
          @else
            <form name="addtocart-form" method="POST" action="{{ route('cart.add') }}">
              @csrf
              <div class="product-single__addtocart">
                <div class="qty-control position-relative">
                  <input type="number" name="quantity" value="1" min="1" class="qty-control__number text-center">
                  <div class="qty-control__reduce">-</div>
                  <div class="qty-control__increase">+</div>
                </div>
                <input type="hidden" name="product_id" value="{{ $product->id }}" />
                <input type="hidden" name="name" id="form-product-name" value="{{ $product->name }}" />
                <input type="hidden" name="variant_id" id="form-variant-id" value="">
                <input type="hidden" name="variant_name" id="form-variant-name" value="{{ $product->variant_name }}">
                <input type="hidden" name="price" id="form-price" value="{{ $product->sale_price > 0 ? $product->sale_price : $product->regular_price }}">
                <button style="font-family: 'Roboto'" type="submit" class="btn btn-primary">Thêm vào giỏ hàng</button>
              </div>
            </form>
          @endif

          <div class="product-single__addtolinks">
            @if(Cart::instance('wishlist')->content()->where('id', $product->id)->count() > 0)
              <form method="POST" action="{{ route('wishlist.remove', ['rowId' => Cart::instance('wishlist')->content()->where('id', $product->id)->first()->rowId]) }}">
                @csrf
                @method('DELETE')
                <a href="javascript:void(0)" class="menu-link menu-link_us-s add-to-wishlist filled-heart" onclick="this.closest('form').submit()">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_heart" />
                  </svg>
                  <span>Xóa khỏi danh sách mong muốn</span>
                </a>
              </form>
            @else
              <form method="POST" action="{{ route('wishlist.add') }}" id="wishlist-form-{{ $product->id }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}" />
                <input type="hidden" name="name" value="{{ $product->name }}" />
                <input type="hidden" name="quantity" value="1"/>
                <input type="hidden" name="price" value="{{ $product->sale_price > 0 ? $product->sale_price : $product->regular_price }}" />
                <a href="javascript:void(0)" class="menu-link menu-link_us-s add-to-wishlist" onclick="document.getElementById('wishlist-form-{{ $product->id }}').submit()">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_heart" />  
                  </svg>
                  <span>Thêm vào danh sách mong muốn</span>
                </a>
              </form>
            @endif
            <share-button class="share-button">
              <button class="menu-link menu-link_us-s to-share border-0 bg-transparent d-flex align-items-center">
                <svg width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_sharing" />
                </svg>
                <span>Chia sẻ</span>
              </button>
              <details id="Details-share-template__main" class="m-1 xl:m-1.5" hidden="">
                <summary class="btn-solid m-1 xl:m-1.5 pt-3.5 pb-3 px-5">+</summary>
                <div id="Article-share-template__main" class="share-button__fallback flex items-center absolute top-full left-0 w-full px-2 py-4 bg-container shadow-theme border-t z-10">
                  <div class="field grow mr-4">
                    <label class="field__label sr-only" for="url">Link</label>
                    <input type="text" class="field__input w-full" id="url" value="{{ url()->current() }}" placeholder="Link" onclick="this.select();" readonly="">
                  </div>
                  <button class="share-button__copy no-js-hidden">
                    <svg class="icon icon-clipboard inline-block mr-1" width="11" height="13" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" viewBox="0 0 11 13">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M2 1a1 1 0 011-1h7a1 1 0 011 1v9a1 1 0 01-1 1V1H2zM1 2a1 1 0 00-1 1v9a1 1 0 001 1h7a1 1 0 001-1V3a1 1 0 00-1-1H1zm0 10V3h7v9H1z" fill="currentColor"></path>
                    </svg>
                    <span class="sr-only">Sao chép liên kết</span>
                  </button>
                </div>
              </details>
            </share-button>
          </div>
          <div class="product-single__meta-info">
            <div class="meta-item">
              <label>SKU:</label>
              <span>{{ $product->SKU }}</span>
            </div>
            <div class="meta-item">
              <label>Danh mục:</label>
              <span>{{ $product->category->name }}</span>
            </div>
            <div class="meta-item">
              <label>Thẻ:</label>
              <span>N/A</span>
            </div>
          </div>
        </div>
      </div>
      <div class="product-single__details-tab">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link nav-link_underscore active" id="tab-description-tab" data-bs-toggle="tab" href="#tab-description" role="tab" aria-controls="tab-description" aria-selected="true">Mô tả</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link nav-link_underscore" id="tab-additional-info-tab" data-bs-toggle="tab" href="#tab-additional-info" role="tab" aria-controls="tab-additional-info" aria-selected="false">Thông tin bổ sung</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link nav-link_underscore" id="tab-reviews-tab" data-bs-toggle="tab" href="#tab-reviews" role="tab" aria-controls="tab-reviews" aria-selected="false">Đánh giá</a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade show active" id="tab-description" role="tabpanel" aria-labelledby="tab-description-tab">
            <div class="product-single__description">
              {!! $product->description !!}
            </div>
          </div>
          <div class="tab-pane fade" id="tab-additional-info" role="tabpanel" aria-labelledby="tab-additional-info-tab">
            <div class="product-single__addtional-info">
              <div class="item">
                {!! $product->short_description !!}
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="tab-reviews" role="tabpanel" aria-labelledby="tab-reviews-tab">
            <h2 class="product-single__reviews-title">Đánh giá</h2>
            <div class="product-single__reviews-list">
              <div class="product-single__reviews-item">
                <div class="customer-avatar">
                  <img loading="lazy" src="{{ asset('assets/images/avatar.jpg') }}" alt="" />
                </div>
                <div class="customer-review">
                  <div class="customer-name">
                    <h6>Janice Miller</h6>
                    <div class="reviews-group d-flex">
                      <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_star" />
                      </svg>
                      <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_star" />
                      </svg>
                      <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_star" />
                      </svg>
                      <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_star" />
                      </svg>
                      <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_star" />
                      </svg>
                    </div>
                  </div>
                  <div class="review-date">Ngày 6 tháng 4, 2023</div>
                  <div class="review-text">
                    <p>Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est…</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="products-carousel container">
      <h2 style="font-family: 'Roboto'" class="h3 text-uppercase mb-4 pb-xl-2 mb-xl-4">Sản phẩm liên quan</h2>
      <div id="related_products" class="position-relative">
        <div class="swiper-container js-swiper-slider" data-settings='{
            "autoplay": false,
            "slidesPerView": 4,
            "slidesPerGroup": 4,
            "effect": "none",
            "loop": true,
            "pagination": {
              "el": "#related_products .products-pagination",
              "type": "bullets",
              "clickable": true
            },
            "navigation": {
              "nextEl": "#related_products .products-carousel__next",
              "prevEl": "#related_products .products-carousel__prev"
            },
            "breakpoints": {
              "320": {
                "slidesPerView": 2,
                "slidesPerGroup": 2,
                "spaceBetween": 14
              },
              "768": {
                "slidesPerView": 3,
                "slidesPerGroup": 3,
                "spaceBetween": 24
              },
              "992": {
                "slidesPerView": 4,
                "slidesPerGroup": 4,
                "spaceBetween": 30
              }
            }
          }'>
          <div class="swiper-wrapper">
            @foreach($rproducts as $rproduct)
              <div class="swiper-slide product-card">
                <div class="pc__img-wrapper position-relative">
                  <a href="{{ route('shop.product.details', ['product_slug' => $rproduct->slug]) }}">
                    <img loading="lazy" src="{{ asset('Uploads/products') }}/{{ $rproduct->image }}" width="330" height="400" alt="{{ $rproduct->name }}" class="pc__img">
                    @php
                      $galleryImages = explode(",", $rproduct->images);
                    @endphp
                    @if(isset($galleryImages[0]) && $galleryImages[0] != '')
                      <img loading="lazy" src="{{ asset('Uploads/products') }}/{{ $galleryImages[0] }}" width="330" height="400" alt="{{ $rproduct->name }}" class="pc__img pc__img-second">
                    @endif
                  </a>
                  @if(Cart::instance('cart')->content()->where('id', $rproduct->id)->count() > 0)
                    <a href="{{ route('cart.index') }}" class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium btn-warning mb-3">Đến giỏ hàng</a>
                  @else
                    <form action="{{ route('cart.add') }}" method="POST">
                      @csrf
                      <input type="hidden" name="id" value="{{ $rproduct->id }}">
                      <input type="hidden" name="name" value="{{ $rproduct->name }}">
                      <input type="hidden" name="quantity" value="1" min="1">
                      <input type="hidden" name="price" value="{{ $rproduct->sale_price > 0 ? $rproduct->sale_price : $rproduct->regular_price }}">
                      @if($rproduct->variants->count() > 0)
                        <select name="variant_id" class="form-control mb-2">
                          <option value="">Chọn biến thể</option>
                          @foreach($rproduct->variants as $variant)
                            <option value="{{ $variant->id }}" data-price="{{ $variant->sale_price > 0 ? $variant->sale_price : $variant->regular_price }}">{{ $variant->variant_name }}</option>
                          @endforeach
                        </select>
                      @endif
                      <button type="submit" class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium btn-primary mb-3">Thêm vào giỏ hàng</button>
                    </form>
                  @endif
                </div>
                <div class="pc__info position-relative">
                  <p style="font-family: 'Roboto'" class="pc__category">{{ $rproduct->category->name }}</p>
                  <h6 style="font-family: 'Roboto'" class="pc__title">
                    <a href="{{ route('shop.product.details', ['product_slug' => $rproduct->slug]) }}">{{ $rproduct->name }}</a>
                  </h6>
                  <div class="product-card__price d-flex">
                    <span class="money price">
                      @if($rproduct->sale_price > 0 && $rproduct->sale_price < $rproduct->regular_price)
                        <s>{{ number_format($rproduct->regular_price, 0, '.', '.') }}₫</s>
                        <span style="color: red">{{ number_format($rproduct->sale_price, 0, '.', '.') }}₫</span>
                      @else
                        {{ number_format($rproduct->regular_price, 0, '.', '.') }}₫
                      @endif
                    </span>
                  </div>
                  @php
                    $wishlistItem = Cart::instance('wishlist')->content()->where('id', $rproduct->id)->first();
                  @endphp
                  @if($wishlistItem)
                    <form method="POST" action="{{ route('wishlist.remove', ['rowId' => $wishlistItem->rowId]) }}">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 filled-heart" title="Xóa khỏi danh sách mong muốn">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <use href="#icon_heart" />
                        </svg>
                      </button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('wishlist.add') }}">
                      @csrf
                      <input type="hidden" name="id" value="{{ $rproduct->id }}" />
                      <input type="hidden" name="name" value="{{ $rproduct->name }}" />
                      <input type="hidden" name="quantity" value="1" />
                      <input type="hidden" name="price" value="{{ $rproduct->sale_price > 0 ? $rproduct->sale_price : $rproduct->regular_price }}" />
                      <button type="submit" class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0" title="Thêm vào danh sách mong muốn">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <use href="#icon_heart" />
                        </svg>
                      </button>
                    </form>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </div>
        <div class="products-carousel__prev position-absolute top-50 d-flex align-items-center justify-content-center">
          <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
            <use href="#icon_prev_md" />
          </svg>
        </div>
        <div class="products-carousel__next position-absolute top-50 d-flex align-items-center justify-content-center">
          <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
            <use href="#icon_next_md" />
          </svg>
        </div>
        <div class="products-pagination mt-4 mb-5 d-flex align-items-center justify-content-center"></div>
      </div>
    </section>
</main>
@endsection

@push('scripts')
  {{-- Nhúng thư viện Swiper nếu chưa có --}}
  <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Swiper cho ảnh thumbnail
    const thumbSwiper = new Swiper(".thumb-slider", {
      spaceBetween: 10,
      slidesPerView: 4,
      freeMode: true,
      watchSlidesProgress: true,
      breakpoints: {
        768: {
          slidesPerView: 5
        }
      }
    });

    const mainSwiper = new Swiper(".main-slider", {
      spaceBetween: 10,
      loop: true,
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev"
      },
      thumbs: {
        swiper: thumbSwiper
      }
    });


      // ==== Phần xử lý biến thể sản phẩm (giữ nguyên) ====
      const buttons = document.querySelectorAll('.variant-btn');
      const product = @json($product);
      const formProductName = document.getElementById('form-product-name');
      const formVariantId = document.getElementById('form-variant-id');
      const formVariantName = document.getElementById('form-variant-name');
      const formPrice = document.getElementById('form-price');
      const defaultProductName = document.getElementById('default-product-name');
      const variantProductName = document.getElementById('variant-product-title');
      const productPrice = document.getElementById('product-price');
      formPrice.value = {{ $product->sale_price > 0 ? $product->sale_price : $product->regular_price }};

      buttons.forEach(btn => {
        btn.addEventListener('click', function () {
          buttons.forEach(b => b.classList.remove('active'));
          this.classList.add('active');
          const variantId = this.getAttribute('data-variant-id');
          const variantTitle = this.getAttribute('data-variant-title');
          const variantPrice = parseFloat(this.getAttribute('data-price'));
          const variant = product.variants.find(v => v.id == variantId);

          if (variantTitle) {
            defaultProductName.style.display = 'none';
            variantProductName.textContent = ` ${variantTitle}`;
            formProductName.value = `${product.name} - ${variantTitle}`;
          } else {
            defaultProductName.style.display = 'inline';
            variantProductName.textContent = '';
            formProductName.value = product.name;
          }

          let priceHtml = '';
          if (variant.sale_price > 0 && variant.sale_price < variant.regular_price) {
            priceHtml = `<s>${parseFloat(variant.regular_price).toLocaleString('vi-VN')}₫</s> <span style="color:red">${parseFloat(variant.sale_price).toLocaleString('vi-VN')}₫</span>`;
          } else {
            priceHtml = `<span>${parseFloat(variant.regular_price).toLocaleString('vi-VN')}₫</span>`;
          }

          productPrice.innerHTML = priceHtml;
          formVariantId.value = variantId;
          formVariantName.value = variantTitle;
          formPrice.value = variantPrice;
        });
      });

      const relatedProductForms = document.querySelectorAll('.product-card form');
      relatedProductForms.forEach(form => {
        const select = form.querySelector('select[name="variant_id"]');
        if (select) {
          select.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            form.querySelector('input[name="price"]').value = price;
          });
        }
      });

      if (!document.querySelector('.variant-btn.active')) {
        variantProductName.textContent = '';
        formProductName.value = product.name;
      }
    });
  </script>
@endpush
