@extends('layouts.app')

@section('title', 'Checkout - Baraja Amphitheater')

@section('content')
<div class="container my-5">
    <h2 class="text-center mb-4 title-highlight">📝 Form Checkout</h2>

    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    <div class="card card-custom shadow-sm p-4 mb-4">
        <form action="{{ route('cart.checkout', [$outlet, $table]) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="customer_name" class="form-label">Nama Pelanggan</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Contoh: Adira" required>
            </div>

            <div class="mb-3">
                <label for="customer_email" class="form-label">Email</label>
                <input type="email" class="form-control" id="customer_email" name="customer_email" placeholder="Contoh: adira@email.com" required>
            </div>

            <div class="mb-3">
                <label for="customer_phone" class="form-label">Nomor Telepon</label>
                <input type="text" class="form-control" id="customer_phone" name="customer_phone" placeholder="Contoh: 081234567890" required>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mt-4">
                <a href="{{ route('cart.index', [$outlet, $table]) }}" class="btn btn-outline-secondary w-100 w-md-auto rounded-pill">
                    ← Kembali ke Keranjang
                </a>
                <button type="submit" class="btn btn-coffee w-100 w-md-auto rounded-pill">
                    Lanjut ke Pembayaran →
                </button>
            </div>
        </form>
    </div>
</div>
@endsection