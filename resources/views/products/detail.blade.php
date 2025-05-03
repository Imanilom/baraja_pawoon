<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Produk</title>
</head>
<body>
    <h1>{{ $product['name'] }}</h1>

    <p>Harga: {{ $product['price'] ?? 'N/A' }}</p>
    <p>Deskripsi: {{ $product['description'] ?? 'Tidak ada deskripsi' }}</p>

    <a href="{{ url()->previous() }}"><< Kembali</a>
</body>
</html>
