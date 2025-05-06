<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Struk Pesanan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 0; }
        .container { max-width: 400px; margin: 0 auto; padding: 20px; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 14px; color: #555; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { text-align: left; padding: 5px; border-bottom: 1px solid #ddd; }
        .total { font-weight: bold; font-size: 16px; text-align: right; }
        .footer { font-size: 12px; color: #777; text-align: center; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">☕ Baraja Amphitheater</div>
            <div class="subtitle">
                {{ $receipt_code }}<br>
                {{ $order_time->format('d M Y H:i') }}<br>
                Meja: {{ $table_number }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>Rp{{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            Pajak: Rp{{ number_format($tax, 0, ',', '.') }}<br>
            Service: Rp{{ number_format($service, 0, ',', '.') }}<br>
            <strong>Total: Rp{{ number_format($total, 0, ',', '.') }}</strong>
        </div>

        <div class="footer">
            Terima kasih telah memesan!<br>
            Harap simpan struk ini sebagai bukti pesanan.
        </div>
    </div>
</body>
</html>