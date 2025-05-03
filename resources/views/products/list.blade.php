<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Produk</title>
</head>
<body>
    <h1>Produk</h1>

    <a href="{{ url('/') }}"><< Kembali ke Kategori</a>

    <ul>
        @foreach ($products['data'] as $product)
            <li>
                <a href="{{ url('/products/' . $product['id']) }}">
                    {{ $product['name'] }}
                </a>
            </li>
        @endforeach
    </ul>
</body>
</html>
