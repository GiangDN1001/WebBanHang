@extends('layouts.app')
@section('content')
<style>
    .table-transaction>tbody>tr:nth-of-type(odd) {
        --bs-table-accent-bg: #fff !important;
        
    }    
    .table-transaction th, .table-transaction td {
        padding: 0.625rem 1.5rem .25rem; !important;
        color:#000 !important;        
    }
    .table > :not(caption) > tr > th {
    padding: 0.625rem 1.5rem .25rem !important;
    background-color: #6a6e51 !important;   
}
    .table-bordered>:not(caption)>*>*{border-width:inherit;line-height:32px;font-size:14px;border:1px solid #e1e1e1;vertical-align:middle;}
.table-striped .image{display:flex;align-items:center;justify-content:center;width:50px;height:50px;flex-shrink:0;border-radius:10px;overflow:hidden;}
.table-striped  td:nth-child(1){min-width:250px;padding-bottom:7px;}
.pname{display:flex;gap:13px;}
.table-bordered > :not(caption) > tr > th, .table-bordered > :not(caption) > tr > td {
    border-width: 1px 1px;
    border-color: #6a6e51;
}
    
</style>
@section('title', 'Order_details')

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
        <h2 class="page-title">Chi tiết đơn hàng</h2>
        <div class="row">
            <div class="col-lg-2">
                @include('user.account-nav')
            </div>
            <div class="col-lg-10">
                @if(Session::has('status'))
                    <p class="alert alert-success">{{Session::get('status')}}</p>
                @endif
                <div class="wg-box mt-5 mb-5">
                    <div class="row">
                        <div class="col-6">
                            <h5>Thông tin đơn hàng</h5>
                        </div>
                        <div class="col-6 text-right">
                            <a class="btn btn-sm btn-danger" href="{{route('user.account.orders')}}">Quay lại</a>
                        </div>
                    </div>                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-transaction">
                            <tr>
                                <th>Mã đơn hàng</th>
                                <td>{{"1" . str_pad($transaction->order->id,4,"0",STR_PAD_LEFT)}}</td>
                                <th>Số điện thoại</th>
                                <td>{{$transaction->order->phone}}</td>
                                {{-- <th>Mã bưu điện</th> --}}
                                {{-- <td>{{$transaction->order->zip}}</td> --}}
                            </tr>
                            <tr>
                                <th>Ngày đặt hàng</th>
                                <td>{{$transaction->order->created_at}}</td>
                                <th>Ngày giao hàng</th>
                                <td>{{$transaction->order->delivered_date}}</td>
                                <th>Ngày hủy</th>
                                <td>{{$transaction->order->canceled_date}}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái đơn hàng</th>
                                <td colspan="5">
                                    @if($transaction->order->status=='delivered')
                                        <span class="badge bg-success">Đã giao</span>
                                    @elseif($transaction->order->status=='canceled')
                                        <span class="badge bg-danger">Đã hủy</span>
                                    @else
                                        <span class="badge bg-warning">Đã đặt</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="wg-box wg-table table-all-user">
                    <div class="row">
                        <div class="col-6">
                            <h5>Các mặt hàng đã đặt</h5>
                        </div>
                        <div class="col-6 text-right">
                            
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th class="text-center">Giá</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-center">Mã sản phẩm</th>
                                    <th class="text-center">Danh mục</th>
                                    <th class="text-center">Thương hiệu</th>                                                        
                                    {{-- <th class="text-center">Tùy chọn</th> --}}
                                    <th class="text-center">Trạng thái trả hàng</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderItems as $orderitem)
                                <tr>
                                    
                                    <td class="pname">
                                        <div class="image">
                                            <img src="{{asset('Uploads/products/thumbnails')}}/{{$orderitem->product->image}}" alt="" class="image">
                                        </div>
                                        <div class="name">
                                            <a href="{{route('shop.product.details',["product_slug"=>$orderitem->product->slug])}}" target="_blank" class="body-title-2">{{$orderitem->product->name}}</a>                                    
                                        </div>  
                                    </td>
                                    <td class="text-center">{{number_format($orderitem->price,0,'.','.')}}₫</td>
                                    <td class="text-center">{{$orderitem->quantity}}</td>
                                    <td class="text-center">{{$orderitem->product->SKU}}</td>
                                    <td class="text-center">{{$orderitem->product->category->name}}</td>
                                    <td class="text-center">{{$orderitem->product->brand->name}}</td>
                                    {{-- <td class="text-center">{{$orderitem->options}}</td> --}}
                                    <td class="text-center">{{$orderitem->rstatus == 0 ? "Không":"Có"}}</td>                                                                                
                                    <td class="text-center">
                                        <a href="{{route('shop.product.details',["product_slug"=>$orderitem->product->slug])}}" target="_blank">
                                            <div class="list-icon-function view-icon">
                                                <div class="item eye">
                                                    <i class="fa fa-eye"></i>
                                                </div>                                                                    
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach                                  
                            </tbody>
                        </table>               
                    </div>
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">                
                    {{$orderItems->links('pagination::bootstrap-5')}}
                </div>
                <div class="wg-box mt-5">
                    <h5>Địa chỉ giao hàng</h5>
                    <div class="my-account__address-item col-md-6">                
                        <div class="my-account__address-item__detail">
                            <p>{{$transaction->order->name}}</p>
                            <p>{{$transaction->order->address}}</p>
                            <p>{{$transaction->order->locality}}</p>
                            <p>{{$transaction->order->city}}, {{$transaction->order->country}}</p>
                            <p>{{$transaction->order->landmark}}</p>
                            {{-- <p>{{$transaction->order->zip}}</p> --}}
                            <br />                                
                            <p>Số điện thoại: {{$transaction->order->phone}}</p>
                        </div>
                    </div>              
                </div>
                <div class="wg-box mt-5">
                    <h5>Giao dịch</h5>
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-transaction">
                        <tr>
                            <th>Tạm tính</th>
                            {{-- <td>${{$transaction->order->subtotal}}</td> --}}
                            {{-- <th>Thuế</th> --}}
                            {{-- <td>${{$transaction->order->tax}}</td> --}}
                            <th>Giảm giá</th>
                            <td>{{number_format($transaction->order->discount,0,'.','.')}}₫</td>
                        </tr>
                        <tr>
                            <th>Tổng cộng</th>
                            <td>{{number_format($transaction->order->total,0,'.','.')}}₫</td>
                            <th>Phương thức thanh toán</th>
                            <td>{{$transaction->mode}}</td>
                            <th>Trạng thái</th>
                            <td>
                                @if($transaction->status=='approved')
                                    <span class="badge bg-success">Đã duyệt</span>
                                @elseif($transaction->status=='declined')
                                    <span class="badge bg-danger">Bị từ chối</span>
                                @elseif($transaction->status=='refunded')
                                    <span class="badge bg-secondary">Đã hoàn tiền</span>
                                @else
                                    <span class="badge bg-warning">Đang chờ</span>
                                @endif
                            </td>
                        </tr>                        
                    </table>
                    </div>
                </div>  
                <div class="wg-box mt-5 text-right">                    
                    <form action="{{route('user.account_cancel_order')}}" method="POST">
                        @csrf
                        @method("PUT")
                        <input type="hidden" name="order_id" value="{{$order->id}}" />
                        <button type="submit" class="btn btn-danger">Hủy đơn hàng</button>                        
                    </form>
                </div>              
            </div>            
        </div>
    </section>
</main>
@endsection