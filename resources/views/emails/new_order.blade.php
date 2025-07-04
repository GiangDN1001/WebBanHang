<h2>Thông báo đơn hàng mới</h2>
<p>Khách hàng: {{ $order->name }}</p>
<p>SĐT: {{ $order->phone }}</p>
<p>Địa chỉ: {{ $order->address }}, {{ $order->city }}, {{ $order->state }}</p>
<p>Tổng tiền: {{ number_format($order->total, 0, ',', '.') }}đ</p>
<p>Ngày đặt hàng: {{ $order->created_at->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</p>

