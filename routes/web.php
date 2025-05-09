<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
/*
|-------------------------------------------------------------------------- 
| Web Routes
|-------------------------------------------------------------------------- 
| 
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
| 
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-pawoon-products', function (\App\Services\PawoonService $pawoonService) {
    return response()->json($pawoonService->getCompanySalestype());
});


Route::prefix('/{outlet}/{table}')->group(function () {
    // Daftar produk
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // Tambah ke keranjang
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

    // Lihat keranjang
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

    // Tampilkan form checkout
    Route::get('/cart/checkout', [CartController::class, 'showCheckoutForm'])->name('cart.checkout.form');

    // Proses checkout setelah form di-submit
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // Hapus dari keranjang
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    
    Route::get('/receipt/{code}', [CartController::class, 'showReceipt'])->name('receipt.show');

});
