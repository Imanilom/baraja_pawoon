<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PawoonService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    protected $pawoon;

    public function __construct()
    {
        $this->pawoon = new PawoonService();
    }

    public function index($outlet, $table, Request $request)
    {
        $categoryId = $request->query('category_id');
        $page = Paginator::resolveCurrentPage() ?? 1;
        $perPage = 6;
        $cacheKey = 'products_page_' . $page . '_category_' . $categoryId;

        $products = Cache::remember($cacheKey, 60, function () use ($categoryId, $perPage, $request, $page) { // Use $page
            $allProducts = $this->getAllProductsFromApi($categoryId); // Get all products from API
            $offset = ($page - 1) * $perPage;
            $itemsForCurrentPage = array_slice($allProducts, $offset, $perPage, true);
            return new \Illuminate\Pagination\LengthAwarePaginator($itemsForCurrentPage, count(
                $allProducts
            ), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
        });

        $categoriesData = $this->getCategoriesFromApi(); // Get categories from API
        $categories = $categoriesData['data'] ?? [];

        return view('products.index', compact('products', 'categories', 'categoryId', 'outlet', 'table'));
    }

    private function getAllProductsFromApi($categoryId = null)
    {
        $cacheKey = 'all_products_category_' . $categoryId;
        return Cache::remember($cacheKey, 60, function () use ($categoryId) {
            $allProducts = [];
            $page = 1;
            $perPage = 25;

            do {
                $productsData = $this->pawoon->getProducts($page, $perPage, $categoryId);
                $products = isset($productsData['data']) ? $productsData['data'] : [];
                $allProducts = array_merge($allProducts, $products);
                $meta = $productsData['meta'] ?? [];
                $totalPages = ceil($meta['total'] / $meta['per_page']);
                $page++;
            } while ($page <= $totalPages);

            // Filter products by category ID (move filtering inside the cache)
            if ($categoryId) {
                $allProducts = collect($allProducts)->filter(function ($product) use ($categoryId) {
                    return isset($product['product_category_id']) && $product['product_category_id'] == $categoryId;
                })->values()->toArray();
            }

            return $allProducts;
        });
    }

    private function getCategoriesFromApi()
    {
        $cacheKey = 'all_categories';
        return Cache::remember($cacheKey, 60, function () {
            return $this->pawoon->getCategories();
        });
    }


}