<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\PawoonService;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

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
    
        // Retrieve quantity and notes from the request
        $quantity = $request->input('quantity', 1); // Default to 1 if not provided
        $notes = $request->input('notes', ''); // Default to empty string if not provided
    
        if (isset($cart[$id])) {
            // If the product is already in the cart, update the quantity
            $cart[$id]['quantity'] += $quantity;
            // Append notes to existing notes, you might want to handle this differently
            $cart[$id]['notes'] .= "\n" . $notes;
        } else {
            // If the product is not in the cart, add it with the provided quantity and notes
            $cart[$id] = [
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'image_url' => $request->input('image_url'),
                'quantity' => $quantity,
                'notes' => $notes
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
    \Log::info('Checkout process started.'); // Added log

    $request->validate([
        'customer_name' => ['required', 'string', 'max:255'],
        'customer_email' => ['required', 'email', 'max:255'],
        'customer_phone' => ['required', 'string', 'max:20'],
    ]);

    $cart = session()->get('cart', []);

    if (empty($cart)) {
        \Log::info('Cart is empty. Redirecting back.'); // Added log
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
            'name' => $item['name'],
            'qty' => $item['quantity'],
            'notes' => $item['notes'], // Use notes from cart
            'price' => $item['price']
        ];
    }

    // Hitung pajak dan service
    $taxAmount = $total * 0.10;
    $serviceAmount = $total * 0.02;
    $total += $taxAmount;
    $receiptOrder = 'Order-' . substr(md5(Str::uuid()), 0, 6);

    $receiptData = [
        'receipt_code' => $receiptOrder,
        'order_time' => Carbon::now(),
        'outlet_name' => 'Baraja Amphitheater',
        'table_number' => $table,
        'customer_name' => $request->input('customer_name', 'Pelanggan'),
        'items' => $items,
        'tax' => $taxAmount,
        'total' => $total,
        'qr_code' => QrCode::size(200)->generate($receiptOrder),
        'message' => 'Silahkan bayar ke kasir dan tunjukan QR code-nya'
    ];

    session(['receipt' => $receiptData]);

    // Data pesanan untuk Pawoon
    $orderData = [
        'receipt_code' => $receiptOrder,
        'outlet_id' =>env('Outlet_id'),
        'order_time' => \Carbon\Carbon::now()->toIso8601String(),
        'customer_name' => $request->input('customer_name'),
        'customer_email' => $request->input('customer_email'),
        'customer_phone' => $request->input('customer_phone'),
        'notes' => 'Nomor Meja ' . $table,
        'company_sales_type_id' => env('company_sales_type_id'),
        'items' => $items,
        'taxes' => [[
            'tax_id' => env('Tax_id'),
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
    
    // Kirim halaman view yang menampilkan QR Code dan pesan
    return view('pdf.receipt', $receiptData);

    try {
        // $this->pawoon->createOrder($orderData);
        // Simpan PDF seperti sebelumnya
        $receiptData = session('receipt');
        $receiptCode = $receiptData['receipt_code'];
    
        // Hapus cart dan receipt dari session
        session()->forget(['cart', 'receipt']);
    
 
    
    } catch (\Exception $e) {
        \Log::error('Error during checkout: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        return redirect()->route('cart.index', [$outlet, $table])->with('error', 'Gagal mengirim pesanan: ' . $e->getMessage());
    }
    
}

public function showReceipt($code)
{
    $receiptData = [
        'receipt_code' => $code,
        'customer_name' => 'Pelanggan',
        'table_number' => 'XX',
        'order_time' => now(),
        'items' => [],
        'tax' => 0,
        'total' => 0,
        'qr_code' => QrCode::size(200)->generate($code),
        'message' => 'Silahkan bayar ke kasir dan tunjukan QR code-nya'
    ];

    return view('pdf.receipt', $receiptData);
}


}