<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\UploadedFile;
class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    //brands

    public function brands() 
    {
        $brands = Brand::orderby('id', 'DESC')->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    public function add_brand() 
    {
        return view('admin.brand-add');
    }

    public function brand_store(Request $request)
    {        
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;        
        $this->GenerateBrandThumbnailImage($request->file('image'),$file_name);
        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status','Brand has been added successfully !');
    }
    
    

    public function brand_edit($id) {
        $brand = Brand::find($id);
        return view('admin.brand-edit', compact('brand'));
        
    }

    public function brand_update(Request $request) {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $request->id,
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);
        $brand = Brand::find($request->id);
        if (!$brand) {
            return redirect()->back()->with('error', 'Brand not found.');
        }
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
    
        if ($request->hasFile('image')) {
            if (File::exists(public_path('/uploads/brands/' . $brand->image))) {
                File::delete(public_path('/uploads/brands/' . $brand->image));
            }
            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateBrandThumbnailImage($image, $file_name);
            $brand->image = $file_name;     
        }
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been updated successfully!');
    }
    

    public function brand_delete($id) {
        $brand = Brand::find($id);
        if(File::exists(public_path('/uploads/brands').'/'.$brand->image)) {
            File::delete(public_path('/uploads/brands').'/'.$brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status','Brand has been deleted successfully !');
    }

    public function GenerateBrandThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        $img = \Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124,124,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }
    
    //categories

    public function categories() {
        $categories = Category::orderby('id', 'DESC')->paginate(10);
        return view('admin.categories', compact('categories'));
    } 

    public function category_add() {
        return view('admin.category-add');
    }

    public function category_store(Request $request) {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;        
        $this->GenerateCategoryThumbailImage($request->file('image'),$file_name);
        $category->image = $file_name;
        $category->save();
        return redirect()->route('admin.categories')->with('status','Category has been added successfully !');
    }

    public function GenerateCategoryThumbailImage($image, $imageName) {
        $destinationPath = public_path('uploads/categories');
        $img = \Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124,124,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function category_delete($id) { 
        $categories = Category::find($id);
        if(File::exists(public_path('/uploads/categories').'/'.$categories->image)) {
            File::delete(public_path('/uploads/categories').'/'.$categories->image);
        }
        $categories->delete();
        return redirect()->route('admin.categories')->with('status','Categories has been deleted successfully !');
    }

    public function category_edit($id) {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update(Request $request) {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $request->id,
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);
        $categories = Category::find($request->id);
        if (!$categories) {
            return redirect()->back()->with('error', 'Category not found.');
        }
        $categories->name = $request->name;
        $categories->slug = Str::slug($request->name);
    
        if ($request->hasFile('image')) {
            if (File::exists(public_path('/uploads/categories/' . $categories->image))) {
                File::delete(public_path('/uploads/categories/' . $categories->image));
            }
            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateCategoryThumbailImage($image, $file_name);
            $categories->image = $file_name;     
        }
        $categories->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been updated successfully!');
    }

    public function products()  {
        $products = Product::orderby('created_at', 'DESC')->paginate(10);
        return view('admin.products', compact('products'));
    }

    public function product_add() {
        $categories = Category::select('id', 'name')->orderby('name')->get();
        $brands = Brand::select('id', 'name')->orderby('name')->get();
        return view('admin.product-add', compact('categories', 'brands'));
    }

    public function product_store(Request $request) {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $current_timestamp = Carbon::now()->timestamp;

        if($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $galleyry_arr = array();
        $galleyry_images = "";
        $couter = 1;

        if($request->hasFile('images')) {
            $allowedfileExtions = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtions);
                if($gcheck) {
                    $gfileName = $current_timestamp . "-" . $couter . '.' . $gextension;
                    $this->GerateProductThumbnailImage($file, $gfileName);
                    array_push($galleyry_arr, $gfileName);
                    $couter++;
                }
            }  
            $galleyry_images = implode(',', $galleyry_arr); 
        }
        $product->images = $galleyry_images;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been added successfully!');
    }

    public function GerateProductThumbnailImage($image, $imageName) {
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products');
        $img = \Image::read($image->path());
        
        $img->cover(540,689,"top");
        $img->resize(540,689,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);

        $img->resize(104,104,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPathThumbnail.'/'.$imageName);
    }

    public function product_edit($id) {
        $product = Product::find($id);
        $categories = Category::select('id', 'name')->orderby('name')->get();
        $brands = Brand::select('id', 'name')->orderby('name')->get();
        return view('admin.product-edit', compact('product', 'categories', 'brands'));
    }

    public function product_update(Request $request) {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,'.$request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);
        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $current_timestamp = Carbon::now()->timestamp;
        if($request->hasFile('image')) {
            if(File::exists(public_path('/uploads/products/' . $product->image))) {
                File::delete(public_path('/uploads/products/' . $product->image));
            }
            if(File::exists(public_path('/uploads/products/thumbnails/' . $product->image))) {
                File::delete(public_path('/uploads/products/thumbnails/' . $product->image));
            }
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }
        $galleyry_arr = array();
        $galleyry_images = "";
        $couter = 1;
        if($request->hasFile('images')) {
            foreach(explode(',', $product->images) as $oflile) {
                if(File::exists(public_path('/uploads/products/' .$oflile))) {
                    File::delete(public_path('/uploads/products/' .$oflile));
                }
                if(File::exists(public_path('/uploads/products/thumbnails/' .$oflile))) {
                    File::delete(public_path('/uploads/products/thumbnails/' .$oflile));
                }
            }
            $allowedfileExtions = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtions);
                if($gcheck) {
                    $gfileName = $current_timestamp . "-" . $couter . '.' . $gextension;
                    $this->GerateProductThumbnailImage($file, $gfileName);
                    array_push($galleyry_arr, $gfileName);
                    $couter++;
                }
            }  
            $galleyry_images = implode(',', $galleyry_arr); 
        }
        $product->images = $galleyry_images;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully!');
    }

    public function product_delete($id) { 
        $product = Product::find($id);
        if(File::exists(public_path('/uploads/products').'/'.$product->image)) {
            File::delete(public_path('/uploads/products').'/'.$product->image);
        }
        if(File::exists(public_path('/uploads/products/thumbnails').'/'.$product->image)) {
            File::delete(public_path('/uploads/products/thumbnails').'/'.$product->image);
        }
        foreach(explode(',', $product->images) as $oflile) {
            if(File::exists(public_path('/uploads/products/' .$oflile))) {
                File::delete(public_path('/uploads/products/' .$oflile));
            }
            if(File::exists(public_path('/uploads/products/thumbnails/' .$oflile))) {
                File::delete(public_path('/uploads/products/thumbnails/' .$oflile));
            }
        }
        $product->delete();
        return redirect()->route('admin.products')->with('status','Product has been deleted successfully !');
    }

}
