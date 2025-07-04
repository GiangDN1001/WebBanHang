<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\UploadedFile;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\Slide;
use App\Models\User;
use App\Models\Blog;
use DB;
use App\Models\Contact;
use Auth;
use Hash;

class AdminController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at', 'DESC')->take(10)->get();

        $dashboardDatas = DB::select("
            SELECT 
                SUM(total) AS TotalAmount,
                SUM(IF(status = 'ordered', total, 0)) AS TotalOrderedAmount,
                SUM(IF(status = 'delivered', total, 0)) AS TotalDeliveredAmount,
                SUM(IF(status = 'canceled', total, 0)) AS TotalCanceledAmount,
                COUNT(*) AS Total,
                SUM(IF(status = 'ordered', 1, 0)) AS TotalOrdered,
                SUM(IF(status = 'delivered', 1, 0)) AS TotalDelivered,
                SUM(IF(status = 'canceled', 1, 0)) AS TotalCanceled
            FROM Orders
        ");

        $monthlyDatas = DB::select("
            SELECT M.id AS MonthNo, M.name AS MonthName,
                IFNULL(D.TotalAmount, 0) AS TotalAmount,
                IFNULL(D.TotalOrderedAmount, 0) AS TotalOrderedAmount,
                IFNULL(D.TotalDeliveredAmount, 0) AS TotalDeliveredAmount,
                IFNULL(D.TotalCanceledAmount, 0) AS TotalCanceledAmount
            FROM month_names M
            LEFT JOIN (
                SELECT 
                    MONTH(created_at) AS MonthNo,
                    SUM(total) AS TotalAmount,
                    SUM(IF(status = 'ordered', total, 0)) AS TotalOrderedAmount,
                    SUM(IF(status = 'delivered', total, 0)) AS TotalDeliveredAmount,
                    SUM(IF(status = 'canceled', total, 0)) AS TotalCanceledAmount
                FROM Orders
                WHERE YEAR(created_at) = YEAR(NOW())
                GROUP BY MONTH(created_at)
            ) D ON D.MonthNo = M.id
            ORDER BY M.id
        ");

        // Ép kiểu float để đảm bảo biểu đồ nhận đúng dữ liệu số
        $AmountM = implode(',', collect($monthlyDatas)->pluck('TotalAmount')->map(fn($x) => (float) $x)->toArray());
        $OrderedAmountM = implode(',', collect($monthlyDatas)->pluck('TotalOrderedAmount')->map(fn($x) => (float) $x)->toArray());
        $DeliveredAmountM = implode(',', collect($monthlyDatas)->pluck('TotalDeliveredAmount')->map(fn($x) => (float) $x)->toArray());
        $CanceledAmountM = implode(',', collect($monthlyDatas)->pluck('TotalCanceledAmount')->map(fn($x) => (float) $x)->toArray());

        // Tổng cộng cho từng loại trạng thái
        $TotalAmount = collect($monthlyDatas)->sum('TotalAmount');
        $TotalOrderedAmount = collect($monthlyDatas)->sum('TotalOrderedAmount');
        $TotalDeliveredAmount = collect($monthlyDatas)->sum('TotalDeliveredAmount');
        $TotalCanceledAmount = collect($monthlyDatas)->sum('TotalCanceledAmount');

        return view('admin.index', compact(
            'orders',
            'dashboardDatas',
            'AmountM',
            'OrderedAmountM',
            'DeliveredAmountM',
            'CanceledAmountM',
            'TotalAmount',
            'TotalOrderedAmount',
            'TotalDeliveredAmount',
            'TotalCanceledAmount'
        ));
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
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);
    
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
    
        if ($request->hasFile('image')) {
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;        
            $this->GenerateBrandThumbnailImage($request->file('image'), $file_name);
            $brand->image = $file_name;
        }
    
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
        $product->short_description = $request->input('short_description');
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
            'slug' => 'required|unique:products,slug,' . $request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
            'images.*' => 'nullable|mimes:jpg,png,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = Product::findOrFail($request->id);

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

        if ($request->hasFile('image')) {
            if (!empty($product->image)) {
                File::delete(public_path('uploads/products/' . $product->image));
                File::delete(public_path('uploads/products/thumbnails/' . $product->image));
            }
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->getClientOriginalExtension();
            $this->GerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        } else {
            $product->image = $request->input('old_image');
        }
        if ($request->hasFile('images')) {
            if (!empty($product->images)) {
                foreach (explode(',', $product->images) as $oldImage) {
                    File::delete(public_path('uploads/products/' . trim($oldImage)));
                    File::delete(public_path('uploads/products/thumbnails/' . trim($oldImage)));
                }
            }
            $galleryArr = [];
            $count = 1;
            foreach ($request->file('images') as $file) {
                $ext = $file->getClientOriginalExtension();
                $fileName = $current_timestamp . '-' . $count . '.' . $ext;
                $this->GerateProductThumbnailImage($file, $fileName);
                $galleryArr[] = $fileName;
                $count++;
            }
            $product->images = implode(',', $galleryArr);
        } else {
            $product->images = $request->input('old_images');
        }
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

    public function coupons()
    {
        $coupons = Coupon::orderBy("expiry_date","DESC")->paginate(12);
        return view("admin.coupons",compact("coupons"));
    }

    public function add_coupon()
    {        
        return view("admin.coupon-add");
    }

    public function add_coupon_store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date'
        ]);
        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route("admin.coupons")->with('status','Record has been added successfully !');
    }

    public function edit_coupon($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.coupon-edit',compact('coupon'));
    }

    public function update_coupon(Request $request)
    {
        $request->validate([
        'code' => 'required',
        'type' => 'required',
        'value' => 'required|numeric',
        'cart_value' => 'required|numeric',
        'expiry_date' => 'required|date'
        ]);
        $coupon = Coupon::find($request->id);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;               
        $coupon->save();           
        return redirect()->route('admin.coupons')->with('status','Record has been updated successfully !');
    }

    public function delete_coupon($id)
    {
        $coupon = Coupon::find($id);        
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status','Record has been deleted successfully !');
    }

    
    public function orders()
    {
        $orders = Order::orderBy('created_at','DESC')->paginate(12);
        return view("admin.orders",compact('orders'));
    }

    public function order_items($order_id){
        $order = Order::find($order_id);

        if (!$order) {
            return redirect()->route('admin.orders')->with('error', 'Order không tồn tại');
        }

        $orderitems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();

        return view("admin.order-details", compact('order', 'orderitems', 'transaction'));
    }

    public function update_order_status(Request $request){        
        $order = Order::find($request->order_id);

        if (!$order) {
            return back()->with("error", "Đơn hàng không tồn tại.");
        }

        $validated = $request->validate([
            'order_status' => 'required|in:ordered,delivered,canceled'
        ]);

        $order->status = $request->order_status;

        if ($request->order_status == 'delivered') {
            $order->delivered_date = Carbon::now();
        } elseif ($request->order_status == 'canceled') {
            $order->canceled_date = Carbon::now();
        }

        $order->save();

        if ($request->order_status == 'delivered') {
            $transaction = Transaction::where('order_id', $request->order_id)->first();

            if ($transaction) {
                $transaction->status = "approved";
                $transaction->save();
            }
        }

        return back()->with("status", "Cập nhật trạng thái thành công!");
    }


    public function slides() {
        $slides = Slide::orderBy('id','DESC')->paginate(12);
        return view('admin.slides',compact('slides'));
    }

    public function slide_add() {
        return view('admin.slide-add');
    }

    public function slide_store(Request $request) {
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $slide = new Slide();
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;        
        $this->GenerateSlideThumbnailImage($image, $file_name);
        $slide->image = $file_name;
        $slide->save();
        return redirect()->route('admin.slides')->with('status','Slide has been added successfully !');
    }

    public function GenerateSlideThumbnailImage($image,$imageName) {
        $destinationPath = public_path('uploads/slides');
        $img = Image::read($image->path());
        $img->cover(400,690,"top");
        $img->resize(400,690,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function slide_edit($id) {
        $slide = Slide::find($id);
        return view('admin.slide-edit', compact('slide'));  
    }

    public function slide_update(Request $request) {
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $slide = Slide::find($request->id);
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        if ($request->hasFile('image')) {
            if (File::exists(public_path('/uploads/slides/' . $slide->image))) {
                File::delete(public_path('/uploads/slides/' . $slide->image));
            }
            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateSlideThumbnailImage($image, $file_name);
            $slide->image = $file_name;     
        }
        $slide->save();
        return redirect()->route('admin.slides')->with('status','Slide has been updated successfully !');
    }
    
    public function slide_delete($id) {
        $slide = Slide::find($id);
        if(File::exists(public_path('/uploads/slides/' . $slide->image))) {
            File::delete(public_path('/uploads/slides/' . $slide->image));
        }
        $slide->delete();
        return redirect()->route('admin.slides')->with('status','Slide has been deleted successfully !');
    }

    public function contacts() {
        $contacts = Contact::orderBy('created_at','DESC')->paginate(12);
        return view('admin.contacts',compact('contacts'));
    }

    public function contact_delete($id) {
        $contact = Contact::find($id);
        $contact->delete();
        return redirect()->route('admin.contacts')->with('status','Contact has been deleted successfully !');
    }

    public function showSettings()
    {
        $admin = auth()->user();
        return view('admin.settings', compact('admin'));
    }


    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'old_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        $admin = Auth::user();
        $admin->name = $request->name;
        $admin->mobile = $request->mobile;
        $admin->email = $request->email;
        if ($request->filled('old_password') && $request->filled('new_password')) {
            if (Hash::check($request->old_password, $admin->password)) {
                $admin->password = Hash::make($request->new_password);
            } else {
                return back()->withErrors(['old_password' => 'Old password is incorrect.']);
            }
        }
        $admin->save();
        return back()->with('success', 'Profile updated successfully.');
    }

    public function users() {
        $users = User::orderBy('id','DESC')->paginate(12);
        return view('admin.users',compact('users'));
    }
    
    public function search(Request $request) {
        $query = $request->input('query');
        $results = Product::where('name', 'like', "%{$query}%")->get()->take(8);
        return response()->json($results);
    }

    public function blogs()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->paginate(10); // 10 là số item mỗi trang
        return view('admin.blogs', compact('blogs'));
    }


    public function blogs_create() {
        return view('admin.blogs_create');
    }

    public function blog_store(Request $request) {
        $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'short_description' => 'required',
            'content' => 'required',
            'status' => 'required|in:0,1',
        ]);

        $blog = new Blog();
        $blog->title = $request->title;
        $blog->slug = Str::slug($request->title);
        $blog->short_description = $request->short_description;
        $blog->content = $request->content;
        $blog->status = $request->status;

        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/blogs'), $filename);
            $blog->image = $filename;
        }

        $blog->save();
        return redirect()->route('admin.blogs')->with('success', 'Blog created successfully.');
    }

    public function blog_edit($id) {
        $blog = Blog::findOrFail($id);
        return view('admin.blogs_edit', compact('blog'));
    }

    public function blog_update(Request $request, $id) {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'short_description' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'status' => 'required|in:0,1',
        ]);

        $blog->title = $request->title;
        $blog->slug = Str::slug($request->title);
        $blog->short_description = $request->short_description;
        $blog->content = $request->content;
        $blog->status = $request->status;

        if ($request->hasFile('image')) {
            if ($blog->image && file_exists(public_path('uploads/blogs/' . $blog->image))) {
                unlink(public_path('uploads/blogs/' . $blog->image));
            }

            $filename = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/blogs'), $filename);
            $blog->image = $filename;
        }

        $blog->save();
        return redirect()->route('admin.blogs')->with('success', 'Blog updated successfully.');
    }

    public function blog_delete($id) {
        $blog = Blog::findOrFail($id);
        if ($blog->image && file_exists(public_path('uploads/blogs/' . $blog->image))) {
            unlink(public_path('uploads/blogs/' . $blog->image));
        }
        $blog->delete();
        return redirect()->back()->with('success', 'Blog deleted successfully.');
    }
}
