<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
class ShopController extends Controller
{
    public function index(REquest $request)
    {   
        $size = $request->query('size') ?  $request->query('size') : 10;
        $o_column = "";
        $o_oder = "";
        $order = $request->query('order') ?  $request->query('order') : -1;
        $f_brands = $request->query('brands');
        $f_categories = $request->query('categories');
        $min_price = $request->query('min') ?  $request->query('min') : 1;
        $max_price = $request->query('max') ?  $request->query('max') : 10000;

        switch ($order) {
            case 1:
                $o_column = "created_at";
                $o_oder = "DESC";
                break;
            case 2:
                $o_column = "created_at";
                $o_oder = "ASC";
                break;
            case 3:
                $o_column = "sale_price";
                $o_oder = "ASC";
                break;
            case 4:
                $o_column = "sale_price";
                $o_oder = "DESC";
                break;
            default:
                $o_column = "id";
                $o_oder = "DESC";
                break;
        }
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $products = Product::where(function($query) use ($f_brands) {
            $query->whereIn('brand_id', explode(',', $f_brands))->orwhereRaw("'".$f_brands."' = ''");
        })->where(function($query) use ($f_categories) {
            $query->whereIn('category_id', explode(',', $f_categories))->orwhereRaw("'".$f_categories."' = ''");
        })->where(function($query) use ($min_price, $max_price) {
            $query->whereBetween('regular_price', [$min_price, $max_price])->orwhereBetween('sale_price', [$min_price, $max_price]);
        })->orderBy($o_column, $o_oder)->paginate($size);
        return view('shop', compact('products','size', 'order','brands', 'f_brands', 'f_categories','categories', 'min_price', 'max_price'));
    }

    public function product_details($product_slug) {
        $product = Product::where('slug', $product_slug)->first();
        $rproducts = Product::where('slug', '<>', $product_slug)->get()->take(8);
        return view('details', compact('product', 'rproducts'));
    }

    
}
