<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=shop_giay;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Đọc file schema
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    if ($schema) {
        $pdo->exec($schema);
        echo "Import schema.sql thành công!\n";
    }
    
    // Đọc file seed
    $seed = file_get_contents(__DIR__ . '/database/seed.sql');
    if ($seed) {
        $pdo->exec($seed);
        echo "Import seed.sql thành công!\n";
    }
    
} catch (PDOException $e) {
    echo "Lỗi database: " . $e->getMessage();
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
