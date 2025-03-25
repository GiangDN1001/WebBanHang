<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cart;
use Surfsidemedia\Shoppingcart\Facades\Cart as FacadesCart;
class WishlistController extends Controller
{
    public function iadd_to_wishlist(Request $request) {
        Cart::instance('wishlist')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');
        return redirect()->back();
    }
}
