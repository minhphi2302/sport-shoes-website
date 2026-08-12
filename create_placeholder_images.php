<?php
/**
 * Tạo ảnh placeholder đơn giản cho sản phẩm
 * Chạy: php create_placeholder_images.php
 */

// Kiểm tra GD extension
if (!extension_loaded('gd')) {
    die("❌ PHP GD extension chưa được cài đặt. Vui lòng cài đặt GD extension.\n");
}

// Tạo thư mục nếu chưa có
$uploadDir = __DIR__ . '/public/uploads/products';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
    echo "✅ Đã tạo thư mục: {$uploadDir}\n";
}

echo "🎨 Bắt đầu tạo ảnh placeholder...\n\n";

// Danh sách sản phẩm giả
$products = [
    ['name' => 'Nike Air Max', 'color' => [220, 38, 38]],
    ['name' => 'Adidas Ultra', 'color' => [0, 0, 0]],
    ['name' => 'New Balance 574', 'color' => [100, 149, 237]],
    ['name' => 'Puma RS-X', 'color' => [255, 165, 0]],
    ['name' => 'Converse Chuck', 'color' => [255, 255, 255]],
    ['name' => 'Vans Old Skool', 'color' => [255, 255, 255]],
    ['name' => 'Asics Gel', 'color' => [70, 130, 180]],
    ['name' => 'Reebok Classic', 'color' => [128, 128, 128]],
    ['name' => 'Nike Pegasus', 'color' => [50, 205, 50]],
    ['name' => 'Adidas Samba', 'color' => [255, 215, 0]],
];

$successCount = 0;

foreach ($products as $index => $product) {
    $num = $index + 1;
    $filename = 'product_' . uniqid() . '.jpg';
    $filepath = $uploadDir . '/' . $filename;
    
    echo "[{$num}/" . count($products) . "] Tạo ảnh: {$product['name']}...";
    
    // Tạo ảnh 800x800
    $width = 800;
    $height = 800;
    $image = imagecreatetruecolor($width, $height);
    
    // Màu nền
    $bgColor = imagecolorallocate($image, $product['color'][0], $product['color'][1], $product['color'][2]);
    imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);
    
    // Thêm gradient đơn giản
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    
    // Vẽ hình shoe icon đơn giản (hình oval + chữ)
    $gray = imagecolorallocate($image, 200, 200, 200);
    imagefilledellipse($image, $width/2, $height/2, 400, 200, $gray);
    
    // Thêm text
    $textColor = imagecolorallocate($image, 255, 255, 255);
    if ($product['color'][0] > 200 && $product['color'][1] > 200 && $product['color'][2] > 200) {
        $textColor = imagecolorallocate($image, 50, 50, 50);
    }
    
    // Font size
    $fontSize = 5; // Built-in font
    $text = $product['name'];
    $textWidth = imagefontwidth($fontSize) * strlen($text);
    $textHeight = imagefontheight($fontSize);
    $x = ($width - $textWidth) / 2;
    $y = ($height - $textHeight) / 2;
    
    imagestring($image, $fontSize, $x, $y, $text, $textColor);
    
    // Thêm viền
    imagerectangle($image, 0, 0, $width-1, $height-1, $black);
    
    // Lưu ảnh
    if (imagejpeg($image, $filepath, 90)) {
        echo " ✅ OK\n";
        $successCount++;
    } else {
        echo " ❌ FAIL\n";
    }
    
    imagedestroy($image);
}

// Tạo default-product.jpg
$defaultPath = __DIR__ . '/public/uploads/default-product.jpg';
$image = imagecreatetruecolor(800, 800);
$gray = imagecolorallocate($image, 200, 200, 200);
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 100, 100, 100);

imagefilledrectangle($image, 0, 0, 800, 800, $gray);
imagestring($image, 5, 320, 390, 'No Image', $black);

imagejpeg($image, $defaultPath, 90);
imagedestroy($image);

echo "\n" . str_repeat('=', 50) . "\n";
echo "📊 KẾT QUẢ:\n";
echo "   ✅ Đã tạo: {$successCount}/" . count($products) . " ảnh\n";
echo "   ✅ Đã tạo: default-product.jpg\n";
echo "   📁 Thư mục: {$uploadDir}\n";
echo str_repeat('=', 50) . "\n\n";

echo "🎉 HOÀN TẤT!\n";
echo "💡 Bây giờ:\n";
echo "   1. Vào Admin Panel\n";
echo "   2. Thêm/Sửa sản phẩm\n";
echo "   3. Upload ảnh vừa tạo\n";
echo "   4. HOẶC tự upload ảnh thật từ máy tính\n\n";

echo "📌 GỢI Ý: Tải ảnh giày thật từ:\n";
echo "   • Unsplash.com (miễn phí)\n";
echo "   • Pexels.com (miễn phí)\n";
echo "   • Hoặc chụp ảnh sản phẩm thật\n\n";

?>
