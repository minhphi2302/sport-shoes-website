<?php
// refresh_db.php - Script tiện ích để reset và nạp lại toàn bộ dữ liệu
echo "Bắt đầu làm mới Database...\n";

try {
    $pdo = new PDO("mysql:host=127.0.0.1;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Xóa và tạo lại Database
    $pdo->exec("DROP DATABASE IF EXISTS shop_giay;");
    $pdo->exec("CREATE DATABASE shop_giay CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "1. Đã xóa và tạo lại Database 'shop_giay'.\n";

    // 2. Chạy file migrate.php
    echo "\n2. Bắt đầu chạy Migrations:\n";
    ob_start();
    require __DIR__ . '/database/migrate.php';
    echo ob_get_clean();

    // 3. Chạy file seeder.php
    echo "\n3. Bắt đầu nạp dữ liệu (Seeder):\n";
    ob_start();
    require __DIR__ . '/database/seeder.php';
    echo ob_get_clean();
    
    echo "\n=> HOÀN TẤT LÀM MỚI DATABASE!\n";
} catch (Exception $e) {
    echo "LỖI: " . $e->getMessage() . "\n";
}
