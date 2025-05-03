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
    
        $page = $request->query('page', 1);
        $perPage = 25;
        $categoryId = $request->query('category_id');
    
        $productsData = $this->pawoon->getProducts($page, $perPage, $categoryId);
        $products = $productsData['data'] ?? [];
        $meta = $productsData['meta'] ?? [];
    
        $categoriesData = $this->pawoon->getCategories();
        $categories = $categoriesData['data'] ?? [];
    
        return view('products.index', compact('products', 'meta', 'categories', 'categoryId', 'outlet', 'table'));
    }
}