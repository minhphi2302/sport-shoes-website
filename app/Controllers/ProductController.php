<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

/**
 * Class ProductController
 * Quản lý hiển thị sản phẩm phía Client
 */
class ProductController extends Controller
{
    private ?Product $productModel = null;
    private ?Category $categoryModel = null;
    private ?Brand $brandModel = null;

    public function __construct()
    {
        try {
            $this->productModel = new Product();
            $this->categoryModel = new Category();
            $this->brandModel = new Brand();
        } catch (\Throwable $e) {
            // Trường hợp DB chưa sẵn sàng
        }
    }

    /**
     * Hiển thị Trang chủ
     */
    public function home(): void
    {
        $featuredProducts = $this->productModel ? $this->productModel->getFeaturedProducts(8) : [];
        $saleProducts = $this->productModel ? $this->productModel->getProductsFiltered(['sale' => true], 1, 8) : [];
        $brands = $this->brandModel ? $this->brandModel->getAllBrands() : [];

        $this->view('client/home', [
            'title' => 'Trang chủ — Anta Store',
            'currentPage' => 'home',
            'featuredProducts' => $featuredProducts,
            'saleProducts' => $saleProducts,
            'brands' => $brands
        ]);
    }

    /**
     * Hiển thị Trang danh sách sản phẩm (có lọc & phân trang)
     */
    public function list(): void
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 12;

        $filters = [
            'category_id' => $_GET['category_id'] ?? null,
            'brand_id' => $_GET['brand_id'] ?? null,
            'gender' => $_GET['gender'] ?? null,
            'sale' => $_GET['sale'] ?? null,
            'search' => $_GET['search'] ?? null,
            'sort' => $_GET['sort'] ?? 'newest',
            'size' => $_GET['size'] ?? null,
            'color' => $_GET['color'] ?? null
        ];

        $products = $this->productModel ? $this->productModel->getProductsFiltered($filters, $page, $perPage) : [];
        $totalProducts = $this->productModel ? $this->productModel->countProductsFiltered($filters) : count($products);
        $totalPages = ceil($totalProducts / $perPage);

        $categories = $this->categoryModel ? $this->categoryModel->getAllCategories() : [];
        $brands = $this->brandModel ? $this->brandModel->getAllBrands() : [];

        // Fetch dynamic filters
        $availableSizes = $this->productModel ? $this->productModel->getAllAvailableSizes() : [];
        $availableColors = $this->productModel ? $this->productModel->getAllAvailableColors() : [];
        $availableGenders = $this->productModel ? $this->productModel->getAllAvailableGenders() : [];

        // Check if AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        if ($isAjax) {
            require __DIR__ . '/../Views/client/partials/product_grid.php';
            exit;
        }

        $this->view('client/product_list', [
            'title' => 'Danh sách sản phẩm — Anta',
            'currentPage' => 'products',
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'availableSizes' => $availableSizes,
            'availableColors' => $availableColors,
            'availableGenders' => $availableGenders,
            'currentPageNum' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ]);
    }

    /**
     * Hiển thị Trang chi tiết sản phẩm
     *
     * @param int $id Mã sản phẩm
     */
    public function detail(int $id): void
    {
        $product = $this->productModel ? $this->productModel->getProductById($id) : null;

        if (!$product) {
            // Mẫu fallback nếu chưa kết nối DB
            $product = [
                'product_id' => $id,
                'name' => 'Nike Air Zoom Pegasus 39',
                'sku' => 'NK-RN-00' . $id,
                'price' => 3000000,
                'sale_price' => 2500000,
                'quantity' => 50,
                'brand_name' => 'Nike',
                'category_name' => 'Running',
                'category_id' => 1,
                'description' => 'Mẫu giày chạy bộ bán chạy hàng đầu thế giới với công nghệ Nike Air Zoom êm ái vượt trội.',
                'image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80'
            ];
        }

        $relatedProducts = $this->productModel ? $this->productModel->getRelatedProducts($product['category_id'] ?? 1, $id, 4) : [];
        $variants = $this->productModel ? $this->productModel->getProductVariants($id) : [];

        $this->view('client/product_detail', [
            'title' => ($product['name'] ?? 'Chi tiết sản phẩm') . ' — Anta',
            'currentPage' => 'products',
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'variants' => $variants
        ]);
    }
}
