# Test Case: Kiểm tra trùng lặp biến thể qua UI

## Mục tiêu
Kiểm tra hệ thống có báo lỗi đúng khi Admin cố tình thêm biến thể trùng lặp với % giảm giá khác nhau.

## Kịch bản test

### Test Case 1: ❌ Thêm biến thể trùng với % giảm khác → PHẢI BÁO LỖI

**Các bước thực hiện:**
1. Truy cập: `http://localhost/sport-shoes-website/admin/products/create`
2. Điền thông tin sản phẩm:
   - Tên sản phẩm: "Test Giày Trùng Biến Thể"
   - SKU: "TEST-DUP-001"
   - Giá bán: 1,000,000 VNĐ
   - Danh mục: Chọn bất kỳ
   - Thương hiệu: Chọn bất kỳ

3. Tạo biến thể thủ công (hoặc qua ma trận):
   - **Biến thể 1:**
     - Model: Mặc định
     - Size: Nam - 39
     - Color: Trắng
     - Giảm giá: 5% → Giá = 950,000
     - Số lượng: 10
   
   - **Biến thể 2 (TRÙNG nhưng % giảm khác):**
     - Model: Mặc định
     - Size: Nam - 39
     - Color: Trắng
     - Giảm giá: 10% → Giá = 900,000
     - Số lượng: 5

4. Click "Lưu sản phẩm"

**Kết quả mong đợi:**
- ❌ Hệ thống **KHÔNG CHO PHÉP** lưu
- Hiển thị thông báo lỗi màu đỏ phía trên form:
  ```
  Biến thể trùng lặp: Mặc định - Nam - 39 - Trắng đã tồn tại với mức giá khác (giảm 5% vs 10%). 
  Không được có cùng một đôi giày với 2 mức giá khác nhau.
  ```

---

### Test Case 2: ✅ Thêm biến thể trùng hoàn toàn → Cộng dồn số lượng

**Các bước thực hiện:**
1. Truy cập: `http://localhost/sport-shoes-website/admin/products/create`
2. Điền thông tin sản phẩm:
   - Tên sản phẩm: "Test Giày Cộng Dồn"
   - SKU: "TEST-MERGE-001"
   - Giá bán: 1,000,000 VNĐ

3. Tạo 2 biến thể GIỐNG HỆT NHAU:
   - **Biến thể 1:**
     - Model: Mặc định
     - Size: Nam - 39
     - Color: Trắng
     - Giảm giá: 5% → Giá = 950,000
     - Số lượng: 10
   
   - **Biến thể 2 (TRÙNG hoàn toàn):**
     - Model: Mặc định
     - Size: Nam - 39
     - Color: Trắng
     - Giảm giá: 5% → Giá = 950,000
     - Số lượng: 5

4. Click "Lưu sản phẩm"

**Kết quả mong đợi:**
- ✅ Hệ thống cho phép lưu
- Thông báo thành công
- Kiểm tra trong chi tiết sản phẩm → chỉ có **1 biến thể** với số lượng = **15** (10 + 5)

---

### Test Case 3: ✅ Thêm biến thể khác nhau → OK

**Các bước thực hiện:**
1. Tạo 3 biến thể KHÁC NHAU:
   - Biến thể 1: Nam - 39 - Trắng - 5%
   - Biến thể 2: Nam - 40 - Trắng - 5%
   - Biến thể 3: Nữ - 39 - Trắng - 5%

**Kết quả mong đợi:**
- ✅ Lưu thành công
- Có đúng 3 biến thể riêng biệt

---

## Cách test nhanh qua Database

Sau khi test, kiểm tra trực tiếp trong database:

```sql
-- Xem sản phẩm vừa tạo
SELECT * FROM products WHERE sku LIKE 'TEST-%' ORDER BY created_at DESC LIMIT 5;

-- Xem biến thể của sản phẩm (giả sử product_id = 28)
SELECT 
    variant_id,
    model, 
    size, 
    color, 
    price,
    quantity,
    ROUND((1000000 - price) / 1000000 * 100, 2) AS discount_percent
FROM product_variants 
WHERE product_id = 28
ORDER BY model, size, color;
```

---

## Automated Test (đã chạy thành công)

File: `test_variant_duplicate.php`

**Kết quả:**
```
TEST 1: Thêm 3 biến thể khác nhau hoàn toàn
✅ PASS: Thêm thành công 3 biến thể khác nhau

TEST 2: Thêm biến thể trùng (Nam-39-Trắng) nhưng % giảm khác nhau
✅ PASS: Báo lỗi đúng

TEST 3: Thêm biến thể trùng hoàn toàn → Cộng dồn số lượng
✅ PASS: Cộng dồn số lượng thành công (1 biến thể, tổng SL = 15)

TEST 4: Mix - biến thể trùng hoàn toàn + biến thể mới
✅ PASS: 2 biến thể đúng
```

---

## Lưu ý

- Logic validation nằm trong `Product::saveVariants()` (line ~100)
- Lỗi được throw qua `ValidationException` → hiển thị trong `$_SESSION['error']`
- Kiểm tra trùng dựa trên: `Model | Size | Color` và `% discount`
