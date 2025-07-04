@extends('layouts.app')
@section('title', 'Edit_Address')
@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
        <h2 class="page-title">Chỉnh sửa địa chỉ</h2>
        <div class="row">
            <div class="col-lg-2">
                @include('user.account-nav')
            </div>
            <div class="col-lg-9">
                <div class="page-content my-account__address">
                    <div class="row">
                        <div class="col-6">
                            <p class="notice">Bạn có thể chỉnh sửa thông tin địa chỉ bên dưới.</p>
                        </div>
                        <div class="col-6 text-right">
                            <a href="{{ route('user.address') }}" class="btn btn-sm btn-danger">Quay lại</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card mb-5">
                                <div class="card-header">
                                    <h5>Chỉnh sửa địa chỉ</h5>
                                </div>
                                <div class="card-body">
                                    @if(session('status'))
                                        <div class="alert alert-success">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    <form action="{{ route('user.address.update', $address->id) }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="name" value="{{ old('name', $address->name) }}">
                                                    <label for="name">Họ và tên *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="phone" value="{{ old('phone', $address->phone) }}">
                                                    <label for="phone">Số điện thoại *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="zip" value="{{ old('zip', $address->zip) }}">
                                                    <label for="zip">Mã bưu điện *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="state" value="{{ old('state', $address->state) }}">
                                                    <label for="state">Tỉnh / Bang *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="city" value="{{ old('city', $address->city) }}">
                                                    <label for="city">Thành phố / Thị trấn *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="address" value="{{ old('address', $address->address) }}">
                                                    <label for="address">Số nhà, Tên tòa nhà *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="locality" value="{{ old('locality', $address->locality) }}">
                                                    <label for="locality">Tên đường, Khu vực, Khu phố *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="landmark" value="{{ old('landmark', $address->landmark) }}">
                                                    <label for="landmark">Địa danh gần đó *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="country" value="{{ old('country', $address->country) }}">
                                                    <label for="country">Quốc gia *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-floating my-3">
                                                    <select style="width: 100%; height: 65px;" class="form-select" name="type">
                                                        <option value="home" {{ old('type', $address->type) == 'home' ? 'selected' : '' }}>Nhà riêng</option>
                                                        <option value="office" {{ old('type', $address->type) == 'office' ? 'selected' : '' }}>Văn phòng</option>
                                                    </select>
                                                    <label for="type">Loại địa chỉ *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12 text-right">
                                                <button type="submit" class="btn btn-success">Cập nhật địa chỉ</button>
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
