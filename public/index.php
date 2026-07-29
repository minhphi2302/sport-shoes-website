<?php

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

use App\Core\Router;

$router = new Router();

// Định nghĩa một số route mẫu
$router->get('/', function () {
    echo "<h1>Welcome to Shop Giày AI!</h1>";
    echo "<p>Router Core Architecture & Custom Autoloader is working perfectly (No Composer needed).</p>";
});

$router->get('/product/{id}', function ($id) {
    echo "Chi tiết sản phẩm ID: " . htmlspecialchars($id);
});

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

// Xử lý request
$router->resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
