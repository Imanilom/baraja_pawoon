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

        /* Styles for the bottom sheet modal */
        .bottom-sheet {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: white;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 20px;
            transition: transform 0.3s ease-in-out;
            transform: translateY(100%); /* Hidden by default */
        }

        .bottom-sheet.show {
            transform: translateY(0); /* Show the modal */
        }

        .bottom-sheet-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .close-button {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }

        .product-image {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }

        .product-details {
            flex-grow: 1;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .quantity-controls button {
            background-color: #ddd;
            border: none;
            padding: 5px 10px;
            margin: 0 5px;
            cursor: pointer;
            border-radius: 5px;
        }

        .quantity-controls input {
            width: 50px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px;
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 576px) {
            .product-image {
                max-width: 70px;
                max-height: 70px;
            }
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
            <div class="row g-2 align-items-center justify-content-center">
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
                            <option value="{{ $category['id'] }}" {{ request('category_id') == $category['id'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        {{-- Badge kategori aktif --}}
        @if (request('category_id'))
            @php
                $activeCategory = collect($categories)->firstWhere('id', request('category_id'));
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
                        @if (!empty($product['image']))
                            <img src="{{ $product['image'] }}" class="card-img-top rounded-top-4 img-fluid" alt="{{ $product['name'] }}">
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

                        <button class="btn btn-sm w-100 btn-coffee mt-2 add-to-cart-btn"
                                data-product-id="{{ $product['id'] }}"
                                data-product-name="{{ $product['name'] }}"
                                data-product-price="{{ $product['price'] }}"
                                data-product-image="{{ $product['image'] ?? 'https://placehold.co/600x400@2x.png' }}">
                            ☕ Tambah ke Keranjang
                        </button>
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
                $categoryId = request()->get('category_id');
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

        {{-- Bottom Sheet Modal --}}
        <div class="bottom-sheet" id="cartModal">
            <div class="bottom-sheet-header">
                <h4>Detail Pesanan</h4>
                <button class="close-button" onclick="closeCartModal()">×</button>
            </div>

            <div class="d-flex align-items-center">
                <img src="" alt="Product Image" class="product-image" id="modalProductImage">
                <div class="product-details">
                    <h5 id="modalProductName" class="title-highlight"></h5>
                    <p>Harga: <strong id="modalProductPrice" class="title-highlight"></strong></p>
                    <p>Total: <strong id="modalProductTotal" class="title-highlight"></strong></p>
                </div>
            </div>

            <div class="mb-3">
                <label for="orderNotes" class="form-label">Catatan Pemesanan:</label>
                <textarea class="form-control" id="orderNotes" rows="3" placeholder="Tambahkan catatan khusus untuk pesanan ini"></textarea>
            </div>

            <div class="quantity-controls">
                <button onclick="decreaseQuantity()">-</button>
                <input type="number" id="quantity" value="1" min="1">
                <button onclick="increaseQuantity()">+</button>
            </div>

            <form id="addToCartForm" method="POST" action="{{ route('cart.add', [$outlet, $table]) }}">
                @csrf
                <input type="hidden" name="id" id="modalProductId" value="">
                <input type="hidden" name="name" id="modalProductNameInput" value="">
                <input type="hidden" name="price" id="modalProductPriceInput" value="">
                <input type="hidden" name="image_url" id="modalProductImageInput" value="">
                <input type="hidden" name="quantity" id="quantityInput" value="1">
                <input type="hidden" name="notes" id="notesInput" value="">
                <button type="submit" class="btn btn-coffee w-100 mt-3">Tambah ke Keranjang</button>
            </form>
        </div>
    </div>

    <script>
        let productPrice = 0; // Store the product price

        // Function to show the cart modal
        function showCartModal(productId, productName, price, productImage) {
            productPrice = price; // Store the product price
            document.getElementById('modalProductId').value = productId;
            document.getElementById('modalProductName').innerText = productName;
            document.getElementById('modalProductNameInput').value = productName;
            document.getElementById('modalProductPrice').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
            document.getElementById('modalProductPriceInput').value = price;
            document.getElementById('modalProductImage').src = productImage;
            document.getElementById('modalProductImageInput').value = productImage;

            updateTotal(); // Calculate and display initial total

            document.getElementById('cartModal').classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent scrolling when the modal is open
        }

        // Function to close the cart modal
        function closeCartModal() {
            document.getElementById('cartModal').classList.remove('show');
            document.body.style.overflow = 'auto'; // Enable scrolling
        }

        // Event listeners for "Add to Cart" buttons
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default form submission

                const productId = this.dataset.productId;
                const productName = this.dataset.productName;
                const productPrice = parseFloat(this.dataset.productPrice); // Parse as float
                const productImage = this.dataset.productImage;

                showCartModal(productId, productName, productPrice, productImage);
            });
        });

        // Quantity controls functions
        function increaseQuantity() {
            let quantityInput = document.getElementById('quantity');
            let quantity = parseInt(quantityInput.value);
            quantityInput.value = quantity + 1;
            updateQuantityInput();
            updateTotal(); // Update total on quantity change
        }

        function decreaseQuantity() {
            let quantityInput = document.getElementById('quantity');
            let quantity = parseInt(quantityInput.value);
            if (quantity > 1) {
                quantityInput.value = quantity - 1;
                updateQuantityInput();
                updateTotal(); // Update total on quantity change
            }
        }

        function updateQuantityInput() {
            document.getElementById('quantityInput').value = document.getElementById('quantity').value;
        }

        // Function to update the total price
        function updateTotal() {
            let quantity = parseInt(document.getElementById('quantity').value);
            let total = productPrice * quantity;
            document.getElementById('modalProductTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        }

        // Event listener for form submission
        document.getElementById('addToCartForm').addEventListener('submit', function() {
            document.getElementById('notesInput').value = document.getElementById('orderNotes').value;
            updateQuantityInput(); // Ensure quantity is updated on form submission
            closeCartModal(); // Close the modal after submission
        });
    </script>
@endsection