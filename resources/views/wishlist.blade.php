@extends('layouts.app')
@section('title', 'Danh mục yêu thích')
@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
        <h2 class="page-title">Wishlist</h2>             
        <div class="shopping-cart">
            @if(Cart::instance("wishlist")->content()->count()>0)
            <div class="cart-table__wrapper">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th></th>
                            <th>Price</th>                           
                            <th>Action</th>                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (Cart::instance('wishlist')->content() as $wishlistItem)
                        <tr>
                            <td>
                                <div class="shopping-cart__product-item">
                                    <img loading="lazy" src="{{asset('uploads/products/thumbnails')}}/{{$wishlistItem->model->image}}" width="120" height="120" alt="" />
                                </div>
                            </td>
                            <td>
                                <div class="shopping-cart__product-item__detail">
                                    <h4>{{$wishlistItem->name}}</h4>
                                    {{-- <ul class="shopping-cart__product-item__options">
                                        <li>Color: Yellow</li>
                                        <li>Size: L</li>
                                    </ul> --}}
                                </div>
                            </td>
                            <td>
                                <span class="shopping-cart__product-price">{{number_format($wishlistItem->price,0,',','.')}}₫</span>
                            </td>      
                            <td>
                                <div class="del-action d-flex gap-2">
                                    <form method="POST" action="{{route('wishlist.move.to.cart',['rowId'=>$wishlistItem->rowId])}}">                                    
                                        @csrf                                    
                                        <button type="submit" class="remove-cart btn btn-sm btn-warning">Move to Cart</button>
                                    </form> 
                                    
                                    <form method="POST" action="{{ route('wishlist.remove', ['rowId' => $wishlistItem->rowId]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="remove-cart btn btn-sm btn-danger">Remove</button>
                                    </form>
                                </div>
                            </td>
                        </tr>   
                        @endforeach
                    </tbody>
                </table>      
                <div class="cart-table-footer">    
                    <form action="{{ route('wishlist.empty') }}" method="POST">
                        @csrf
                        @method('DELETE')                
                        <button class="btn btn-light" type="submit">CLEAR WISHLIST</button>
                    </form>
                </div>          
            </div>   
            @else
                <div class="row">
                    <div class="col-md-12">
                        <p>No item found in your wishlist</p>
                        <a href="{{route('shop.index')}}" class="btn btn-info">Wishlist Now</a>
                    </div>
                </div>
            @endif
        </div>
    </section>
</main>
@endsection