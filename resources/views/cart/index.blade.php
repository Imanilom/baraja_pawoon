@extends('layouts.app')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .title-highlight {
            color: #005429;
            font-weight: 600;
        }

        .btn-coffee {
            background: linear-gradient(135deg, #005429, #007a3d);
            color: white;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-coffee:hover {
            background: linear-gradient(135deg, #007a3d, #005429);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card-custom {
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        }

        .cart-item {
            padding: 1rem;
        }

        .cart-item h5 {
            font-size: 1.1rem;
            margin-bottom: 0.4rem;
        }

        .cart-item p {
            margin-bottom: 0.3rem;
        }

        .total-box {
            background: #e8f5e9;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1.5rem;
            text-align: right;
        }

        .total-box strong {
            font-size: 1.25rem;
        }

        @media (min-width: 768px) {
            .cart-table {
                display: block;
            }

            .cart-mobile {
                display: none;
            }
        }

        @media (max-width: 767px) {
            .cart-table {
                display: none;
            }

            .cart-mobile {
                display: block;
            }

            .btn-coffee {
                width: 100%;
            }
        }

        .checkout-floating {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            display: none;
        }

        @media (max-width: 767px) {
            .checkout-floating {
                display: block;
            }
        }
    </style>

    <div class="container my-5">
        <h2 class="text-center mb-4 title-highlight">🛒 Keranjang Belanja</h2>

        {{-- Informasi Outlet dan Meja --}}
        @if (isset($outlet) && isset($table))
            <div class="alert alert-success text-center shadow-sm rounded-3 py-2">
                <strong>Outlet:</strong> Baraja Amphitheater | <strong>Meja:</strong> {{ $table }}
            </div>
        @endif

        {{-- Notifikasi Sukses --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Daftar Produk di Keranjang --}}
        @if (count($cart) > 0)
            {{-- Versi Mobile --}}
            <div class="cart-mobile">
                @php $grand_total = 0; @endphp
                @foreach ($cart as $id => $item)
                    @php 
                        $total = $item['price'] * $item['quantity'];
                        $grand_total += $total; 
                    @endphp
                    <div class="card card-custom mb-3">
                        <div class="row g-0">
                            <div class="col-4 d-flex align-items-center justify-content-center">
                                <img src="{{ $item['image_url'] ?? 'https://placehold.co/600x400@2x.png' }}" 
                                     alt="{{ $item['name'] }}" 
                                     class="img-fluid rounded-start" style="height: 100px; object-fit: cover;">
                            </div>
                            <div class="col-8">
                                <div class="card-body p-2">
                                    <h5 class="card-title mb-1">{{ $item['name'] }}</h5>
                                    <p class="mb-1">Harga: <strong>Rp {{ number_format($item['price'], 0, ',', '.') }}</strong></p>
                                    <p class="mb-1">Jumlah: {{ $item['quantity'] }}</p>
                                    <p class="mb-2">Total: <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></p>
                                    <form method="POST" action="{{ route('cart.remove', [$outlet ?? 0, $table ?? 0]) }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <button type="submit" class="btn btn-sm btn-danger w-100">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="total-box">
                    <strong>Total Semua: Rp {{ number_format($grand_total, 0, ',', '.') }}</strong>
                </div>
            </div>

            {{-- Versi Desktop --}}
            <div class="cart-table">
                <div class="card card-custom shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grand_total = 0; @endphp
                                    @foreach ($cart as $id => $item)
                                        @php 
                                            $total = $item['price'] * $item['quantity'];
                                            $grand_total += $total; 
                                        @endphp
                                        <tr class="align-middle">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $item['image_url'] ?? 'https://placehold.co/600x400@2x.png' }}" 
                                                         alt="{{ $item['name'] }}" 
                                                         class="rounded me-3" 
                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                    <span><strong>{{ $item['name'] }}</strong></span>
                                                </div>
                                            </td>
                                            <td>Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                            <td>{{ $item['quantity'] }}</td>
                                            <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('cart.remove', [$outlet ?? 0, $table ?? 0]) }}">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $id }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-success">
                                        <td colspan="3" class="text-end fw-bold">Total:</td>
                                        <td colspan="2" class="title-highlight fw-bold">
                                            Rp {{ number_format($grand_total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Checkout Floating untuk Mobile --}}
            <div class="checkout-floating">
                <a href="{{ route('cart.checkout', [$outlet, $table]) }}" class="btn btn-coffee btn-lg rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-cart-check" style="font-size: 1.5rem;"></i>
                </a>
            </div>
        @else
            <div class="alert alert-warning text-center py-4">
                <i class="bi bi-cart-x" style="font-size: 2rem;"></i>
                <p class="mt-2 mb-0">Keranjang kamu masih kosong. ☕</p>
            </div>
        @endif

        {{-- Tombol Navigasi --}}
        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mt-4">
            @if (isset($outlet) && isset($table))
                <a href="{{ route('products.index', [$outlet, $table]) }}" class="btn btn-outline-secondary btn-lg w-100 w-md-auto rounded-pill">
                    ← Lanjut Belanja
                </a>
                <a href="{{ route('cart.checkout', [$outlet, $table]) }}" class="btn btn-coffee btn-lg w-100 w-md-auto rounded-pill">
                    Checkout Sekarang →
                </a>
            @else
                <div class="alert alert-danger text-center mb-0">
                    Tidak dapat melanjutkan. Outlet atau nomor meja tidak ditemukan.
                </div>
            @endif
        </div>
    </div>
@endsection