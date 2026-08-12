<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Size;

class ProductController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;
    private Brand $brandModel;
    private Size $sizeModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->brandModel = new Brand();
        $this->sizeModel = new Size();
    }

    public function home(): void
    {
        $featuredProducts = $this->productModel->getFeatured(8);
        $categories = $this->categoryModel->all();
        $brands = $this->brandModel->all();

        $allProducts = $this->productModel->getFeatured(16);
        $saleProducts = array_values(array_filter($allProducts, function ($p) {
            return !empty($p['sale_price']) && $p['sale_price'] < $p['price'];
        }));
        
        $this->view('client/home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'brands' => $brands,
            'saleProducts' => $saleProducts
        ]);
    }

    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;

        $filters = [];
        if (!empty($_GET['category_id'])) {
            $filters['category_id'] = (int)$_GET['category_id'];
        }
        if (!empty($_GET['brand_id'])) {
            $filters['brand_id'] = (int)$_GET['brand_id'];
        }
        if (!empty($_GET['search'])) {
            $filters['search'] = trim($_GET['search']);
        }

        $products = $this->productModel->findAllWithFilters($filters, $page, $perPage);
        $totalProducts = $this->productModel->countAllWithFilters($filters);
        $totalPages = ceil($totalProducts / $perPage);

        // Nếu request AJAX từ homepage filter (trả về partial grid sản phẩm)
        if (isset($_GET['home']) && $_GET['home'] == '1') {
            $this->view('client/partials/home_product_list', [
                'products' => $products
            ]);
            return;
        }

        $categories = $this->categoryModel->all();
        $brands = $this->brandModel->all();

        $this->view('client/product_list', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'filters' => $filters
        ]);
    }

    public function show($id): void
    {
        $product = $this->productModel->findById((int)$id);
        if (!$product) {
            http_response_code(404);
            die('Sản phẩm không tồn tại.');
        }

        $variants = $this->productModel->getVariants((int)$id);
        $sizes = $this->sizeModel->all();

        $this->view('client/product_detail', [
            'product' => $product,
            'variants' => $variants,
            'sizes' => $sizes
        ]);
    }
}
