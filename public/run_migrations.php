<?php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

\App\Core\Env::load(__DIR__ . '/../.env');

try {
    $pdo = \App\Core\Database::getInstance();
    $stmt = $pdo->query("SHOW COLUMNS FROM products");
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
