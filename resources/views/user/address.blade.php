@extends('layouts.app')
@section('title', 'Address')
@section('content')
    <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
        <h2 class="page-title">Địa Chỉ</h2>
        <div class="row">
            <div class="col-lg-3">
                <ul class="account-nav">
                    <li><a href="{{ route('user.index') }}" class="menu-link menu-link_us-s">Bảng điều khiển</a></li>
                    <li><a href="{{route('user.account.orders')}}" class="menu-link menu-link_us-s {{Route::is('user.account.orders') ? 'menu-link_active':''}}">Đơn đặt hàng</a></li>
                    <li><a href="{{ route('user.address') }}" class="menu-link menu-link_us-s">Địa chỉ</a></li>
                    {{-- <li><a href="account-details.html" class="menu-link menu-link_us-s">Chi tiết tài khoản</a></li>
                    <li><a href="account-wishlist.html" class="menu-link menu-link_us-s">Wishlist</a></li> --}}
                    <li>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <a href="{{ route('logout') }}" class="menu-link menu-link_us-s" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng xuất</a>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="col-lg-9">
                <div class="page-content my-account__address">
                <div class="row">
                    <div class="col-6">
                    <p class="notice">Các địa chỉ sau đây sẽ được sử dụng trên trang thanh toán theo mặc định.</p>
                    </div>
                    <div class="col-6 text-right">
                    <a href="{{ route('user.address.add') }}" class="btn btn-sm btn-info">Thêm địa chỉ</a>
                    </div>
                </div>
                <div class="my-account__address-list row">
                    <h5 style="font-family: sans-serif">Địa chỉ giao hàng</h5>

                    @foreach($addresses as $address)
                        <div class="my-account__address-item col-md-6 mb-4">
                            <div class="my-account__address-item__title d-flex justify-content-between align-items-center">
                                <h5>{{ $address->name }} 
                                    @if($address->isdefault)
                                        <i class="fa fa-check-circle text-success"></i>
                                    @endif
                                </h5>
                                <a href="{{ route('user.address.edit', $address->id) }}">Sửa địa chỉ</a>
                            </div>
                            <div class="my-account__address-item__detail">
                                <p>{{ $address->address }}, {{ $address->locality }}, {{ $address->city }}, {{ $address->state }}</p>
                                {{-- <p>{{ $address->country }}  {{ $address->zip }}</p> --}}
                                {{-- <br> --}}
                                <p>Mobile : {{ $address->phone }}</p>
                            </div>
                        </div>
                    @endforeach

                    @if($addresses->isEmpty())
                        <div class="col-12">
                            <p class="text-muted">You haven't added any addresses yet.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>
    </main>
@endsection