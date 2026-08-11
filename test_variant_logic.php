<?php
/**
 * Demo logic kiểm tra biến thể trùng lặp mới
 * 
 * LOGIC MỚI:
 * - Chỉ cho phép thêm biến thể hoàn toàn mới (không trùng model+size+màu)
 * - Nếu biến thể đã tồn tại trong form submit → BÁO LỖI
 * - Nếu biến thể đã tồn tại trong DB → Cho phép CẬP NHẬT (giá, số lượng)
 * 
 * TRƯỚC ĐÂY (SAI):
 * - Cho phép trùng model+size+màu → Cộng dồn số lượng
 * 
 * SAU KHI SỬA (ĐÚNG):
 * - Trùng trong form → Báo lỗi
 * - Trùng với DB → Cập nhật (ghi đè số lượng, không cộng dồn)
 */

echo "<!DOCTYPE html>\n";
echo "<html lang='vi'>\n<head>\n";
echo "<meta charset='UTF-8'>\n";
echo "<title>Demo Logic Kiểm Tra Biến Thể</title>\n";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>\n";
echo "</head>\n<body class='bg-light py-5'>\n";
echo "<div class='container'>\n";
echo "<h1 class='mb-4'>🧪 Demo Logic Kiểm Tra Biến Thể Trùng Lặp</h1>\n";

// Test Case 1: Trùng trong form submit
echo "<div class='card mb-3'>\n";
echo "<div class='card-header bg-danger text-white'><strong>Test Case 1:</strong> Trùng trong form submit (Phải báo lỗi)</div>\n";
echo "<div class='card-body'>\n";
echo "<pre class='bg-light p-3 border rounded'>\n";
echo "Dữ liệu submit:\n";
echo "- Size 39, Màu Trắng, Giảm 5%\n";
echo "- Size 39, Màu Trắng, Giảm 6%\n\n";
echo "KẾT QUẢ MONG ĐỢI: ❌ Báo lỗi \"Biến thể trùng lặp\"\n";
echo "LOGIC: Không cho phép cùng model+size+màu trong cùng lần submit\n";
echo "</pre>\n";
echo "</div>\n</div>\n";

// Test Case 2: Thêm mới biến thể (không trùng)
echo "<div class='card mb-3'>\n";
echo "<div class='card-header bg-success text-white'><strong>Test Case 2:</strong> Thêm biến thể mới (Không trùng)</div>\n";
echo "<div class='card-body'>\n";
echo "<pre class='bg-light p-3 border rounded'>\n";
echo "Dữ liệu submit:\n";
echo "- Size 39, Màu Trắng\n";
echo "- Size 40, Màu Đen\n\n";
echo "KẾT QUẢ MONG ĐỢI: ✅ Thêm thành công 2 biến thể\n";
echo "LOGIC: Không trùng model+size+màu → Thêm mới\n";
echo "</pre>\n";
echo "</div>\n</div>\n";

// Test Case 3: Cập nhật biến thể đã tồn tại trong DB
echo "<div class='card mb-3'>\n";
echo "<div class='card-header bg-info text-white'><strong>Test Case 3:</strong> Cập nhật biến thể đã có trong DB</div>\n";
echo "<div class='card-body'>\n";
echo "<pre class='bg-light p-3 border rounded'>\n";
echo "Biến thể hiện có trong DB:\n";
echo "- Size 39, Màu Trắng, Số lượng: 10\n\n";
echo "Dữ liệu submit (edit):\n";
echo "- Size 39, Màu Trắng, Số lượng: 20\n\n";
echo "KẾT QUẢ MONG ĐỢI: ✅ Cập nhật thành công, số lượng = 20 (GHI ĐÈ, không cộng dồn)\n";
echo "LOGIC: Biến thể đã tồn tại → Cho phép cập nhật giá và số lượng\n";
echo "</pre>\n";
echo "</div>\n</div>\n";

// Test Case 4: Trước đây (sai) - cộng dồn
echo "<div class='card mb-3'>\n";
echo "<div class='card-header bg-warning'><strong>Test Case 4:</strong> Logic CŨ (SAI) - Cộng dồn</div>\n";
echo "<div class='card-body'>\n";
echo "<pre class='bg-light p-3 border rounded'>\n";
echo "Biến thể hiện có trong DB:\n";
echo "- Size 39, Màu Trắng, Số lượng: 10\n\n";
echo "Dữ liệu submit (theo logic CŨ):\n";
echo "- Size 39, Màu Trắng, Số lượng: 20\n\n";
echo "KẾT QUẢ CŨ (SAI): Số lượng = 30 (10 + 20) ❌\n";
echo "KẾT QUẢ MỚI (ĐÚNG): Số lượng = 20 (ghi đè) ✅\n";
echo "</pre>\n";
echo "</div>\n</div>\n";

// So sánh logic
echo "<div class='card mb-3 border-primary'>\n";
echo "<div class='card-header bg-primary text-white'><strong>📋 Tóm Tắt Sự Khác Biệt</strong></div>\n";
echo "<div class='card-body'>\n";
echo "<table class='table table-bordered'>\n";
echo "<thead class='table-dark'><tr><th>Tình huống</th><th>Logic CŨ (Sai)</th><th>Logic MỚI (Đúng)</th></tr></thead>\n";
echo "<tbody>\n";
echo "<tr>\n";
echo "<td>Trùng trong form submit</td>\n";
echo "<td class='bg-danger text-white'>Cộng dồn số lượng</td>\n";
echo "<td class='bg-success text-white'>Báo lỗi</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Cập nhật biến thể đã có trong DB</td>\n";
echo "<td class='bg-danger text-white'>Cộng dồn số lượng</td>\n";
echo "<td class='bg-success text-white'>Ghi đè số lượng</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Thêm biến thể mới</td>\n";
echo "<td class='bg-success text-white'>OK</td>\n";
echo "<td class='bg-success text-white'>OK</td>\n";
echo "</tr>\n";
echo "</tbody>\n";
echo "</table>\n";
echo "</div>\n</div>\n";

// Business Rules
echo "<div class='card border-success'>\n";
echo "<div class='card-header bg-success text-white'><strong>✅ Business Rules (PRODUCTS)</strong></div>\n";
echo "<div class='card-body'>\n";
echo "<ol>\n";
echo "<li><strong>Biến thể duy nhất:</strong> Không cho phép tồn tại 2 biến thể cùng model+size+màu</li>\n";
echo "<li><strong>Cập nhật ghi đè:</strong> Khi edit biến thể, số lượng mới GHI ĐÈ số lượng cũ, không cộng dồn</li>\n";
echo "<li><strong>Kiểm tra trùng lặp:</strong> Phải báo lỗi nếu user cố tình thêm biến thể trùng trong cùng lần submit</li>\n";
echo "<li><strong>Giá không vượt giá gốc:</strong> Giá biến thể ≤ Giá bán sản phẩm</li>\n";
echo "</ol>\n";
echo "</div>\n</div>\n";

echo "</div>\n</body>\n</html>";
?>
