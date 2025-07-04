<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

class ShopController extends Controller
{
    public function index(Request $request)
    {
                $size = $request->query('size', 43);
        $order = $request->query('order', -1);
        $f_brands = $request->query('brands', '');
        $f_categories = $request->query('categories', '');
        $min_price = $request->query('min', 1);
        $max_price = $request->query('max', 10000);

        // Mapping sắp xếp
        switch ($order) {
            case 1:
                $o_column = "created_at";
                $o_order = "DESC";
                break;
            case 2:
                $o_column = "created_at";
                $o_order = "ASC";
                break;
            case 3:
                $o_column = "sale_price";
                $o_order = "ASC";
                break;
            case 4:
                $o_column = "sale_price";
                $o_order = "DESC";
                break;
            default:
                $o_column = "id";
                $o_order = "DESC";
                break;
        }

        $query = Product::query();

        // Lọc theo thương hiệu nếu có
        $brand_ids = array_filter(explode(',', $f_brands), fn($id) => is_numeric($id));
        if (!empty($brand_ids)) {
            $query->whereIn('brand_id', $brand_ids);
        }

        // Lọc theo danh mục nếu có
        $category_ids = array_filter(explode(',', $f_categories), fn($id) => is_numeric($id));
        if (!empty($category_ids)) {
            $query->whereIn('category_id', $category_ids);
        }

        // Thực hiện sắp xếp và phân trang
        $products = $query->orderBy($o_column, $o_order)->paginate($size);

        // Danh sách filter
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('shop', compact(
            'products', 'size', 'order', 'brands', 'f_brands',
            'categories', 'f_categories', 'min_price', 'max_price'
        ));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->firstOrFail();
        $rproducts = Product::where('slug', '<>', $product_slug)
                            ->latest()
                            ->take(8)
                            ->get();

        return view('details', compact('product', 'rproducts'));
    }
}
