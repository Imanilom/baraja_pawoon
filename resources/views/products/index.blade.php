@extends('layouts.app')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fdfcfb;
        }

        .card:hover {
            transform: translateY(-5px);
            transition: 0.3s ease;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .title-highlight {
            color: #005429;
        }

        .btn-coffee {
            background-color: #005429;
            color: white;
        }

        .btn-coffee:hover {
            background-color: rgb(2, 66, 33);
        }
    </style>

    <div class="container py-5">

        {{-- Judul --}}
        <h2 class="text-center mb-3 title-highlight">☕ Menu Spesial Kami Hari Ini</h2>

        {{-- Informasi outlet dan meja jika tersedia --}}
        @if (isset($outlet) && isset($table))
            <div class="alert alert-success text-center">
                <strong>Outlet:</strong> Baraja Amphitheater | <strong>Meja:</strong> {{ $table }}
            </div>
        @endif

        {{-- Notifikasi sukses atau error --}}
        @if(session('success'))
            <div class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger text-center">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter Kategori --}}
        <form method="GET" class="mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <select name="category_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($categories as $category)
                            @continue(in_array($category['id'], [
                                'b997b2f0-2866-11ea-ab2f-b913a3fa2965',
                                '9c788a20-387a-11eb-9172-ad7cf7a080e6',
                                '5b65e960-724e-11ed-81dc-51a98550262c',
                                'a9bdba20-96ff-11ed-9042-e36d004ccaea',
                                '0c151b80-56ba-11ee-9653-d1f7f41051cc',
                                '5b65e960-724e-11ed-81dc-51a98b3c20cb',
                            ]))
                            <option value="{{ $category['id'] }}" {{ $categoryId == $category['id'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        {{-- Badge kategori aktif --}}
        @if ($categoryId)
            @php
                $activeCategory = collect($categories)->firstWhere('id', $categoryId);
            @endphp
            @if ($activeCategory)
                <div class="text-center mb-4">
                    <span class="badge bg-success px-3 py-2">
                        Kategori: {{ $activeCategory['name'] }}
                    </span>
                </div>
            @endif
        @endif

        {{-- Info jumlah produk --}}
        @if (!empty($meta))
            <p class="text-center text-muted">
                Menampilkan {{ count($products) }} dari total {{ $meta['total'] }} produk.
            </p>
        @endif

        {{-- Daftar Produk --}}
        <div class="row">
            @forelse ($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 rounded-4">
                        @if (!empty($product['image_url']))
                            <img src="{{ $product['image_url'] }}" class="card-img-top rounded-top-4 img-fluid" alt="{{ $product['name'] }}">
                        @else
                            <img src="https://placehold.co/600x400@2x.png" class="card-img-top rounded-top-4 img-fluid" alt="No Image">
                        @endif

                        <div class="card-body">
                            <h5 class="card-title title-highlight">{{ $product['name'] }}</h5>
                            <p class="card-text text-muted">{{ $product['description'] ?? 'Tidak ada deskripsi.' }}</p>
                        </div>

                        <div class="card-footer bg-light border-0">
                            <strong class="title-highlight">Harga: Rp {{ number_format($product['price'], 0, ',', '.') }}</strong>
                        </div>

                        <form method="POST" action="{{ route('cart.add', [$outlet, $table]) }}" class="p-3 pt-0">
                            @csrf
                            <input type="hidden" name="id" value="{{ $product['id'] }}">
                            <input type="hidden" name="name" value="{{ $product['name'] }}">
                            <input type="hidden" name="price" value="{{ $product['price'] }}">
                            <input type="hidden" name="image_url" value="{{ $product['image_url'] ?? '' }}">
                            <button type="submit" class="btn btn-sm w-100 btn-coffee mt-2">
                                ☕ Tambah ke Keranjang
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center">Tidak ada produk ditemukan.</p>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if (!empty($meta))
            @php
                $currentPage = $meta['page'];
                $totalPages = ceil($meta['total'] / $meta['per_page']);
            @endphp

            <nav>
                <ul class="pagination justify-content-center mt-4">
                    <li class="page-item {{ $currentPage <= 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="?page={{ $currentPage - 1 }}{{ $categoryId ? '&category_id=' . $categoryId : '' }}">← Sebelumnya</a>
                    </li>

                    @for ($i = 1; $i <= $totalPages; $i++)
                        <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                            <a class="page-link" href="?page={{ $i }}{{ $categoryId ? '&category_id=' . $categoryId : '' }}">{{ $i }}</a>
                        </li>
                    @endfor

                    <li class="page-item {{ $currentPage >= $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="?page={{ $currentPage + 1 }}{{ $categoryId ? '&category_id=' . $categoryId : '' }}">Berikutnya →</a>
                    </li>
                </ul>
            </nav>
        @endif
    </div>
@endsection