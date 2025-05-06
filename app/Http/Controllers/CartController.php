<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\PawoonService;
use Barryvdh\DomPDF\Facade\Pdf;

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
        // Simpan outlet dan table ke session agar bisa dipakai di checkout
        session(['outlet' => $outlet, 'table' => $table]);
    
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
   // app/Http/Controllers/CartController.php

   public function showCheckoutForm($outlet, $table, Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index', [$outlet, $table])->with('error', 'Keranjang kosong!');
        }

        return view('cart.checkout-form', compact('outlet', 'table'));
    }

    public function checkout($outlet, $table, Request $request)
    {
        $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index', [$outlet, $table])->with('error', 'Keranjang kosong!');
        }

        // Hitung total dan item
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

        // Hitung pajak dan service
        $taxAmount = $total * 0.10;
        $serviceAmount = $total * 0.02;
        $total += $taxAmount;
        $receiptOrder = 'Order-' . substr(md5(Str::uuid()), 0, 6);
        // Simpan ke session untuk PDF
        session([
            'receipt' => [
                'receipt_code' => $receiptOrder,
                'order_time' => \Carbon\Carbon::now(),
                'outlet_name' => 'Baraja Amphitheater',
                'table_number' => $table,
                'customer_name' => $request->input('customer_name', 'Pelanggan'),
                'items' => $items,
                'tax' => $taxAmount,
                'total' => $total
            ]
        ]);

        // Data pesanan untuk Pawoon
        $orderData = [
            
            'receipt_code' => $receiptOrder,
            'outlet_id' => "1101ee80-fe3b-11ef-8975-1b84bb569308",
            'order_time' => \Carbon\Carbon::now()->toIso8601String(),
            'customer_name' => $request->input('customer_name'),
            'customer_email' => $request->input('customer_email'),
            'customer_phone' => $request->input('customer_phone'),
            'notes' => 'Nomor Meja ' . $table,
            'company_sales_type_id' => '955bf3c0-ffd5-11ef-88ec-b98f6c9ef958',
            'items' => $items,
            'taxes' => [[
                'tax_id' => '38dfd790-fe3c-11ef-9376-415874e7b927',
                'amount' => round($taxAmount)
            ]],

            'payment' => [
                'amount' => $total,
                'method' => 'cash',
                'company_payment_method_id' => ''
            ],
            'feature_flags' => [
                'order_accepted_type' => 'manual'
            ]
        ];

        try {
            $this->pawoon->createOrder($orderData);
            session()->forget(['cart', 'receipt']);
            // Generate PDF
            $pdf = Pdf::loadView('cart.receipt', session('receipt'));
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $pdf->save(storage_path('app/public/receipts/' . session('receipt')['receipt_code'] . '.pdf'));
            
            return redirect()->route('cart.index', [$outlet, $table])->with('success', 'Pesanan berhasil dikirim!');
        } catch (\Exception $e) {
            return redirect()->route('cart.index', [$outlet, $table])->with('error', 'Gagal mengirim pesanan: ' . $e->getMessage());
        }
    }

}