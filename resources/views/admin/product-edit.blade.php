@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit Product</h3>
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
                    <a href="{{ route('admin.products') }}">
                        <div class="text-tiny">Products</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Edit product</div>
                </li>
            </ul>
        </div>
        <!-- form-add-product -->
        <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data" action="{{ route('admin.product.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $product->id }}">
            <div class="wg-box">
                <fieldset class="name">
                    <div class="body-title mb-10">Product name <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter product name" name="name" tabindex="0" value="{{ $product->name }}" aria-required="true" required="">
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('name') <span class=" alert alert-danger text-center">{{ $message }}</span> @enderror

                <fieldset class="name">
                    <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter product slug"
                        name="slug" tabindex="0" value="{{ $product->slug }}" aria-required="true" required>
                    <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                </fieldset>
                @error('slug') <span class=" alert alert-danger text-center">{{ $message }}</span> @enderror

                <div class="gap22 cols">
                    <fieldset class="category">
                        <div class="body-title mb-10">Category <span class="tf-color-1">*</span>
                        </div>
                        <div class="select">
                            <select class="" name="category_id">
                                <option>Choose category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </fieldset>
                    @error('category_id') <span class=" alert alert-danger text-center">{{ $message }}</span> @enderror

                    <fieldset class="brand">
                        <div class="body-title mb-10">Brand <span class="tf-color-1">*</span>
                        </div>
                        <div class="select">
                            <select class="" name="brand_id">
                                <option>Choose Brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"  {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach

                            </select>
                        </div>
                    </fieldset>
                    @error('brand_id') <span class=" alert alert-danger text-center">{{ $message }}</span> @enderror

                </div>

                <fieldset class="shortdescription">
                    <div class="body-title mb-10">Short Description <span
                            class="tf-color-1">*</span></div>
                    <textarea class="mb-10 ht-150" name="short_description" id="short_description"
                        placeholder="Short Description" tabindex="0" aria-required="true"
                        required="">{{ $product->short_description }}</textarea>
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('short_description') <span class=" alert alert-danger text-center">{{ $message }}</span> @enderror

                <fieldset class="description">
                    <div class="body-title mb-10">Description <span class="tf-color-1">*</span>
                    </div>
                    <textarea class="mb-10" name="description" placeholder="Description" id="description"
                        tabindex="0" aria-required="true" required="">{{ $product->description }}</textarea>
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('description') <span class=" alert alert-danger text-center">{{ $message }}</span> @enderror

            </div>
            <div class="wg-box">
                <fieldset>
                    <div class="body-title">Upload images <span class="tf-color-1">*</span>
                    </div>
                    <div class="upload-image flex-grow">
                        @if($product->image)
                            <div class="item" id="imgpreview">
                                <img src="{{ asset('uploads/products/' . $product->image) }}" class="effect8" alt="${{ $product->image }}">
                            </div>
                            <input type="hidden" name="old_image" value="{{ $product->image }}">
                        @endif
                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your images here or select <span
                                        class="tf-color">click to browse</span></span>
                                <input type="file" id="myFile" name="image" accept="image/*">
                            </label>
                        </div>
                    </div>
                </fieldset>
                @error('image') <span class=" alert alert-danger text-center">{{ $message }}</span> @enderror

                <fieldset>
                    <div class="body-title mb-10">Upload Gallery Images</div>
                    <div class="upload-image mb-16">
                    @if($product->images)
                        @foreach(explode(',',$product->images) as $img )
                            <div class="item gitem">
                                <img src="{{ asset('uploads/products/') }}/{{ trim($img) }}" alt="">
                            </div>   
                        @endforeach
                        <input type="hidden" name="old_images" value="{{ $product->images }}">
                    @endif                                            
                        <div id="galUpload" class="item up-load">
                            <label class="uploadfile" for="gFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="text-tiny">Drop your images here or select <span
                                        class="tf-color">click to browse</span></span>
                                <input type="file" id="gFile" name="images[]" accept="image/*"
                                    multiple="">
                            </label>
                        </div>
                    </div>
                </fieldset>
                @error('images') <span class=" alert alert-danger text-center">{{ $message }}</span> @enderror

                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">Regular Price <span
                                class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter regular price"
                            name="regular_price" tabindex="0" value="{{ $product->regular_price }}" aria-required="true"
                            required="">
                    </fieldset>
                    @error('regular_price') <span class=" alert alert-danger text-center">{{ $message }}</span> @enderror

                    <fieldset class="name">
                        <div class="body-title mb-10">Sale Price <span
                                class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter sale price"
                            name="sale_price" tabindex="0" value="{{ $product->sale_price }}" aria-required="true"
                            required="">
                    </fieldset>
                    @error('sale_price') <span class=" alert alert-danger text-center">{{ $message }}</span> @enderror
                    
                </div>


                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">SKU <span class="tf-color-1">*</span>
                        </div>
                        <input class="mb-10" type="text" placeholder="Enter SKU" name="SKU"
                            tabindex="0" value="{{ $product->SKU }}" aria-required="true" required="">
                    </fieldset>
                    @error('SKU') <span class=" alert alert-danger text-center">{{ $message }}</span> @enderror

                    <fieldset class="name">
                        <div class="body-title mb-10">Quantity <span class="tf-color-1">*</span>
                        </div>
                        <input class="mb-10" type="text" placeholder="Enter quantity"
                            name="quantity" tabindex="0" value="{{ $product->quantity }}" aria-required="true"
                            required="">
                    </fieldset>
                    @error('quantity') <span class=" alert alert-danger text-center">{{ $message }}</span> @enderror

                </div>

                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">Stock</div>
                        <div class="select mb-10">
                            <select class="" name="stock_status">
                                <option value="instock" {{ $product->stock_status == 'instock' ? 'selected' : '' }}>InStock</option>
                                <option value="outofstock" {{ $product->stock_status == 'outofstock' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                        </div>
                    </fieldset>
                    @error('stock_status') <span class=" alert alert-danger text-center">{{ $message }}</span> @enderror

                    <fieldset class="name">
                        <div class="body-title mb-10">Featured</div>
                        <div class="select mb-10">
                            <select class="" name="featured">
                                <option value="0" {{ $product->featured == "0" ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $product->featured == "1" ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </fieldset>
                    @error('featured') <span class=" alert alert-danger text-center">{{ $message }}</span> @enderror

                </div>
                <h4>Biến thể sản phẩm</h4>
                <div id="variants-wrapper">
                @foreach($product->variants as $index => $variant)
                    <div class="variant-item border p-3 mb-2">
                        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">

                        <div class="form-group">
                            <label>Tên biến thể</label>
                            <input type="text" class="form-control" name="variants[{{ $index }}][variant_name]" value="{{ $variant->variant_name }}">
                        </div>

                        <div class="form-group">
                            <label>Tiêu đề biến thể</label>
                            <input type="text" class="form-control" name="variants[{{ $index }}][variant_title]" value="{{ $variant->variant_title }}">
                        </div>

                        <div class="form-group">
                            <label>Giá gốc</label>
                            <input type="number" class="form-control" name="variants[{{ $index }}][regular_price]" value="{{ $variant->regular_price }}">
                        </div>

                        <div class="form-group">
                            <label>Giá khuyến mãi</label>
                            <input type="number" class="form-control" name="variants[{{ $index }}][sale_price]" value="{{ $variant->sale_price }}">
                        </div>

                        <div class="form-group">
                            <label>Số lượng</label>
                            <input type="number" class="form-control" name="variants[{{ $index }}][quantity]" value="{{ $variant->quantity }}">
                        </div>

                        <button type="button" class="btn btn-danger btn-sm mt-2 remove-variant">Xóa biến thể</button>
                    </div>
                @endforeach
                </div>

                <button type="button" id="add-variant" class="btn btn-secondary mt-3">Thêm biến thể</button>

                <div class="cols gap10">
                    <button class="tf-button w-full" type="submit">Update product</button>
                </div>
            </div>
        </form>
        <!-- /form-add-product -->
        
    </div>
    <!-- /main-content-wrap -->
</div>
@endsection


@push('scripts')
    <script>
        $(function() {

            $('#myFile').on('change', function() {
                const photoInp = $("#myFile");
                const [file] = this.files;
                if (file) {
                    $("#imgpreview img").attr('src', URL.createObjectURL(file));
                    $("#imgpreview").show();
                }
            });

            
            $('#gFile').on('change', function() {
                const photoInp = $("#gFile");
                const gphotos = this.files;
                $.each(gphotos, function(key, val) {
                    $("#galUpload").prepend(`<div class="item gitems"><img src="${URL.createObjectURL(val)}" alt="image" /></div>`)
                })

            });

            $("input[name='name']").on('change', function() { 
                $("input[name='slug']").val(StringToSlug($(this).val()));
            });

        });

        function StringToSlug(Text) {
            return Text.toLowerCase()
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-+|-+$/g, "");
        }
    </script>

    <script src="{{ asset('js/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script>
        tinymce.init({
            selector: '#short_description',
            height: 500,
            plugins: [
                'advlist autolink lists link image charmap preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table code help wordcount',
                'textcolor colorpicker',
                'fontfamily fontsize'
            ],
            toolbar: 'undo redo | formatselect fontselect fontsizeselect | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | table | code preview fullscreen',
            menubar: 'file edit view insert format tools table help',
            font_formats: 'Arial=arial,helvetica,sans-serif; Courier New=courier new,courier; Times New Roman=times new roman,times;',
            fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt 48pt',
            content_style: "body { font-family: Arial; font-size:14px }",
            setup: function (editor) {
                editor.on('change', function () {
                    document.querySelector('#short_description').value = editor.getContent();
                });
            }
        });

        tinymce.init({
            selector: '#description',
            height: 500,
            plugins: [
                'advlist autolink lists link image charmap preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table code help wordcount',
                'textcolor colorpicker',
                'fontfamily fontsize'
            ],
            toolbar: 'undo redo | formatselect fontselect fontsizeselect | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | table | code preview fullscreen',
            menubar: 'file edit view insert format tools table help',
            font_formats: 'Arial=arial,helvetica,sans-serif; Courier New=courier new,courier; Times New Roman=times new roman,times;',
            fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt 48pt',
            content_style: "body { font-family: Arial; font-size:14px }",
            setup: function (editor) {
                editor.on('change', function () {
                    document.querySelector('#description').value = editor.getContent();
                });
            }
        });

        document.querySelector('form').addEventListener('submit', function (e) {
            document.querySelector('#short_description').value = tinymce.get('short_description').getContent();
            document.querySelector('#description').value = tinymce.get('description').getContent();
        });
    </script>

    <script>
        let variantIndex = {{ $product->variants->count() }};
        document.getElementById('add-variant').addEventListener('click', function () {
            const wrapper = document.getElementById('variants-wrapper');
            const html = `
            <div class="variant-item border p-3 mb-2">
                <div class="form-group">
                <label>Tên biến thể</label>
                <input type="text" class="form-control" name="variants[${variantIndex}][variant_name]">
                </div>

                <div class="form-group">
                <label>Tiêu đề biến thể</label>
                <input type="text" class="form-control" name="variants[${variantIndex}][variant_title]">
                </div>

                <div class="form-group">
                <label>Giá gốc</label>
                <input type="number" class="form-control" name="variants[${variantIndex}][regular_price]">
                </div>

                <div class="form-group">
                <label>Giá khuyến mãi</label>
                <input type="number" class="form-control" name="variants[${variantIndex}][sale_price]">
                </div>

                <div class="form-group">
                <label>Số lượng</label>
                <input type="number" class="form-control" name="variants[${variantIndex}][quantity]">
                </div>

                <button type="button" class="btn btn-danger btn-sm mt-2 remove-variant">Xóa biến thể</button>
            </div>
            `;
            wrapper.insertAdjacentHTML('beforeend', html);
            variantIndex++;
        });
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-variant')) {
            e.target.closest('.variant-item').remove();
            }
        });
    </script>

@endpush