# Quy Tắc Quản Lý Biến Thể Sản Phẩm

## Tổng quan

Hệ thống quản lý biến thể sản phẩm cho phép tạo nhiều phiên bản của cùng một sản phẩm với các thuộc tính khác nhau (size, màu sắc, model) và giá bán khác nhau.

## Quy tắc nghiệp vụ (Phiên bản mới - Linh hoạt hơn)

### 1. Định nghĩa biến thể duy nhất

Một biến thể được định nghĩa là **DUY NHẤT** dựa trên 3 yếu tố:
- **Model** (Mẫu giày): VD: "Bản Thường", "Bản Cao Cấp", "Mặc định"
- **Size** (Kích cỡ): VD: "Nam - 42", "Nữ - 38", "Trẻ em - 35"
- **Color** (Màu sắc): VD: "Trắng", "Đen", "Xanh dương"

### 2. Xử lý biến thể trùng lặp

#### 2.1 Trường hợp 1: Thêm biến thể đã tồn tại (cùng Model + Size + Màu)

**Hành động:** 
- Cộng dồn số lượng
- Cập nhật giá theo giá mới (cho phép thay đổi giá)
- Cập nhật SKU theo SKU mới

**Ví dụ:**
```
Biến thể đã có trong DB:
- Model: Mặc định
- Size: Nam - 42
- Màu: Trắng
- Giá: 190,000 VNĐ
- Số lượng: 10

Người dùng thêm lại:
- Model: Mặc định
- Size: Nam - 42
- Màu: Trắng
- Giá: 180,000 VNĐ (giá mới)
- Số lượng: 5

Kết quả:
✓ Số lượng được cộng dồn: 10 + 5 = 15
✓ Giá được cập nhật: 180,000 VNĐ (lấy giá mới)
✓ Thông báo: "Đã cập nhật biến thể và cộng dồn số lượng"
```

#### 2.2 Trường hợp 2: Nhiều dòng trùng trong cùng form submit

**Hành động:** Tự động gộp lại, lấy giá và SKU của dòng cuối cùng

**Ví dụ:**
```
Form submit có:
Dòng 1: Model: Mặc định, Size: Nam - 42, Màu: Trắng, Giá: 190,000, Qty: 10
Dòng 2: Model: Mặc định, Size: Nam - 42, Màu: Trắng, Giá: 180,000, Qty: 5
Dòng 3: Model: Mặc định, Size: Nam - 43, Màu: Đen, Giá: 185,000, Qty: 8

Kết quả sau xử lý:
✓ Biến thể 1 (Mặc định-Nam42-Trắng): Qty=15 (10+5), Giá=180,000 (lấy dòng 2)
✓ Biến thể 2 (Mặc định-Nam43-Đen): Qty=8, Giá=185,000
```

#### 2.3 Trường hợp 3: Biến thể hoàn toàn mới

**Hành động:** Thêm mới vào danh sách

**Ví dụ:**
```
Biến thể đã có:
- Model: Mặc định, Size: Nam - 42, Màu: Trắng

Người dùng thêm:
- Model: Mặc định, Size: Nam - 43, Màu: Đen ← Khác size VÀ màu

Kết quả:
✓ Thêm biến thể mới thành công
```

### 3. Tính giá biến thể

Giá biến thể được tính dựa trên:
1. **Giá bán mặc định** (Giá gốc sản phẩm)
2. **Giảm giá từ Model** (nếu có)
3. **Giảm giá từ Size** (%)
4. **Giảm giá từ Màu** (%)

**Công thức:**
```
Giá Model = Giá gốc (hoặc giá đã nhập cho model đó)
Tổng % giảm = % giảm Size + % giảm Màu
Giá cuối = Giá Model × (1 - Tổng % giảm / 100)
```

**Ví dụ:**
```
Giá gốc sản phẩm: 200,000 VNĐ
Model "Mặc định": 200,000 VNĐ (không tăng giá)
Size "Nam - 42": giảm 5%
Màu "Trắng": giảm 5%
→ Giá cuối = 200,000 × (1 - 10/100) = 180,000 VNĐ
```

**Ràng buộc:**
- Giá biến thể **KHÔNG được vượt** giá bán mặc định
- Giá biến thể **PHẢI > 0**

### 4. Xử lý trong Ma Trận Tạo Biến Thể (UI)

#### Frontend (variant_matrix_generator.php)

**Khi người dùng nhấn "Thêm các biến thể vừa chọn":**

1. **Snapshot các row hiện có** TRƯỚC KHI thêm mới (tránh false positive)
2. **Duyệt qua từng tổ hợp** (Model × Size × Color)
3. **Kiểm tra từng biến thể:**
   - Nếu chưa tồn tại → Thêm mới vào bảng
   - Nếu đã tồn tại VÀ giá giống → Cộng dồn số lượng, highlight xanh
   - Nếu đã tồn tại NHƯNG giá khác → Báo lỗi, dừng ngay

**Thông báo sau khi xử lý:**
```javascript
// Tất cả mới
"Đã thêm 5 biến thể mới thành công!"

// Một phần mới, một phần cộng dồn
"Đã thêm 3 biến thể mới và cộng dồn số lượng cho 2 biến thể đã tồn tại (highlight xanh)."

// Tất cả đều cộng dồn
"Đã cộng dồn số lượng cho các biến thể đã tồn tại (highlight xanh)."

// Có lỗi (giá khác)
"Biến thể Mặc định - Nam 42 - Trắng đã tồn tại với giá 190,000 VNĐ, 
 nhưng bạn đang thêm với giá 180,000 VNĐ. Không thể có cùng biến thể với 2 mức giá khác nhau!"
```

#### Backend (Product Model - saveVariants)

**Logic xử lý 3 tầng:**

1. **Tầng 1: Kiểm tra trùng trong form submit**
   - Gộp các biến thể trùng trong cùng 1 lần submit
   - Nếu trùng nhưng giá khác → Throw ValidationException

2. **Tầng 2: So sánh với DB hiện có**
   - Merge với các biến thể đã lưu trong DB
   - Nếu trùng nhưng giá khác → Throw ValidationException
   - Nếu trùng và giá giống → Cộng dồn số lượng

3. **Tầng 3: Thực thi DB**
   - INSERT biến thể mới
   - UPDATE biến thể đã tồn tại (với số lượng đã cộng dồn)
   - DELETE biến thể không còn trong danh sách

### 5. Test Cases

#### Test Case 1: Thêm biến thể mới
```php
Input:
- Model: Mặc định, Size: Nam - 42, Màu: Trắng, Giá: 190,000, Qty: 10

Expected:
✓ Thêm thành công, variant_id mới được tạo
```

#### Test Case 2: Cộng dồn biến thể giống hệt
```php
Input (lần 1):
- Model: Mặc định, Size: Nam - 42, Màu: Trắng, Giá: 190,000, Qty: 10

Input (lần 2):
- Model: Mặc định, Size: Nam - 42, Màu: Trắng, Giá: 190,000, Qty: 5

Expected:
✓ Số lượng = 15 (10 + 5)
✓ variant_id không đổi
```

#### Test Case 3: Báo lỗi khi giá khác
```php
Input (DB có sẵn):
- Model: Mặc định, Size: Nam - 42, Màu: Trắng, Giá: 190,000, Qty: 10

Input (submit mới):
- Model: Mặc định, Size: Nam - 42, Màu: Trắng, Giá: 180,000, Qty: 5

Expected:
✗ ValidationException: "Biến thể ... đã tồn tại với mức giá khác ..."
```

#### Test Case 4: Thêm nhiều biến thể cùng lúc (mixed)
```php
Input:
[
  {Model: Mặc định, Size: Nam - 42, Màu: Trắng, Giá: 190,000, Qty: 10}, // Mới
  {Model: Mặc định, Size: Nam - 43, Màu: Đen, Giá: 185,000, Qty: 8},   // Mới
  {Model: Mặc định, Size: Nam - 42, Màu: Trắng, Giá: 190,000, Qty: 5}  // Trùng với variant 1
]

Expected:
✓ 2 biến thể mới được tạo (Nam-43-Đen, và 1 khác)
✓ Biến thể Nam-42-Trắng có số lượng = 15 (10 + 5 cộng dồn)
```

### 6. File liên quan

```
app/Models/Product.php
└── saveVariants()    # Logic backend xử lý biến thể

views/admin/components/variant_matrix_generator.php
└── JavaScript        # Logic frontend kiểm tra trùng lặp

test_variant_duplicate.php
└── Test suite        # File test các kịch bản
```

### 7. Lưu ý khi phát triển

1. **Luôn so sánh giá dùng `abs(a - b) > 0.01`** thay vì `===` (tránh lỗi float)
2. **Snapshot các row trước khi thêm mới** trong JS (tránh query lại row vừa append)
3. **Throw ValidationException sớm** khi phát hiện lỗi (fail-fast principle)
4. **Cộng dồn số lượng** chỉ khi Model + Size + Color + Price giống 100%
5. **Unique constraint trong DB** đảm bảo `(product_id, model, size, color)` là duy nhất

### 8. Migration liên quan

```sql
-- 016_update_product_variants_structure.sql
ALTER TABLE product_variants 
  ADD UNIQUE KEY unique_variant (product_id, model, color, size);
```

Constraint này đảm bảo không có 2 biến thể cùng (product_id, model, color, size) trong DB.

---

## Tóm tắt (Phiên bản Linh hoạt)

| Tình huống | Model | Size | Màu | Giá | Hành động |
|------------|-------|------|-----|-----|-----------|
| Trùng với DB | Giống | Giống | Giống | Bất kỳ | ✓ Cộng dồn số lượng + Cập nhật giá mới |
| Trùng trong form | Giống | Giống | Giống | Bất kỳ | ✓ Gộp lại, lấy giá cuối + Cộng số lượng |
| Hoàn toàn mới | Khác bất kỳ yếu tố nào | | | | ✓ Thêm mới |

**Nguyên tắc mới:** Hệ thống linh hoạt hơn, cho phép:
- ✅ Cập nhật giá khi thêm lại biến thể đã tồn tại
- ✅ Tự động gộp các dòng trùng trong form
- ✅ Cộng dồn số lượng mọi trường hợp trùng model+size+màu

**Lưu ý:** Nếu có nhiều dòng trùng trong form, hệ thống sẽ lấy giá và SKU của **dòng cuối cùng**.
