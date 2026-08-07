<?php

// Load Autoloader & .env
require_once __DIR__ . '/../app/bootstrap.php';


use App\Core\Router;

$router = new Router();

$router->get('/', [App\Controllers\ProductController::class, 'home']);
$router->get('/products', [App\Controllers\ProductController::class, 'index']);
$router->get('/products/{id}', [App\Controllers\ProductController::class, 'show']);

// Thêm route động để test lấy dữ liệu từ bất kỳ bảng nào (VD: /test/users, /test/products)
$router->get('/test/{table}', function ($table) {
    // Danh sách các bảng cho phép query để tránh SQL Injection
    $allowedTables = ['users', 'categories', 'brands', 'products', 'orders', 'order_details', 'migrations'];
    
    if (!in_array($table, $allowedTables)) {
        echo "Bảng không tồn tại hoặc không được phép truy cập!";
        return;
    }

    try {
        $pdo = \App\Core\Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM `$table` LIMIT 10");
        $data = $stmt->fetchAll();
        
        echo "<h1>Danh sách dữ liệu bảng: " . htmlspecialchars($table) . "</h1>";
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    } catch (\Exception $e) {
        echo "Lỗi kết nối hoặc query DB: " . $e->getMessage();
    }
});

$router->get('/login', [App\Controllers\AuthController::class, 'showLogin']);
$router->post('/login', [App\Controllers\AuthController::class, 'login']);
$router->get('/register', [App\Controllers\AuthController::class, 'showRegister']);
$router->post('/register', [App\Controllers\AuthController::class, 'register']);
$router->get('/forgot-password', [App\Controllers\AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password', [App\Controllers\AuthController::class, 'processForgotPassword']);
$router->get('/reset-password', [App\Controllers\AuthController::class, 'showResetPassword']);
$router->post('/reset-password', [App\Controllers\AuthController::class, 'processResetPassword']);
$router->post('/logout', [App\Controllers\AuthController::class, 'logout']);
$router->get('/logout', [App\Controllers\AuthController::class, 'logout']);

$router->get('/profile', [App\Controllers\ProfileController::class, 'index']);
$router->post('/profile/password', [App\Controllers\ProfileController::class, 'updatePassword']);
$router->post('/profile/delete', [App\Controllers\ProfileController::class, 'deleteAccount']);

$router->get('/cart', [App\Controllers\CartController::class, 'index']);
$router->post('/cart/add', [App\Controllers\CartController::class, 'add']);
$router->post('/cart/update', [App\Controllers\CartController::class, 'update']);
$router->post('/cart/remove', [App\Controllers\CartController::class, 'remove']);
$router->post('/cart/clear', [App\Controllers\CartController::class, 'clear']);

$router->get('/checkout', [App\Controllers\OrderController::class, 'showCheckout']);
$router->post('/checkout', [App\Controllers\OrderController::class, 'placeOrder']);
$router->get('/orders/{id}/success', [App\Controllers\OrderController::class, 'showSuccess']);

$router->get('/orders', [App\Controllers\OrderController::class, 'myOrders']);
$router->get('/orders/{id}', [App\Controllers\OrderController::class, 'show']);
$router->post('/orders/{id}/cancel', [App\Controllers\OrderController::class, 'cancel']);
$router->get('/admin/orders', [App\Controllers\Admin\OrderAdminController::class, 'index']);
$router->get('/admin/orders/{id}', [App\Controllers\Admin\OrderAdminController::class, 'show']);
$router->post('/admin/orders/{id}/status', [App\Controllers\Admin\OrderAdminController::class, 'updateStatus']);

$router->get('/admin/categories', [App\Controllers\Admin\CategoryController::class, 'index']);
$router->get('/admin/categories/create', [App\Controllers\Admin\CategoryController::class, 'create']);
$router->post('/admin/categories/create', [App\Controllers\Admin\CategoryController::class, 'create']);
$router->get('/admin/categories/{id}/edit', [App\Controllers\Admin\CategoryController::class, 'edit']);
$router->post('/admin/categories/{id}/edit', [App\Controllers\Admin\CategoryController::class, 'edit']);
$router->post('/admin/categories/{id}/delete', [App\Controllers\Admin\CategoryController::class, 'delete']);

$router->get('/admin/brands', [App\Controllers\Admin\BrandController::class, 'index']);
$router->get('/admin/brands/create', [App\Controllers\Admin\BrandController::class, 'create']);
$router->post('/admin/brands/create', [App\Controllers\Admin\BrandController::class, 'create']);
$router->get('/admin/brands/{id}/edit', [App\Controllers\Admin\BrandController::class, 'edit']);
$router->post('/admin/brands/{id}/edit', [App\Controllers\Admin\BrandController::class, 'edit']);
$router->post('/admin/brands/{id}/delete', [App\Controllers\Admin\BrandController::class, 'delete']);

$router->get('/admin/products', [App\Controllers\Admin\ProductAdminController::class, 'index']);
$router->post('/admin/products/bulk-update', [App\Controllers\Admin\ProductAdminController::class, 'bulkUpdate']);
$router->get('/admin/products/create', [App\Controllers\Admin\ProductAdminController::class, 'create']);
$router->post('/admin/products/create', [App\Controllers\Admin\ProductAdminController::class, 'create']);
$router->get('/admin/products/{id}', [App\Controllers\Admin\ProductAdminController::class, 'show']);
$router->get('/admin/products/{id}/edit', [App\Controllers\Admin\ProductAdminController::class, 'edit']);
$router->post('/admin/products/{id}/edit', [App\Controllers\Admin\ProductAdminController::class, 'edit']);
$router->post('/admin/products/{id}/delete', [App\Controllers\Admin\ProductAdminController::class, 'delete']);

$router->get('/admin/dashboard', [App\Controllers\Admin\DashboardController::class, 'index']);
$router->get('/admin/customers', [App\Controllers\Admin\CustomerAdminController::class, 'index']);
$router->get('/admin/customers/{id}', [App\Controllers\Admin\CustomerAdminController::class, 'show']);
$router->post('/admin/customers/{id}/toggle-status', [App\Controllers\Admin\CustomerAdminController::class, 'toggleStatus']);
$router->post('/admin/customers/{id}/delete', [App\Controllers\Admin\CustomerAdminController::class, 'delete']);

$router->resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
