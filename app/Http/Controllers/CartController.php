<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Carbon\Carbon;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewOrderNotification;
use App\Models\ProductVariant;
class CartController extends Controller
{
    public function index() {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function add_to_cart(Request $request) {
        if ($request->has('variant_id') && $request->variant_id != null) {
            // Trường hợp có biến thể
            $variant = ProductVariant::findOrFail($request->variant_id);
            $product = $variant->product;

            Cart::instance('cart')->add([
                'id' => $variant->id,
                'name' => $product->name,
                'qty' => 1,
                'price' => $variant->sale_price ?? $variant->regular_price,
                'weight' => 0,
                'options' => [
                    'product_id' => $product->id,
                    'variant_title' => $variant->variant_title,
                    'image' => $product->image
                ]
            ]);
        } elseif ($request->has('product_id') && $request->product_id != null) {
            // Trường hợp KHÔNG có biến thể
            $product = \App\Models\Product::findOrFail($request->product_id);

            Cart::instance('cart')->add([
                'id' => $product->id,
                'name' => $product->name,
                'qty' => 1,
                'price' => $product->sale_price ?? $product->regular_price,
                'weight' => 0,
                'options' => [
                    'product_id' => $product->id,
                    'variant_title' => null,
                    'image' => $product->image
                ]
            ]);
        } else {
            return redirect()->back()->with('error', 'Không thể thêm sản phẩm vào giỏ hàng.');
        }

        return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng');
    }


    public function increase_cart_quantity($rowId) {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function decrease_cart_quantity($rowId) {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function remove_item($rowId) {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart() {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function increase_item_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();
    }
    public function reduce_item_quantity($rowId){
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();
    }

    public function apply_coupon_code(Request $request)
    {      
        $coupon_code = $request->coupon_code;
        if(isset($coupon_code))
        {
            $coupon = Coupon::where('code',$coupon_code)->where('expiry_date','>=',Carbon::today())->where('cart_value','<=', floatval(str_replace(',', '', Cart::instance('cart')->subtotal())))->first();
            if(!$coupon)
            {
                return back()->with('error','Invalid coupon code!');
            }
            session()->put('coupon',[
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'cart_value' => $coupon->cart_value
            ]);
            $this->calculateDiscounts();
            return back()->with('success','Coupon code has been applied!');
        }
        else{
            return back()->with('error','Invalid coupon code!');
        }        
    }

    public function calculateDiscounts()
    {
        $discount = 0;
        $cartSubtotal = (float) str_replace(',', '', Cart::instance('cart')->subtotal());

        if(session()->has('coupon'))
        {
            if(session()->get('coupon')['type'] == 'fixed')
            {
                $discount = session()->get('coupon')['value'];
            }
            else
            {
                $discount = ($cartSubtotal * session()->get('coupon')['value']) / 100;
            }

            $subtotalAfterDiscount = $cartSubtotal - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

            session()->put('discounts', [
                'discount' => number_format(floatval($discount), 2, '.', ''),
                'subtotal' => number_format(floatval($subtotalAfterDiscount), 2, '.', ''),
                'tax' => number_format(floatval($taxAfterDiscount), 2, '.', ''),
                'total' => number_format(floatval($totalAfterDiscount), 2, '.', '')
            ]);
        }
    }

    public function remove_coupon_code()
    {
        session()->forget('coupon');
        session()->forget('discounts');
        return back()->with('status','Coupon has been removed!');
    }

    public function checkout()
    {
        if(!Auth::check())
        {
            return redirect()->route("login");
        }
        $address = Address::where('user_id',Auth::user()->id)->where('isdefault',1)->first();              
        return view('checkout',compact("address"));
    }

    public function place_order(Request $request)
    {
        $user_id = Auth::user()->id;
        $address = Address::where('user_id',$user_id)->where('isdefault',true)->first();
        if(!$address)
        {
            $request->validate([                
                'name' => 'required|max:100',
                'phone' => 'required|numeric|digits:10',
                'zip' => 'required|numeric|digits:6',
                'state' => 'required',
                'city' => 'required',
                'address' => 'required',
                'locality' => 'required',
                'landmark' => 'required'           
            ]);
            $address = new Address();    
            $address->user_id = $user_id;    
            $address->name = $request->name;
            $address->phone = $request->phone;
            $address->zip = $request->zip;
            $address->state = $request->state;
            $address->city = $request->city;
            $address->address = $request->address;
            $address->locality = $request->locality;
            $address->landmark = $request->landmark;
            $address->country = '';
            $address->isdefault = true;
            $address->save();
        }
        $this->setAmountForCheckout();
        $order = new Order();
        $order->user_id = $user_id;
        $order->subtotal = session()->get('checkout')['subtotal'];
        $order->discount = session()->get('checkout')['discount'];
        $order->tax = session()->get('checkout')['tax'];
        $order->total = session()->get('checkout')['total'];
        $order->name = $address->name;
        $order->phone = $address->phone;
        $order->locality = $address->locality;
        $order->address = $address->address;
        $order->city = $address->city;
        $order->state = $address->state;
        $order->country = $address->country;
        $order->landmark = $address->landmark;
        $order->zip = $address->zip;
        $order->save();                
        foreach(Cart::instance('cart')->content() as $item)
        {
            $orderitem = new OrderItem();
            $orderitem->order_id = $order->id;
            $orderitem->product_id = $item->options->product_id;
  
            if (ProductVariant::find($item->id)) {
                $orderitem->variant_id = $item->id;
                $orderitem->variant_title = $item->options->variant_title;
            }

            $orderitem->price = $item->price;
            $orderitem->quantity = $item->qty;
            $orderitem->save();  
        }
        $transaction = new Transaction();
        $transaction->user_id = $user_id;
        $transaction->order_id = $order->id;
        $transaction->mode = $request->mode;
        $transaction->status = "pending";
        $transaction->save();
        Mail::to('danggianghxpt2003@gmail.com')->send(new NewOrderNotification($order));
        Cart::instance('cart')->destroy();
        session()->forget('checkout');
        session()->forget('coupon');
        session()->forget('discounts');
        return redirect()->route('cart.confirmation');
    }

    public function setAmountForCheckout()
    { 
        if (!Cart::instance('cart')->count() > 0) {
            session()->forget('checkout');
            return;
        }

        if (session()->has('coupon')) {
            session()->put('checkout', [
                'discount' => floatval(str_replace(',', '', session()->get('discounts')['discount'])),
                'subtotal' => floatval(str_replace(',', '', session()->get('discounts')['subtotal'])),
                'tax' => 0,
                'total' => floatval(str_replace(',', '', session()->get('discounts')['total']))
            ]);
        } else {
            session()->put('checkout', [
                'discount' => 0,
                'subtotal' => floatval(str_replace(',', '', Cart::instance('cart')->subtotal())),
                'tax' => 0,
                'total' => floatval(str_replace(',', '', Cart::instance('cart')->total()))
            ]);
        }
    }

    public function confirmation()
    {
        return view('order-confirmation');
    }


}
