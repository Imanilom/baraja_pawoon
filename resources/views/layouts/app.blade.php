<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Baraja Amphitheater - Coffee Shop & Event Space">
    <title>@yield('title', 'Baraja Amphitheater')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        html, body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }

        .navbar-brand {
            font-weight: 700;
            color: #005429 !important;
        }

        .navbar-nav .nav-link {
            color: #333 !important;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #005429 !important;
        }

        .hero {
            background: linear-gradient(135deg, #f8f9fa, #e9f5db);
            padding: 5rem 0;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            color: #005429;
        }

        .hero p {
            font-size: 1.25rem;
            color: #555;
        }

        .footer {
            background-color: #005429;
            color: white;
        }

        .footer a {
            color: #f8f9fa;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .footer .social-icons i {
            font-size: 1.25rem;
            margin: 0 10px;
            color: #f8f9fa;
            transition: color 0.3s ease;
        }

        .footer .social-icons i:hover {
            color: #ffc107;
        }

        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            display: none;
            background: #005429;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 50%;
            font-size: 1.2rem;
        }

        .back-to-top:hover {
            background: #007a3d;
        }

        @media (max-width: 767px) {
            .hero h1 {
                font-size: 2.25rem;
            }

            .hero p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">☕ Baraja Amphitheater</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('products') ? 'active' : '' }}" href="{{ route('products.index', ['outlet' => 'default', 'table' => 'A1']) }}">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('about') ? 'active' : '' }}" href="#">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('contact') ? 'active' : '' }}" href="#">Contact</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section (Opsional, bisa diubah sesuai halaman) -->
    @yield('hero')

    <!-- Main Content -->
    <main class="container mt-5 pt-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container py-5">
            <div class="row text-center text-md-start">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="fw-bold mb-3">Baraja Amphitheater</h5>
                    <p>Experience the perfect blend of coffee and culture in a vibrant amphitheater setting.</p>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="fw-bold mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li><a href="{{ route('products.index', ['outlet' => 'default', 'table' => 'A1']) }}">Products</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold mb-3">Newsletter</h5>
                    <p>Subscribe to get updates on new menu items and events.</p>
                    <form>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Your email" required>
                            <button class="btn btn-coffee" type="submit">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="mb-0">&copy; {{ date('Y') }} Baraja Amphitheater. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" onclick="scrollToTop()"><i class="bi bi-arrow-up"></i></button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Scroll to top functionality
        window.addEventListener('scroll', function () {
            const btn = document.querySelector('.back-to-top');
            btn.style.display = (window.pageYOffset > 300) ? 'block' : 'none';
        });

        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>

    @stack('scripts')
</body>
</html>