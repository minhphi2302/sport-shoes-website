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
        echo "=> Đã nạp users, categories, brands thành công!\n";
    }
    
    // Nạp thêm sản phẩm mẫu 
    $productSql = "
    INSERT INTO products (sku, name, description, price, sale_price, quantity, category_id, brand_id, status) VALUES
    ('NK-RN-001', 'Giày Nike Air Zoom Pegasus Nữ', 'Giày chạy êm ái', 3000000.00, 2500000.00, 50, 1, 1, 'active'),
    ('AD-RN-002', 'Giày Adidas Ultraboost 22 Nam', 'Giày chạy siêu nhẹ', 3500000.00, 3200000.00, 30, 1, 2, 'active'),
    ('NK-RN-003', 'Giày Nike React Infinity Run Nam', 'Giày chạy chống chấn thương', 3200000.00, 2800000.00, 40, 1, 1, 'active'),
    ('NK-RN-004', 'Giày Nike React Infinity Run Nữ', 'Giày chạy chống chấn thương 1', 200000.00, 80000.00, 0, 1, 1, 'active');
    ('NK-RN-005', 'Giày Nike React Infinity Run Nam', 'Giày chạy chống chấn thương 1234', 20000.00, 8000.00, 0, 1, 1, 'active');
    ";
    $pdo->exec($productSql);
    echo "=> Đã nạp products thành công!\n";
    
    // Nạp thêm biến thể sản phẩm (product_variants) vì scheme mới yêu cầu
    $variantSql = "
    INSERT INTO product_variants (product_id, sku, model, size, color, quantity) VALUES
    (1, 'NK-RN-001-40', 'Pegasus', '40', 'Đen', 10),
    (1, 'NK-RN-001-41', 'Pegasus', '41', 'Đen', 15),
    (2, 'AD-RN-003-42', 'Ultraboost', '42', 'Trắng', 5),
    (3, 'NK-RN-005-39', 'Infinity', '39', 'Đỏ', 20);
    ";
    $pdo->exec($variantSql);
    echo "=> Đã nạp product_variants thành công!\n";

    echo "Hoàn tất đồng bộ dữ liệu mẫu!\n";
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
