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
    $pdo->exec("ALTER TABLE products ADD COLUMN sizes VARCHAR(255) DEFAULT NULL;");
    $pdo->exec("ALTER TABLE products ADD COLUMN colors VARCHAR(255) DEFAULT NULL;");
    $pdo->exec("ALTER TABLE order_details ADD COLUMN size VARCHAR(50) DEFAULT NULL;");
    $pdo->exec("ALTER TABLE order_details ADD COLUMN color VARCHAR(50) DEFAULT NULL;");
    echo "COLUMNS_ADDED";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
