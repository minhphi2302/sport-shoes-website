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
$router->get('/', function() {
    echo "<h1>Welcome to Shop Giày AI!</h1>";
    echo "<p>Router Core Architecture & Custom Autoloader is working perfectly (No Composer needed).</p>";
});

$router->get('/product/{id}', function($id) {
    echo "Chi tiết sản phẩm ID: " . htmlspecialchars($id);
});

// Xử lý request
$router->resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
