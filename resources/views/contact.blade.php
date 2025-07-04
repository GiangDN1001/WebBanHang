@extends('layouts.app')
@section('title', 'Contact')

@section('content')
    <style>
        .text-danger{
            color: red !important;
        }
    </style>
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="contact-us container">
        <div class="mw-930">
            <h2 class="page-title">LIÊN HỆ CHÚNG TÔI</h2>
        </div>
        </section>

        <hr class="mt-2 text-secondary " />
        <div class="mb-4 pb-4"></div>

        <section class="contact-us container">
        <div class="mw-930">
            <div class="contact-us__form">
                @if(Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ Session::get('success') }} <!-- Thông báo thành công sẽ phụ thuộc vào nội dung thực tế -->
                    </div>
                @endif
                <form name="contact-us-form" class="needs-validation" novalidate="" method="POST" action="{{route('home.contact.store')}}">
                    @csrf
                    <h3 class="mb-5">KẾT NỐI VỚI CHÚNG TÔI</h3>
                    <div class="form-floating my-4">
                    <input type="text" class="form-control" value="{{ old('name') }}" name="name" placeholder="Họ tên *" required="">
                    <label for="contact_us_name">Họ tên *</label>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span> <!-- Lỗi sẽ phụ thuộc vào nội dung thực tế -->
                    @enderror
                    </div>
                    <div class="form-floating my-4">
                    <input type="text" class="form-control" value="{{ old('phone') }}" name="phone" placeholder="Số điện thoại *" required="">
                    <label for="contact_us_name">Số điện thoại *</label>
                    @error('phone')
                        <span class="text-danger">{{ $message }}</span> <!-- Lỗi sẽ phụ thuộc vào nội dung thực tế -->
                    @enderror
                    </div>
                    <div class="form-floating my-4">
                    <input type="email" class="form-control" value="{{ old('email') }}" name="email" placeholder="Địa chỉ email *" required="">
                    <label for="contact_us_name">Địa chỉ email *</label>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span> <!-- Lỗi sẽ phụ thuộc vào nội dung thực tế -->
                    @enderror
                    </div>
                    <div class="my-4">
                    <textarea class="form-control form-control_gray"  name="comment" placeholder="Tin nhắn của bạn" cols="30"
                        rows="8" required="">{{ old('comment') }}</textarea>
                    @error('comment')
                        <span class="text-danger">{{ $message }}</span> <!-- Lỗi sẽ phụ thuộc vào nội dung thực tế -->
                    @enderror
                    </div>
                    <div class="my-4">
                        <button type="submit" class="btn btn-primary">Gửi</button>
                    </div>
                </form>
            </div>
        </div>
        </section>
  </main>
@endsection