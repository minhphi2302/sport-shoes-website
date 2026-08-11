<?php
/**
 * File demo kiểm tra logic phát hiện biến thể trùng lặp
 * 
 * Kịch bản test:
 * 1. Thêm biến thể mới → Thành công
 * 2. Thêm lại biến thể giống hệt (cùng model, size, màu, giá) → Cộng dồn số lượng
 * 3. Thêm biến thể cùng model/size/màu nhưng khác giá → Báo lỗi
 */

require_once __DIR__ . '/app/bootstrap.php';

use App\Models\Product;
use App\Exceptions\ValidationException;

$db = App\Core\Database::getInstance()->getConnection();
$productModel = new Product($db);

echo "<h2>Test Logic Biến Thể Trùng Lặp</h2>";
echo "<hr>";

// Giả sử product_id = 1 đã tồn tại
$testProductId = 1;

// Kiểm tra product có tồn tại không
$product = $productModel->findById($testProductId);
if (!$product) {
    die("<p style='color:red'>Lỗi: Không tìm thấy sản phẩm có ID = {$testProductId}. Vui lòng tạo sản phẩm test trước.</p>");
}

echo "<h3>Sản phẩm test: {$product['name']} (Giá gốc: " . number_format($product['price']) . " VNĐ)</h3>";
echo "<hr>";

// ===========================
// TEST 1: Thêm biến thể mới
// ===========================
echo "<h4>TEST 1: Thêm biến thể mới</h4>";
try {
    $productModel->saveVariants(
        $testProductId,
        ['TEST-SKU-1'],
        ['Mặc định'],
        ['Nam - 42'],
        ['Trắng'],
        [190000], // Giá giảm 5%
        [10]      // Số lượng 10
    );
    echo "<p style='color:green'>✓ Thêm biến thể mới thành công!</p>";
} catch (ValidationException $e) {
    echo "<p style='color:red'>✗ Lỗi: {$e->getMessage()}</p>";
}

// Hiển thị biến thể hiện tại
$variants = $productModel->getVariants($testProductId);
echo "<p>Số biến thể hiện tại: " . count($variants) . "</p>";
foreach ($variants as $v) {
    echo "<li>{$v['model']} - {$v['size']} - {$v['color']} - Giá: " . number_format($v['price']) . " VNĐ - Số lượng: {$v['quantity']}</li>";
}
echo "<hr>";

// ===========================
// TEST 2: Thêm lại biến thể giống hệt → Cộng dồn số lượng
// ===========================
echo "<h4>TEST 2: Thêm lại biến thể giống hệt (cùng giá) → Cộng dồn số lượng</h4>";
try {
    $productModel->saveVariants(
        $testProductId,
        ['TEST-SKU-1'],
        ['Mặc định'],
        ['Nam - 42'],
        ['Trắng'],
        [190000], // Cùng giá
        [5]       // Thêm 5 nữa
    );
    echo "<p style='color:green'>✓ Cộng dồn số lượng thành công!</p>";
} catch (ValidationException $e) {
    echo "<p style='color:red'>✗ Lỗi: {$e->getMessage()}</p>";
}

// Hiển thị biến thể hiện tại
$variants = $productModel->getVariants($testProductId);
echo "<p>Số biến thể hiện tại: " . count($variants) . "</p>";
foreach ($variants as $v) {
    echo "<li>{$v['model']} - {$v['size']} - {$v['color']} - Giá: " . number_format($v['price']) . " VNĐ - Số lượng: {$v['quantity']}</li>";
}
echo "<hr>";

// ===========================
// TEST 3: Thêm biến thể cùng model/size/màu nhưng khác giá → Báo lỗi
// ===========================
echo "<h4>TEST 3: Thêm biến thể cùng model/size/màu nhưng khác giá → Báo lỗi</h4>";
try {
    $productModel->saveVariants(
        $testProductId,
        ['TEST-SKU-1', 'TEST-SKU-2'],
        ['Mặc định', 'Mặc định'],
        ['Nam - 42', 'Nam - 42'],
        ['Trắng', 'Trắng'],
        [190000, 180000], // Giá khác nhau
        [5, 5]
    );
    echo "<p style='color:red'>✗ Test thất bại: Không báo lỗi khi thêm biến thể trùng với giá khác!</p>";
} catch (ValidationException $e) {
    echo "<p style='color:green'>✓ Đã phát hiện lỗi đúng: {$e->getMessage()}</p>";
}
echo "<hr>";

// ===========================
// TEST 4: Thêm nhiều biến thể mới cùng lúc
// ===========================
echo "<h4>TEST 4: Thêm nhiều biến thể mới cùng lúc</h4>";
try {
    $productModel->saveVariants(
        $testProductId,
        ['TEST-SKU-1', 'TEST-SKU-3', 'TEST-SKU-4'],
        ['Mặc định', 'Mặc định', 'Mặc định'],
        ['Nam - 42', 'Nam - 43', 'Nữ - 38'],
        ['Trắng', 'Đen', 'Hồng'],
        [190000, 185000, 180000],
        [10, 8, 12]
    );
    echo "<p style='color:green'>✓ Thêm nhiều biến thể thành công!</p>";
} catch (ValidationException $e) {
    echo "<p style='color:red'>✗ Lỗi: {$e->getMessage()}</p>";
}

// Hiển thị biến thể hiện tại
$variants = $productModel->getVariants($testProductId);
echo "<p>Số biến thể hiện tại: " . count($variants) . "</p>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>SKU</th><th>Model</th><th>Size</th><th>Màu</th><th>Giá</th><th>Số lượng</th></tr>";
foreach ($variants as $v) {
    echo "<tr>";
    echo "<td>{$v['sku']}</td>";
    echo "<td>{$v['model']}</td>";
    echo "<td>{$v['size']}</td>";
    echo "<td>{$v['color']}</td>";
    echo "<td>" . number_format($v['price']) . " VNĐ</td>";
    echo "<td>{$v['quantity']}</td>";
    echo "</tr>";
}
echo "</table>";
echo "<hr>";

echo "<h3>Tất cả các test đã hoàn thành!</h3>";
