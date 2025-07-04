@extends('layouts.app') {{-- Hoặc layouts phù hợp với giao diện bạn dùng --}}

@section('content')
    <div class="container py-5">
        <h3 class="mb-3">{{ $blog->title }}</h3>
        <hr>
        <div class="blog-content">
            {!! $blog->content !!}
        </div>
        <hr>
        <h3>Bài viết liên quan</h3>
        <div class="row">
            @forelse($relatedBlogs as $related)
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card h-100">
                        @if($related->image)
                            <img src="{{ asset('uploads/blogs/' . $related->image) }}" class="card-img-top" alt="{{ $related->title }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $related->title }}</h5>
                            <p class="card-text">{{ Str::limit($related->short_description, 100) }}</p>
                            <a href="{{ route('blog.detail', $related->slug) }}" class="btn btn-sm btn-outline-primary">Đọc thêm</a>
                        </div>
                    </div>
                </div>
            @empty
                <p>Không có bài viết liên quan.</p>
            @endforelse
        </div>

    </div>
@endsection
