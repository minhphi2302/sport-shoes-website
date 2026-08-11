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
        $featuredProducts = $this->productModel->getFeatured(6);
        $categories = $this->categoryModel->all();
        
        $this->view('client/home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories
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
        if (!empty($_GET['gender'])) {
            $filters['gender'] = trim($_GET['gender']);
        }
        if (!empty($_GET['size'])) {
            $filters['size'] = trim($_GET['size']);
        }
        if (!empty($_GET['color'])) {
            $filters['color'] = trim($_GET['color']);
        }

        $products = $this->productModel->findAllWithFilters($filters, $page, $perPage);
        $totalProducts = $this->productModel->countAllWithFilters($filters);
        $totalPages = ceil($totalProducts / $perPage);

        $categories = $this->categoryModel->all();
        $brands = $this->brandModel->all();

        // Nếu là AJAX request (lọc / phân trang), chỉ trả về partial product_grid
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // Expose vars needed by product_grid.php
            $currentPageNum = $page;
            extract([
                'products'      => $products,
                'totalProducts' => $totalProducts,
                'totalPages'    => $totalPages,
                'currentPageNum'=> $page,
                'filters'       => $filters,
            ]);
            ob_start();
            require dirname(__DIR__, 2) . '/views/client/partials/product_grid.php';
            $html = ob_get_clean();
            header('Content-Type: text/html; charset=UTF-8');
            echo $html;
            exit;
        }

        $this->view('client/product_list', [
            'products'       => $products,
            'categories'     => $categories,
            'brands'         => $brands,
            'currentPage'    => $page,
            'currentPageNum' => $page,      // dùng bởi product_grid.php pagination
            'totalPages'     => $totalPages,
            'totalProducts'  => $totalProducts,
            'filters'        => $filters,
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
