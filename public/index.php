<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Bật hiển thị lỗi trực quan để phòng tránh trắng trang
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Custom Autoloader thay cho Composer
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load .env
\App\Core\Env::load(__DIR__ . '/../.env');

// Helper function base_url() tự động tạo URL chuẩn tuyệt đối cho cả Root lẫn Public
if (!function_exists('base_url')) {
    function base_url(string $path = ''): string {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $baseDir = preg_replace('#/public$#i', '', $scriptDir);
        if ($baseDir === '/' || $baseDir === '.') {
            $baseDir = '';
        }
        $path = ltrim($path, '/');
        return $baseDir . ($path ? '/' . $path : '/');
    }
}

use App\Core\Router;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\AuthController;

$router = new Router();

// =========================================================================
// CLIENT ROUTES (Giao diện mua hàng phía Khách hàng)
// =========================================================================

// Trang chủ
$router->get('/', [ProductController::class, 'home']);

// Danh sách sản phẩm (có lọc & phân trang)
$router->get('/products', [ProductController::class, 'list']);

// Chi tiết sản phẩm
$router->get('/product/{id}', [ProductController::class, 'detail']);

// Giỏ hàng
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->get('/cart/add/{id}', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->get('/cart/remove/{id}', [CartController::class, 'remove']);
$router->get('/cart/clear', [CartController::class, 'clear']);

// Thanh toán & Đặt hàng
$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout/process', [CheckoutController::class, 'process']);
$router->get('/checkout/success', [CheckoutController::class, 'success']);

// Đăng nhập & Đăng ký
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/account', [AuthController::class, 'account']);
$router->post('/account/update', [AuthController::class, 'updateProfile']);

// Route test kết nối DB
$router->get('/test/{table}', function ($table) {
    $allowedTables = ['users', 'categories', 'brands', 'products', 'orders', 'order_details', 'migrations'];
    if (!in_array($table, $allowedTables)) {
        echo "Bảng không tồn tại hoặc không được phép truy cập!";
        return;
    }
    try {
        $pdo = \App\Core\Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM `$table` LIMIT 10");
        $data = $stmt->fetchAll();
        echo "<h1>Danh sách dữ liệu bảng: " . htmlspecialchars($table) . "</h1><pre>";
        print_r($data);
        echo "</pre>";
    } catch (\Exception $e) {
        echo "Lỗi kết nối DB: " . $e->getMessage();
    }
});

// Xử lý request
$router->resolve($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');
