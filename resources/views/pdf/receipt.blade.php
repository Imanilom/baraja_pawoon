<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f4;
            padding: 20px;
            color: #333;
            margin: 0;
        }

        .receipt-container {
            background: #fff;
            padding: 20px 25px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            max-width: 600px;
        }

        h2 {
            text-align: center;
            color: #4CAF50;
            font-size: 1.5rem;
        }

        .receipt-info p {
            margin: 5px 0;
            font-size: 1rem;
        }

        ul {
            list-style: none;
            padding-left: 0;
        }

        ul li {
            border-bottom: 1px solid #ddd;
            padding: 8px 0;
            font-size: 0.95rem;
        }

        .total, .tax {
            font-weight: bold;
        }

        .message {
            margin-top: 20px;
            font-style: italic;
            text-align: center;
        }

        .qr-code {
            text-align: center;
            margin-top: 20px;
        }

        .divider {
            border: none;
            border-top: 2px dashed #ccc;
            margin: 20px 0;
        }

        /* Responsive tweaks */
        @media (max-width: 768px) {
            .receipt-container {
                padding: 20px 15px;
                max-width: 95%;
            }

            h2 {
                font-size: 1.25rem;
            }

            .receipt-info p, ul li {
                font-size: 0.95rem;
            }

            .total, .tax {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            h2 {
                font-size: 1.1rem;
            }

            .receipt-info p, ul li {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <h2>Terima kasih, {{ $customer_name }}</h2>

        <div class="receipt-info">
            <p>Kode Pesanan: <strong>{{ $receipt_code }}</strong></p>
            <p>Meja: <strong>{{ $table_number }}</strong></p>
            <p>Waktu: {{ $order_time }}</p>
        </div>

        <hr class="divider">

        <ul>
            @foreach($items as $item)
                <li>{{ $item['qty'] }}x {{ $item['name'] }} - Rp{{ number_format($item['price'], 0) }}</li>
            @endforeach
        </ul>

        <p class="tax">Pajak: Rp{{ number_format($tax, 0) }}</p>
        <p class="total">Total: <strong>Rp{{ number_format($total, 0) }}</strong></p>

        <hr class="divider">

        <p class="message">{{ $message }}</p>

        <div class="qr-code">
            {!! $qr_code !!}
        </div>
    </div>
</body>
</html>
