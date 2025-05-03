<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Produk Pawoon</title>
</head>
<body>
    <h1>Daftar Kategori Produk</h1>

    <ul>
        @foreach ($categories['data'] as $category)
            <li>
                <a href="{{ url('/products?category_id=' . $category['id']) }}">
                    {{ $category['name'] }}
                </a>
            </li>
        @endforeach
    </ul>
</body>
</html>
