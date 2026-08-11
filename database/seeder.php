<?php
require __DIR__ . '/../app/Core/Database.php';
require __DIR__ . '/../app/Core/Env.php';
\App\Core\Env::load(__DIR__ . '/../.env');

try {
    $pdo = \App\Core\Database::getInstance();
    
    echo "Đang nạp dữ liệu vào database shop_giay...\n";
    
    $seedSql = file_get_contents(__DIR__ . '/seed.sql');
    if ($seedSql) {
        $pdo->exec($seedSql);
        echo "=> Đã nạp thành công users, categories, brands, products và product_variants!\n";
    }

    echo "Hoàn tất đồng bộ dữ liệu mẫu!\n";
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
