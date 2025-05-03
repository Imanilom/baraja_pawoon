<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\PawoonService;

class CartController extends Controller
{

    protected $pawoon;

    public function __construct(PawoonService $pawoon)
    {
        $this->pawoon = $pawoon;
    }

    // Menampilkan halaman keranjang
    public function index($outlet, $table, Request $request)
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart', 'outlet', 'table'));
    }
    
    // Menambahkan produk ke keranjang
    public function add($outlet, $table, Request $request)
    {
        $cart = session()->get('cart', []);
    
        $id = $request->input('id');
    
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'image_url' => $request->input('image_url'),
                'quantity' => 1
            ];
        }
    
        session()->put('cart', $cart);
    
        return redirect()->route('cart.index', [$outlet, $table])->with('success', 'Produk ditambahkan ke keranjang!');
    }
    
    public function remove($outlet, $table, Request $request)
    {
        $cart = session()->get('cart', []);
        $id = $request->input('id');
    
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
    
        return redirect()->route('cart.index', [$outlet, $table])->with('success', 'Produk dihapus dari keranjang!');
    }


    // Proses checkout
    public function checkout(Request $request)
    {
        $outletId = $request->query('outlet_id');
        $nomorMeja = $request->query('nomor_meja');

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index', [
                'outlet_id' => $outletId,
                'nomor_meja' => $nomorMeja
            ])->with('error', 'Keranjang kosong!');
        }

        // Hitung total
        $total = 0;
        $items = [];

        foreach ($cart as $productId => $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;

            $items[] = [
                'product_id' => $productId,
                'qty' => $item['quantity'],
                'notes' => 'Tanpa keterangan',
                'price' => $item['price']
            ];
        }

        // Hitung service dan tax
        $taxAmount = $total * 0.10; // 10%
        $serviceAmount = $total * 0.02; // 2%
        $total = $total + $taxAmount + $serviceAmount;

        $orderData = [
            'receipt_code' => 'Order-' . Str::uuid(),
            'outlet_id' => $outletId,
            'order_time' => Carbon::now()->toIso8601String(),
            'customer_name' => $request->input('customer_name', 'Pelanggan'),
            'customer_email' => $request->input('customer_email', 'pelanggan@email.com'),
            'customer_phone' => $request->input('customer_phone', '0800000000'),
            'discount_title' => '',
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'notes' => 'Nomor Meja ' . $nomorMeja,
            'company_sales_type_id' => 'ca6f3a81-fabb-4bff-a383-98aab1995394',
            'items' => $items,
            'taxes' => [
                [
                    'tax_id' => '2d4a3040-2272-11ea-a9fb-efb08550262c',
                    'amount' => round($taxAmount)
                ]
            ],
            'services' => [
                [
                    'service_id' => 'f860ed90-e82c-11ef-bc00-63327bc3c4f0',
                    'amount' => round($serviceAmount)
                ]
            ],
            'payment' => [
                'amount' => $total,
                'method' => 'others',
                'company_payment_method_id' => '1c6a4790-28a2-11ea-a658-954c3cfb97a9'
            ],
            'feature_flags' => [
                'order_accepted_type' => 'manual'
            ]
        ];

        try {
            $response = $this->pawoon->createOrder($orderData);
            session()->forget('cart'); // Bersihkan keranjang
            return redirect()->route('cart.index', [
                'outlet_id' => $outletId,
                'nomor_meja' => $nomorMeja
            ])->with('success', 'Pesanan berhasil dikirim!');
        } catch (\Exception $e) {
            return redirect()->route('cart.index', [
                'outlet_id' => $outletId,
                'nomor_meja' => $nomorMeja
            ])->with('error', 'Gagal mengirim pesanan: ' . $e->getMessage());
        }
    }
}
