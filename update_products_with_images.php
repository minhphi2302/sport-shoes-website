<?php
/**
 * Script cập nhật ảnh cho sản phẩm đã có trong database
 * Chạy sau khi đã tạo ảnh placeholder hoặc download ảnh mẫu
 */

require_once __DIR__ . '/app/bootstrap.php';

$pdo = App\Core\Database::getInstance();

echo "🔄 Đang cập nhật ảnh cho sản phẩm...\n\n";

// Lấy danh sách ảnh có sẵn
$imageDir = __DIR__ . '/public/uploads/products';
$images = [];

if (is_dir($imageDir)) {
    $files = scandir($imageDir);
    foreach ($files as $file) {
        if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $file)) {
            $images[] = 'products/' . $file;
        }
    }
}

if (empty($images)) {
    echo "❌ Không tìm thấy ảnh nào trong thư mục {$imageDir}\n";
    echo "💡 Vui lòng chạy một trong các script sau trước:\n";
    echo "   php create_placeholder_images.php\n";
    echo "   php download_sample_images.php\n\n";
    exit(1);
}

echo "✅ Tìm thấy " . count($images) . " ảnh\n\n";

// Lấy tất cả sản phẩm chưa có ảnh hoặc ảnh cũ
$stmt = $pdo->query("SELECT product_id, name, image_url FROM products ORDER BY product_id");
$products = $stmt->fetchAll();

$updateCount = 0;
$skipCount = 0;

foreach ($products as $index => $product) {
    $productId = $product['product_id'];
    $productName = $product['name'];
    $currentImage = $product['image_url'];
    
    // Chọn ảnh theo index (lặp lại nếu hết)
    $newImage = $images[$index % count($images)];
    
    // Chỉ update nếu chưa có ảnh hoặc ảnh cũ là default
    if (empty($currentImage) || $currentImage === 'default-product.jpg') {
        $updateStmt = $pdo->prepare("UPDATE products SET image_url = :image WHERE product_id = :id");
        $updateStmt->execute([
            'image' => $newImage,
            'id' => $productId
        ]);
        
        echo "✅ [{$productId}] {$productName} → {$newImage}\n";
        $updateCount++;
    } else {
        echo "⏭️  [{$productId}] {$productName} → Đã có ảnh, bỏ qua\n";
        $skipCount++;
    }
}

echo "\n" . str_repeat('=', 50) . "\n";
echo "📊 KẾT QUẢ:\n";
echo "   ✅ Đã cập nhật: {$updateCount} sản phẩm\n";
echo "   ⏭️  Bỏ qua: {$skipCount} sản phẩm (đã có ảnh)\n";
echo "   📁 Tổng số ảnh: " . count($images) . "\n";
echo str_repeat('=', 50) . "\n\n";

echo "🎉 HOÀN TẤT! Refresh trang web để xem kết quả.\n\n";

?>
