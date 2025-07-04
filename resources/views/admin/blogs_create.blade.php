@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Blog Information</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ route('admin.blogs') }}">
                        <div class="text-tiny">Blogs</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">New Blog</div>
                </li>
            </ul>
        </div>
        <!-- new-category -->
        <div class="wg-box">
            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form class="form-new-product form-style-1" action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <fieldset class="name">
                    <div class="body-title">Title<span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Blogs name" name="title"
                        tabindex="0" value="{{ old('title') }}" aria-required="true" required>
                </fieldset>
                @error('title')
                    <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror

                <fieldset class="name">
                    <div class="body-title">Blog Slug <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Blog Slug" name="slug"
                        tabindex="0" value="{{ old('slug') }}" aria-required="true" required>
                </fieldset>
                @error('slug')
                    <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror

                <fieldset class="name">
                    <div class="body-title">Short Description <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Short Description" name="short_description"
                        value="{{ old('short_description') }}" required>
                </fieldset>
                @error('short_description')
                    <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror

                <fieldset>
                    <div class="body-title">Upload Image <span class="tf-color-1">*</span></div>
                    <input type="file" name="image" accept="image/*">
                </fieldset>
                @error('image')
                    <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror

                <fieldset class="name">
                    <div class="body-title">Content <span class="tf-color-1">*</span></div>
                    <textarea id="content" name="content">{{ old('content') }}</textarea>
                </fieldset>
                @error('content')
                    <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror

                <fieldset class="name">
                    <div class="body-title">Status <span class="tf-color-1">*</span></div>
                    <div class="select" style="width: 100%">
                        <select name="status" required>
                            <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>On</option>
                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Off</option>
                        </select>
                    </div>
                </fieldset>
                @error('status')
                    <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror

                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>

            <script src="{{ asset('js/tinymce/js/tinymce/tinymce.min.js') }}"></script>    
            <script>
                tinymce.init({
                    selector: '#content',
                    plugins: 'lists link image table code align', 
                    toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code',
                    menubar: false,
                    height: 500,
                    setup: function (editor) {
                        editor.on('change', function () {
                            document.querySelector('#content').value = editor.getContent();
                        });
                        editor.on('submit', function () {
                            document.querySelector('#content').value = editor.getContent();
                        });
                    }
                });
            </script>

        </div>
    </div>
</div>
@endsection


@push('scripts')
    <script>
        $(function() {

            $('#myFile').on('change', function() {
                const [file] = this.files;
                if (file) {
                    $("#imgpreview img").attr('src', URL.createObjectURL(file));
                    $("#imgpreview").show();
                }
            });

            $("input[name='title']").on('change', function() { 
                $("input[name='slug']").val(StringToSlug($(this).val()));
            });

        });
        function StringToSlug(Text) {
            return Text.toLowerCase()
                .replace(/[^a-z0-9]+/g, "-")
                .replace(/^-+|-+$/g, "");
        }
    </script>
@endpush
