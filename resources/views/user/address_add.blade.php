@extends('layouts.app')
@section('title', 'Add_Address')
@section('content')
    <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
        <h2 class="page-title">Địa chỉ</h2>
        <div class="row">
            <div class="col-lg-2">
                @include('user.account-nav')
            </div>
            <div class="col-lg-9">
                <div class="page-content my-account__address">
                    <div class="row">
                        <div class="col-6">
                            <p class="notice">Các địa chỉ sau sẽ được sử dụng mặc định trên trang thanh toán.</p>
                        </div>
                        <div class="col-6 text-right">
                            <a href="{{ route('user.address') }}" class="btn btn-sm btn-danger">Quay lại</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card mb-5">
                                <div class="card-header">
                                    <h5 style="font-family: sans-serif">Thêm địa chỉ mới</h5>
                                </div>
                                <div class="card-body">
                                    @if(session('status'))
                                        <div class="alert alert-success">
                                            {{ session('status') }}
                                        </div>
                                    @endif
                                    <form action="{{ route('user.address.store') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                                                    <label for="name">Họ và tên *</label>
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                                                    <label for="phone">Số điện thoại *</label>
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-4">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="zip" value="{{ old('zip') }}">
                                                    <label for="zip">Mã bưu chính *</label>
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>                         --}}
                                            <div class="col-md-6">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="city" value="{{ old('city') }}">
                                                    <label for="city">Quận / Huyện *</label>
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mt-3 mb-3">
                                                    <input type="text" class="form-control" name="state" value="{{ old('state') }}">
                                                    <label for="state">Tỉnh / Thành phố *</label>
                                                    <span class="text-danger"></span>
                                                </div>                            
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                                                    <label for="address">Số nhà, Tên tòa nhà *</label>
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="locality" value="{{ old('locality') }}">
                                                    <label for="locality">Tên đường, Khu vực, Khu phố *</label>
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>    
                                            {{-- <div class="col-md-12">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="landmark" value="{{ old('landmark') }}">
                                                    <label for="landmark">Địa điểm gần đó *</label>
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>   --}}

                                            {{-- <div class="col-md-12">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="country" value="{{ old('country') }}">
                                                    <label for="country">Quốc gia *</label>
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div> --}}
                                            <div class="col-md-12">
                                                <div class="form-floating my-3">
                                                    <select style="width: 100%; height: 65px;" class="form-select" name="type">
                                                        <option value="home" selected>Nhà riêng</option>
                                                        <option value="office">Văn phòng</option>
                                                    </select>
                                                    <label for="type">Loại địa chỉ *</label>
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1" id="isdefault" name="isdefault">
                                                    <label class="form-check-label" for="isdefault">
                                                        Đặt làm địa chỉ mặc định
                                                    </label>
                                                </div>
                                            </div>  
                                            <div class="col-md-12 text-right">
                                                <button type="submit" class="btn btn-success">Gửi</button>
                                            </div>                                     
                                        </div>
                                    </form> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>                    
                </div>
            </div>
        </div>
    </section>
    </main>
@endsection
