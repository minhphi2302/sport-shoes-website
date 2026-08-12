<?php
/**
 * Script download ảnh mẫu cho sản phẩm
 * Chạy: php download_sample_images.php
 */

// Danh sách ảnh mẫu giày thể thao (Unsplash - free to use)
$sampleImages = [
    // Nike shoes
    [
        'url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80', // Nike Red
        'name' => 'nike_red_shoes.jpg'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=800&q=80', // Nike Air
        'name' => 'nike_air_shoes.jpg'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=800&q=80', // Nike White
        'name' => 'nike_white_shoes.jpg'
    ],
    
    // Adidas shoes
    [
        'url' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=800&q=80', // Adidas
        'name' => 'adidas_shoes_1.jpg'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=800&q=80', // Adidas White
        'name' => 'adidas_white_shoes.jpg'
    ],
    
    // New Balance
    [
        'url' => 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=800&q=80', // NB
        'name' => 'newbalance_shoes.jpg'
    ],
    
    // Puma
    [
        'url' => 'https://images.unsplash.com/photo-1584735175097-719d848f8449?w=800&q=80', // Puma
        'name' => 'puma_shoes.jpg'
    ],
    
    // Generic sports shoes
    [
        'url' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=800&q=80',
        'name' => 'sports_shoes_1.jpg'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=800&q=80',
        'name' => 'sports_shoes_2.jpg'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=800&q=80',
        'name' => 'sports_shoes_3.jpg'
    ],
];

// Tạo thư mục nếu chưa có
$uploadDir = __DIR__ . '/public/uploads/products';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
    echo "✅ Đã tạo thư mục: {$uploadDir}\n";
}

echo "🚀 Bắt đầu download ảnh mẫu...\n\n";

$successCount = 0;
$failCount = 0;

foreach ($sampleImages as $index => $image) {
    $num = $index + 1;
    echo "[{$num}/" . count($sampleImages) . "] Đang download: {$image['name']}...";
    
    try {
        // Download ảnh
        $imageData = @file_get_contents($image['url']);
        
        if ($imageData === false) {
            echo " ❌ FAIL\n";
            $failCount++;
            continue;
        }
        
        // Lưu file
        $savePath = $uploadDir . '/' . $image['name'];
        $saved = file_put_contents($savePath, $imageData);
        
        if ($saved) {
            echo " ✅ OK (" . round($saved / 1024, 2) . " KB)\n";
            $successCount++;
        } else {
            echo " ❌ FAIL (không lưu được)\n";
            $failCount++;
        }
        
    } catch (Exception $e) {
        echo " ❌ FAIL ({$e->getMessage()})\n";
        $failCount++;
    }
    
    // Chờ một chút để không spam server
    usleep(500000); // 0.5 giây
}

echo "\n" . str_repeat('=', 50) . "\n";
echo "📊 KẾT QUẢ:\n";
echo "   ✅ Thành công: {$successCount}/" . count($sampleImages) . "\n";
echo "   ❌ Thất bại: {$failCount}/" . count($sampleImages) . "\n";
echo "   📁 Thư mục: {$uploadDir}\n";
echo str_repeat('=', 50) . "\n\n";

if ($successCount > 0) {
    echo "🎉 HOÀN TẤT! Ảnh đã được lưu vào thư mục uploads/products/\n";
    echo "💡 Bây giờ bạn có thể:\n";
    echo "   1. Vào Admin → Sản phẩm → Thêm/Sửa sản phẩm\n";
    echo "   2. Chọn ảnh từ thư mục uploads/products/\n";
    echo "   3. Hoặc tự upload ảnh khác\n\n";
} else {
    echo "⚠️ Không download được ảnh nào!\n";
    echo "💡 Kiểm tra:\n";
    echo "   1. Kết nối internet\n";
    echo "   2. PHP có bật allow_url_fopen\n";
    echo "   3. Firewall không chặn\n\n";
}

// Tạo file default-product.jpg nếu chưa có
$defaultImagePath = __DIR__ . '/public/uploads/default-product.jpg';
if (!file_exists($defaultImagePath) && $successCount > 0) {
    // Copy ảnh đầu tiên làm default
    $firstImage = $uploadDir . '/' . $sampleImages[0]['name'];
    if (file_exists($firstImage)) {
        copy($firstImage, $defaultImagePath);
        echo "✅ Đã tạo ảnh mặc định: default-product.jpg\n";
    }
}

?>
