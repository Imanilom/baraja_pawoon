<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PawoonService;

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

        // Fetch all products
        $allProducts = [];
        $page = 1;
        $perPage = 25; // Or whatever your API's page size is

        do {
            $productsData = $this->pawoon->getProducts($page, $perPage, $categoryId);
            $products = isset($productsData['data']) ? $productsData['data'] : [];

            $allProducts = array_merge($allProducts, $products);

            $meta = $productsData['meta'] ?? [];
            $totalPages = ceil($meta['total'] / $meta['per_page']);

            $page++;
        } while ($page <= $totalPages);

        // Filter products by category ID
        if ($categoryId) {
            $allProducts = collect($allProducts)->filter(function ($product) use ($categoryId) {
                return isset($product['product_category_id']) && $product['product_category_id'] == $categoryId;
            })->values()->toArray();
        }

        $categoriesData = $this->pawoon->getCategories();
        $categories = isset($categoriesData['data']) ? $categoriesData['data'] : [];

      //Re-paginate the array
        $currentPage = $request->get('page', 1);
        $perPage = 25;
        $offset = ($currentPage - 1) * $perPage;
        $itemsForCurrentPage = array_slice($allProducts, $offset, $perPage, true);
        $products = new \Illuminate\Pagination\LengthAwarePaginator($itemsForCurrentPage, count($allProducts), $perPage, $currentPage, ['path' => $request->url(), 'query' => $request->query()]);
        
        return view('products.index', compact('products', 'categories', 'categoryId', 'outlet', 'table'));
    }
}