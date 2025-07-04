<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slide;
use App\Models\Category;
use App\Models\Product;
use App\Models\Contact;
use App\Models\Blog;
class HomeController extends Controller
{
    
    public function index()
    {
        $slides = Slide::where('status',1)->get()->take(3);
        $categories = Category::OrderBy('name')->get();
        $products = Product::where('sale_price', '<>','')->inRandomOrder()->get()->take(8);
        $fproducts = Product::where('featured',1)->get()->take(4);
        $blogs = Blog::where('status', 1)->latest()->take(5)->get();
        return view('index', compact('slides', 'categories', 'products', 'fproducts','blogs'));
    }

    public function contact()
    {
        return view('contact');
    }

    public function contact_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric|digits:10',
            'comment' => 'required'
        ]);
        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->comment = $request->comment;
        $contact->save();
        return redirect()->back()->with('success', 'Your message has been sent successfully.');
    }

    public function about()
    {
        return view('about');
    }

    public function search(Request $request) {
        $query = $request->input('query');
        $results = Product::where('name', 'like', "%$query%")->get()->take(8);
        return response()->json($results);
    }

    public function blog_detail($slug)
    {
        $blog = Blog::where('slug', $slug)->where('status', 1)->firstOrFail();

        $relatedBlogs = Blog::where('status', 1)
            ->where('id', '!=', $blog->id)
            ->latest()
            ->take(4)
            ->get();

        return view('blog_detail', compact('blog', 'relatedBlogs'));
    }

}