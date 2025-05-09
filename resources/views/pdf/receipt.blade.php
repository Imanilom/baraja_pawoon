<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
</head>
<body>
    <h2>Terima kasih {{ $customer_name }}</h2>
    <p>Kode Pesanan: <strong>{{ $receipt_code }}</strong></p>
    <p>Meja: {{ $table_number }}</p>
    <p>Waktu: {{ $order_time }}</p>
    <hr>
    <ul>
        @foreach($items as $item)
            <li>{{ $item['qty'] }}x Produk ID {{ $item['product_id'] }} - Rp{{ number_format($item['price'], 0) }}</li>
        @endforeach
    </ul>
    <p>Pajak: Rp{{ number_format($tax, 0) }}</p>
    <p>Total: <strong>Rp{{ number_format($total, 0) }}</strong></p>

    <hr>
    <p>{{ $message }}</p>
    <div>
        {!! $qr_code !!}
    </div>
</body>
</html>
