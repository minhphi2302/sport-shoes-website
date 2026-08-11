<?php

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

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
    // Đảm bảo kết nối dùng utf8mb4 khi đọc/ghi dữ liệu tiếng Việt
    $pdo->exec("SET NAMES utf8mb4");
    
    // Check if migrations table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
    if ($stmt->rowCount() == 0) {
        $sql = file_get_contents(__DIR__ . '/migrations/001_create_migrations_table.sql');
        $pdo->exec($sql);
        echo "Created migrations table.\n";
    }

    $stmt = $pdo->query("SELECT migration_name FROM migrations");
    $appliedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $files = glob(__DIR__ . '/migrations/*.sql');
    sort($files);

    foreach ($files as $file) {
        $filename = basename($file);
        
        if (!in_array($filename, $appliedMigrations)) {
            echo "Applying migration: $filename\n";
            $sql = file_get_contents($file);
            $pdo->exec($sql);
            
            $stmt = $pdo->prepare("INSERT INTO migrations (migration_name, executed_at) VALUES (?, NOW())");
            $stmt->execute([$filename]);
            echo "Successfully applied $filename\n";
        }
    }
    
    echo "All migrations applied successfully.\n";

} catch (\Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
