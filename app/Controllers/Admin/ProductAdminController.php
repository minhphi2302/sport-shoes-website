<?php

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Size;
use App\Models\Color;
use App\Core\FileUploader;
use App\Exceptions\ValidationException;

class ProductAdminController extends AdminController
{
    private Product $productModel;
    private Category $categoryModel;
    private Brand $brandModel;
    private Size $sizeModel;
    private Color $colorModel;
    private FileUploader $fileUploader;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->brandModel = new Brand();
        $this->sizeModel = new Size();
        $this->colorModel = new Color();
        $this->fileUploader = new FileUploader();
    }

    private function getProductDataFromRequest(): array
    {
        $data = [
            'sku' => mb_strtoupper(trim($_POST['sku'] ?? ''), 'UTF-8'),
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'brand_id' => (int)($_POST['brand_id'] ?? 0),
            'price' => (float)($_POST['price'] ?? 0),
            'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
            'quantity' => 0 // Will be updated by saveVariants
        ];

        if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadDir = __DIR__ . '/../../../public/uploads/products';
            $data['image_url'] = 'products/' . $this->fileUploader->uploadImage($_FILES['image'], $uploadDir, 'product_');
        }

        return $data;
    }

    private function processAndSaveVariants(int $productId): void
    {
        $variantSkus = $_POST['variant_skus'] ?? [];
        $variantModels = $_POST['variant_models'] ?? [];
        
        $variantGenders = $_POST['variant_genders'] ?? [];
        $variantRawSizes = $_POST['variant_raw_sizes'] ?? [];
        $variantSizes = [];
        foreach ($variantRawSizes as $idx => $size) {
            $gender = $variantGenders[$idx] ?? 'Nam';
            $variantSizes[] = trim($gender) . ' - ' . trim($size);
        }
        
        $variantColors = $_POST['variant_colors'] ?? [];
        $variantPrices = $_POST['variant_prices'] ?? [];
        $variantQtys = $_POST['variant_qtys'] ?? [];
        $this->productModel->saveVariants($productId, $variantSkus, $variantModels, $variantSizes, $variantColors, $variantPrices, $variantQtys);
    }

    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        
        $filters = [];
        if (!empty($_GET['search'])) {
            $filters['search'] = trim($_GET['search']);
        }

        $products = $this->productModel->findAllWithFilters($filters, $page, $perPage);
        $totalProducts = $this->productModel->countAllWithFilters($filters);
        $totalPages = ceil($totalProducts / $perPage);

        $this->view('admin/product_list', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'filters' => $filters
        ]);
    }

    public function bulkUpdate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['products'])) {
            $pdo = \App\Core\Database::getInstance();
            try {
                $pdo->beginTransaction();
                
                $stmt = $pdo->prepare("UPDATE products SET price = :price, sale_price = :sale_price WHERE product_id = :id");
                
                foreach ($_POST['products'] as $id => $data) {
                    $price = (float)($data['price'] ?? 0);
                    $salePrice = !empty($data['sale_price']) ? (float)$data['sale_price'] : null;
                    
                    $stmt->execute([
                        'price' => $price,
                        'sale_price' => $salePrice,
                        'id' => (int)$id
                    ]);
                }
                
                $pdo->commit();
                $_SESSION['success'] = 'Cập nhật giá hàng loạt thành công.';
            } catch (\Exception $e) {
                $pdo->rollBack();
                $_SESSION['error'] = 'Lỗi cập nhật hàng loạt: ' . $e->getMessage();
            }
        }
        $this->redirect('/admin/products');
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = $this->getProductDataFromRequest();
                if (!isset($data['image_url'])) $data['image_url'] = null; // ensure image_url exists

                $productId = $this->productModel->create($data);
                
                if (!$productId) {
                    throw new \Exception('Không thể tạo sản phẩm, lỗi DB trả về false.');
                }
                
                $this->processAndSaveVariants($productId);

                // Khởi động session trước khi set message (business rule: Session Security)
                \App\Core\Auth::initSession();
                $_SESSION['success'] = 'Thêm sản phẩm thành công.';
                $this->redirect('/admin/products');

            } catch (ValidationException $e) {
                // Khởi động session trước khi set message
                \App\Core\Auth::initSession();
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('/admin/products/create');
            } catch (\Exception $e) {
                // Khởi động session trước khi set message
                \App\Core\Auth::initSession();
                $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
                // Log chi tiết để debug
                error_log("Product Create Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                $this->redirect('/admin/products/create');
            }
        }

        $categories = $this->categoryModel->all();
        $brands = $this->brandModel->all();
        $sizes = $this->sizeModel->all();
        $colors = $this->colorModel->all();

        $this->view('admin/product_create', [
            'categories' => $categories,
            'brands' => $brands,
            'sizes' => $sizes,
            'colors' => $colors
        ]);
    }

    public function show($id): void
    {
        $productId = (int)$id;
        $product = $this->productModel->findById($productId);
        
        if (!$product) {
                $_SESSION['error'] = 'Sản phẩm không tồn tại.';
                $this->redirect('/admin/products');
            }
            
            $variants = $this->productModel->getVariants($productId);
        
        $this->view('admin/product_detail', [
            'product' => $product,
            'variants' => $variants
        ]);
    }

    public function edit($id): void
    {
        $productId = (int)$id;
        $product = $this->productModel->findById($productId);

        if (!$product) {
            $_SESSION['error'] = 'Sản phẩm không tồn tại';
            $this->redirect('/admin/products');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = $this->getProductDataFromRequest();

                if (!empty($data['image_url']) && !empty($product['image_url'])) {
                    $oldPath = __DIR__ . '/../../../public/uploads/' . $product['image_url'];
                    if (file_exists($oldPath)) @unlink($oldPath);
                }

                $this->productModel->update($productId, $data);
                $this->processAndSaveVariants($productId);

                $_SESSION['success'] = 'Cập nhật sản phẩm thành công.';
                
                // Giữ lại page hiện tại khi redirect về danh sách (business rule: UX)
                $returnPage = !empty($_POST['return_page']) ? (int)$_POST['return_page'] : 1;
                $this->redirect('/admin/products?page=' . $returnPage);

            } catch (ValidationException $e) {
                $_SESSION['error'] = $e->getMessage();
                $returnPage = !empty($_POST['return_page']) ? '?return_page=' . (int)$_POST['return_page'] : '';
                $this->redirect("/admin/products/{$productId}/edit" . $returnPage);
            } catch (\Exception $e) {
                $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
                $returnPage = !empty($_POST['return_page']) ? '?return_page=' . (int)$_POST['return_page'] : '';
                $this->redirect("/admin/products/{$productId}/edit" . $returnPage);
            }
        }

        $categories = $this->categoryModel->all();
        $brands = $this->brandModel->all();
        $sizes = $this->sizeModel->all();
        $colors = $this->colorModel->all();

        $this->view('admin/product_edit', [
            'product' => $product,
            'categories' => $categories,
            'brands' => $brands,
            'sizes' => $sizes,
            'colors' => $colors,
            'variants' => $this->productModel->getVariants($productId),
            'returnPage' => $_GET['return_page'] ?? 1 // Truyền return_page vào view
        ]);
    }

    public function delete($id): void
    {
        $productId = (int)$id;
        try {
            $this->productModel->delete($productId);
            $_SESSION['success'] = 'Xóa sản phẩm thành công';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa';
        }
        $this->redirect('/admin/products');
    }
}
