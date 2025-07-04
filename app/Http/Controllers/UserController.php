<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Auth;
use Carbon\Carbon;
use App\Models\Address;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }
    
    public function account_orders()
    {
        $orders = Order::where('user_id',Auth::user()->id)->orderBy('created_at','DESC')->paginate(10);
        return view('user.orders',compact('orders'));
    }

    public function account_order_details($order_id)
    {
        $order = Order::where('user_id',Auth::user()->id)->find($order_id);        
        $orderItems = OrderItem::where('order_id',$order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id',$order_id)->first();
        return view('user.order-details',compact('order','orderItems','transaction'));
    }

    public function account_cancel_order(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = "canceled";
        $order->canceled_date = Carbon::now();
        $order->save();
        return back()->with("status", "Order has been cancelled successfully!");
    }

    public function address()
    {
        $addresses = Address::where('user_id', auth()->id())->get();
        return view('user.address', compact('addresses'));
    }


    public function address_add()
    {
        return view('user.address_add');
    }

    public function address_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'locality' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'landmark' => 'required',
            'zip' => 'required',
            'type' => 'required',
        ]);

        $address = new Address();
        $address->user_id = auth()->id();
        $address->name = $request->name;
        $address->phone = $request->phone;
        $address->locality = $request->locality;
        $address->address = $request->address;
        $address->city = $request->city;
        $address->state = $request->state;
        $address->country = $request->country;
        $address->landmark = $request->landmark;
        $address->zip = $request->zip;
        $address->type = $request->type;
        $address->isdefault = $request->has('isdefault') ? true : false;
        $address->save();
        return redirect()->route('user.address')->with('status', 'Address has been added successfully!');
    }

    public function address_edit($id)
    {
        $address = Address::where('user_id', auth()->id())->where('id', $id)->firstOrFail();
        return view('user.address_edit', compact('address'));
    }

    public function address_update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'locality' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'landmark' => 'required',
            'zip' => 'required',
            'type' => 'required',
        ]);

        $address = Address::where('user_id', auth()->id())->where('id', $id)->firstOrFail();

        $address->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'locality' => $request->locality,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'landmark' => $request->landmark,
            'zip' => $request->zip,
            'type' => $request->type,
            'isdefault' => $request->has('isdefault') ? true : false,
        ]);

        return redirect()->route('user.address')->with('status', 'Address updated successfully!');
    }

    
}
