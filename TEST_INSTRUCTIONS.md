# Hướng Dẫn Test Biến Thể Sản Phẩm

## Vấn đề đã sửa

**Triệu chứng:** Sau khi tạo sản phẩm với biến thể, không tạo thêm được nữa, không thấy thông báo gì.

**Nguyên nhân:** 
- JavaScript validation ở client đang **chặn** form submit khi phát hiện biến thể trùng lặp
- Nhưng logic backend lại **cho phép** trùng lặp hoàn toàn (để cộng dồn số lượng)
- Xung đột giữa client-side và server-side validation

**Giải pháp:**
- ✅ Bỏ validation trùng lặp ở JavaScript client
- ✅ Để backend xử lý toàn bộ logic kiểm tra trùng lặp
- ✅ Đảm bảo session message được hiển thị đúng cách

---

## Test Case 1: Thêm Biến Thể Trùng với % Giảm Khác → BÁO LỖI

**Các bước:**

1. Truy cập: `http://localhost/sport-shoes-website/admin/products/create`

2. Điền thông tin:
   - Tên: Test Giày
   - SKU: TEST-001
   - Giá: 1,000,000
   - Chọn danh mục và thương hiệu
   - Upload ảnh bất kỳ

3. Thêm biến thể thủ công vào bảng (hoặc dùng matrix generator):
   
   **Biến thể 1:**
   - SKU: TEST-001-A
   - Mẫu: Mặc định
   - Đối tượng: Nam
   - Size: 39
   - Màu: Trắng
   - Giá: **950,000** (giảm 5%)
   - SL: 10

   **Biến thể 2 (TRÙNG nhưng giá khác):**
   - SKU: TEST-001-B
   - Mẫu: Mặc định
   - Đối tượng: Nam
   - Size: 39
   - Màu: Trắng
   - Giá: **900,000** (giảm 10%)
   - SL: 5

4. Click "Thêm sản phẩm"

**Kết quả mong đợi:**

```
❌ Alert màu đỏ phía trên form:

"Biến thể trùng lặp: Mặc định - Nam - 39 - Trắng đã tồn tại với mức giá khác 
(giảm 5% vs 10%). Không được có cùng một đôi giày với 2 mức giá khác nhau."
```

---

## Test Case 2: Thêm Biến Thể Trùng Hoàn Toàn → Cộng Dồn

**Các bước:**

1. Tạo sản phẩm mới tương tự Test Case 1

2. Thêm 2 biến thể GIỐNG HỆTẾT NHAU:
   
   **Biến thể 1:**
   - Mặc định | Nam | 39 | Trắng | 950,000 (5%) | SL 10

   **Biến thể 2:**
   - Mặc định | Nam | 39 | Trắng | 950,000 (5%) | SL 5

3. Click "Thêm sản phẩm"

**Kết quả mong đợi:**

```
✅ Alert màu xanh:
"Thêm sản phẩm thành công."

✅ Redirect về danh sách sản phẩm
✅ Click vào chi tiết sản phẩm → Chỉ có 1 biến thể với SL = 15
```

---

## Test Case 3: Thêm Nhiều Biến Thể Khác Nhau → OK

**Các bước:**

1. Tạo sản phẩm với 3 biến thể khác nhau:
   - Nam | 39 | Trắng | 950,000 | SL 10
   - Nam | 40 | Trắng | 950,000 | SL 10
   - Nữ | 39 | Đen | 900,000 | SL 10

2. Submit

**Kết quả mong đợi:**

```
✅ Thành công
✅ Có đúng 3 biến thể trong database
```

---

## Kiểm Tra Database

```sql
-- Xem sản phẩm test
SELECT * FROM products 
WHERE sku LIKE 'TEST-%' 
ORDER BY created_at DESC;

-- Xem biến thể (thay product_id = XX)
SELECT 
    variant_id,
    sku,
    model,
    size,
    color,
    price,
    quantity,
    ROUND((1000000 - price) / 1000000 * 100, 2) AS discount_percent
FROM product_variants
WHERE product_id = XX
ORDER BY model, size, color;
```

---

## Automated Test

```bash
# Chạy test suite tự động
cd c:\xampp\htdocs\sport-shoes-website
php test_variant_duplicate.php
```

**Kết quả:**
```
✅ TEST 1: PASS
✅ TEST 2: PASS (báo lỗi đúng)
✅ TEST 3: PASS (cộng dồn SL)
✅ TEST 4: PASS (mix case)
```

---

## Thay Đổi Code

### 1. Backend: `app/Models/Product.php`

**Method:** `saveVariants()` (line ~90-150)

**Thay đổi:**
- ✅ Thêm logic tính % discount
- ✅ Kiểm tra trùng lặp bằng key: `model|size|color`
- ✅ So sánh % discount để phát hiện conflict
- ✅ Throw `ValidationException` nếu trùng với % khác
- ✅ Cộng dồn số lượng nếu trùng hoàn toàn

### 2. Backend: `app/Controllers/Admin/ProductAdminController.php`

**Method:** `create()` (line ~125-165)

**Thay đổi:**
- ✅ Thêm `Auth::initSession()` trước khi set session message
- ✅ Thêm error logging để debug

### 3. Frontend: `public/assets/js/admin_product.js`

**Line:** ~100-150

**Thay đổi:**
- ✅ **BỎ** validation trùng lặp ở client (line ~110-140)
- ✅ Để backend xử lý toàn bộ
- ✅ Comment giải thích lý do

**Lý do:** Client không thể biết được % discount để validate đúng. Chỉ backend mới có đủ thông tin.

---

## Debug

Nếu vẫn không thấy thông báo:

### 1. Kiểm tra session

```php
// Thêm vào đầu product_create.php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
var_dump($_SESSION);
?>
```

### 2. Kiểm tra redirect

```php
// Trong ProductAdminController::create(), thêm log
error_log("Setting error session: " . $e->getMessage());
error_log("Session ID: " . session_id());
```

### 3. Kiểm tra browser console

- F12 → Console tab
- Xem có lỗi JavaScript nào không

### 4. Kiểm tra Network tab

- F12 → Network tab
- Submit form → Xem response có redirect đúng không

---

## Expected Flow

```
User Submit Form
    ↓
[JavaScript Validation]
    ├─ Validate format (tên, SKU, giá, ...)
    ├─ KHÔNG kiểm tra trùng biến thể
    └─ Submit to server
        ↓
[PHP Controller]
    ├─ Get form data
    ├─ Create product
    └─ Call Product::saveVariants()
            ↓
        [Product Model]
            ├─ Tính % discount cho từng biến thể
            ├─ Tạo unique key (model|size|color)
            ├─ Kiểm tra trùng:
            │   ├─ Trùng key + Khác % → ❌ Throw ValidationException
            │   ├─ Trùng key + Giống % → ✅ Cộng dồn số lượng
            │   └─ Không trùng → ✅ Thêm mới
            └─ Insert vào DB
                ↓
            [SUCCESS]
                ├─ Set $_SESSION['success']
                └─ Redirect to /admin/products
            
            [ERROR - ValidationException]
                ├─ Catch in Controller
                ├─ Set $_SESSION['error']
                └─ Redirect to /admin/products/create
                    ↓
                [View]
                    ├─ Hiển thị alert màu đỏ
                    └─ Unset $_SESSION['error']
```

---

## Files Liên Quan

```
app/
├── Models/
│   └── Product.php                          ← Logic kiểm tra trùng
├── Controllers/
│   └── Admin/
│       └── ProductAdminController.php       ← Xử lý form, session
└── Exceptions/
    └── ValidationException.php              ← Custom exception

public/
└── assets/
    └── js/
        └── admin_product.js                 ← Validation client (đã bỏ check trùng)

views/
└── admin/
    ├── product_create.php                   ← Form thêm sản phẩm
    └── components/
        └── variant_matrix_generator.php     ← Ma trận biến thể

tests/
├── test_variant_duplicate.php               ← Automated test
├── test_duplicate_ui.md                     ← Manual test guide
└── demo_variant_rules.php                   ← Visual demo
```

---

## Troubleshooting

### Lỗi: "Call to a member function on null"

**Nguyên nhân:** Session chưa được start

**Giải pháp:** Đã thêm `Auth::initSession()` trước khi set session message

---

### Lỗi: Không redirect sau submit

**Nguyên nhân:** JavaScript có lỗi, chặn form submit

**Giải pháp:** Kiểm tra browser console (F12)

---

### Lỗi: Thông báo không hiển thị sau redirect

**Nguyên nhân:** 
1. Session cookie không được set (check browser settings)
2. View đã unset session trước khi hiển thị

**Giải pháp:** Kiểm tra `$_SESSION` trong view bằng `var_dump()`

---

## Summary

✅ **Đã sửa:** JavaScript không còn chặn form khi có biến thể trùng  
✅ **Đã sửa:** Backend xử lý đúng logic trùng lặp  
✅ **Đã sửa:** Session message hiển thị đúng  
✅ **Đã test:** All automated tests PASS  

**Bây giờ có thể:**
- ✅ Thêm biến thể trùng hoàn toàn → Tự động cộng dồn SL
- ❌ Thêm biến thể trùng nhưng % giảm khác → Báo lỗi rõ ràng
- ✅ Xem thông báo thành công/lỗi sau mỗi lần submit
