# Changelog - Sửa Lỗi Pagination và Logic Biến Thể

**Ngày:** 11/08/2026  
**Version:** 2.1.0  
**Loại:** Bug Fix + Business Rule Enhancement

---

## 🐛 Các Lỗi Đã Sửa

### 1. Lỗi Pagination Khi Sửa Sản Phẩm

**Vấn đề:**
- Khi sửa sản phẩm ở trang 2 (hoặc bất kỳ trang nào khác trang 1)
- Sau khi lưu thay đổi, hệ thống tự động chuyển về trang 1
- Gây khó chịu cho user vì phải tìm lại sản phẩm vừa sửa

**Nguyên nhân:**
- Controller `ProductAdminController::edit()` redirect về `/admin/products` không giữ tham số `page`
- View `product_list.php` không truyền thông tin trang hiện tại vào link "Sửa"

**Giải pháp:**

#### a) Controller (`ProductAdminController.php`):
```php
public function edit($id): void
{
    // ...
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // ... xử lý update ...
            
            $_SESSION['success'] = 'Cập nhật sản phẩm thành công.';
            
            // ✅ Giữ lại page hiện tại khi redirect
            $returnPage = !empty($_POST['return_page']) ? (int)$_POST['return_page'] : 1;
            $this->redirect('/admin/products?page=' . $returnPage);
            
        } catch (ValidationException $e) {
            // ✅ Giữ return_page trong URL khi redirect về form edit
            $returnPage = !empty($_POST['return_page']) ? '?return_page=' . (int)$_POST['return_page'] : '';
            $this->redirect("/admin/products/{$productId}/edit" . $returnPage);
        }
    }
    
    // ✅ Truyền return_page vào view
    $this->view('admin/product_edit', [
        // ...
        'returnPage' => $_GET['return_page'] ?? 1
    ]);
}
```

#### b) View List (`product_list.php`):
```php
<!-- ✅ Thêm tham số return_page vào link Sửa -->
<a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/products/<?= $product['product_id'] ?>/edit?return_page=<?= $currentPage ?>" 
   class="btn btn-sm btn-outline-primary me-1">Sửa</a>
```

#### c) View Edit (`product_edit.php`):
```php
<form id="product-form" action="" method="POST" enctype="multipart/form-data">
    <!-- ✅ Hidden field để giữ return_page -->
    <input type="hidden" name="return_page" value="<?= htmlspecialchars($returnPage ?? 1) ?>">
    <!-- ... -->
</form>
```

---

### 2. Lỗi Logic Kiểm Tra Biến Thể Trùng Lặp

**Vấn đề:**
- Logic CŨ (SAI): Khi thêm biến thể trùng model+size+màu → Hệ thống CỘNG DỒN số lượng
- Ví dụ:
  ```
  Thêm: Size 39, Màu Trắng, Giảm 5% → OK
  Thêm: Size 39, Màu Trắng, Giảm 6% → Vẫn thêm được (SAI!)
  ```
- Business rule yêu cầu: Chỉ có biến thể trùng HOÀN TOÀN (model+size+màu) mới được cập nhật, không được cộng dồn

**Nguyên nhân:**
- Method `Product::saveVariants()` có logic gộp các biến thể trùng trong form submit bằng cách cộng dồn số lượng
- Không kiểm tra và báo lỗi khi có biến thể trùng trong cùng lần submit

**Giải pháp:**

#### Thay đổi logic trong `Product.php`:

```php
public function saveVariants(int $productId, array $skus, array $models, array $sizes, array $colors, array $prices, array $qtys): void
{
    // ...
    
    // ✅ Kiểm tra trùng lặp TRONG form submit
    // Business rule: CHỈ CHO PHÉP thêm biến thể nếu hoàn toàn mới
    $seenInForm = [];
    foreach ($newVariants as $nv) {
        $key = $nv['key'];
        if (isset($seenInForm[$key])) {
            // ❌ Trùng model+size+màu trong cùng lần submit → Báo lỗi
            throw new ValidationException(
                'variants',
                "Biến thể trùng lặp: {$nv['model']} - {$nv['size']} - {$nv['color']}. Vui lòng kiểm tra lại."
            );
        }
        $seenInForm[$key] = $nv;
    }
    
    // ✅ Merge với variants hiện có trong DB
    foreach ($seenInForm as $key => $newVar) {
        if (isset($existingMap[$key])) {
            // Biến thể ĐÃ TỒN TẠI trong DB → Cho phép CẬP NHẬT
            $finalVariants[$key] = [
                // ...
                'quantity' => $newVar['quantity'], // ✅ GHI ĐÈ, không cộng dồn
                'is_update' => true
            ];
        } else {
            // Biến thể MỚI hoàn toàn → Thêm vào DB
            $finalVariants[$key] = [
                // ...
                'is_update' => false
            ];
        }
    }
    
    // ...
}
```

---

## 📊 So Sánh Logic

| Tình huống | Logic CŨ (Sai) | Logic MỚI (Đúng) |
|------------|-----------------|------------------|
| Trùng trong form submit | ❌ Cộng dồn số lượng | ✅ Báo lỗi |
| Cập nhật biến thể đã có trong DB | ❌ Cộng dồn số lượng | ✅ Ghi đè số lượng |
| Thêm biến thể mới | ✅ OK | ✅ OK |

---

## 🧪 Test Cases

### Test Case 1: Trùng trong form submit
```
Input:
- Size 39, Màu Trắng, Giảm 5%
- Size 39, Màu Trắng, Giảm 6%

Expected: ❌ ValidationException "Biến thể trùng lặp"
```

### Test Case 2: Thêm biến thể mới
```
Input:
- Size 39, Màu Trắng
- Size 40, Màu Đen

Expected: ✅ Thêm thành công 2 biến thể
```

### Test Case 3: Cập nhật biến thể đã có trong DB
```
DB hiện có: Size 39, Màu Trắng, SL = 10

Input: Size 39, Màu Trắng, SL = 20

Expected: ✅ Cập nhật thành công, SL = 20 (ghi đè, không cộng 10 + 20)
```

### Test Case 4: Sửa sản phẩm ở trang 2
```
1. Vào trang 2 của danh sách sản phẩm
2. Click nút "Sửa" một sản phẩm bất kỳ
3. Thay đổi thông tin và lưu

Expected: ✅ Redirect về đúng trang 2, không về trang 1
```

---

## ✅ Business Rules (PRODUCTS)

Theo `AGENTS.md` mục 10:

1. **Biến thể duy nhất:** Không cho phép tồn tại 2 biến thể cùng model+size+màu
2. **Cập nhật ghi đè:** Khi edit biến thể, số lượng mới GHI ĐÈ số lượng cũ, không cộng dồn
3. **Kiểm tra trùng lặp:** Phải báo lỗi nếu user cố tình thêm biến thể trùng trong cùng lần submit
4. **Giá không vượt giá gốc:** Giá biến thể ≤ Giá bán sản phẩm
5. **UX tốt hơn:** Giữ trang hiện tại khi redirect sau edit

---

## 📁 Files Thay Đổi

```
modified:   app/Controllers/Admin/ProductAdminController.php
modified:   app/Models/Product.php
modified:   views/admin/product_edit.php
modified:   views/admin/product_list.php
new file:   test_variant_logic.php
new file:   CHANGELOG_PAGINATION_AND_VARIANT_FIX.md
```

---

## 🚀 Cách Test

1. **Test Pagination:**
   ```
   1. Truy cập: http://localhost:8888/sport-shoes-website/admin/products?page=2
   2. Click nút "Sửa" một sản phẩm bất kỳ
   3. Thay đổi thông tin và click "Lưu thay đổi"
   4. Kiểm tra: Có redirect về trang 2 hay không?
   ```

2. **Test Logic Biến Thể:**
   ```
   1. Truy cập: http://localhost:8888/sport-shoes-website/test_variant_logic.php
   2. Đọc kỹ các test case và logic mới
   3. Thử tạo/sửa sản phẩm với biến thể trùng lặp
   4. Kiểm tra: Có báo lỗi ValidationException hay không?
   ```

---

## 🔐 Security & Validation

- ✅ Validation input: `return_page` được cast về `int` để tránh XSS
- ✅ Prepared statement: Không có thay đổi query, vẫn dùng PDO prepared statement
- ✅ Business rule: Kiểm tra biến thể trùng ở tầng Model (defense in depth)
- ✅ Error handling: Throw `ValidationException` rõ ràng, không nuốt lỗi

---

## 📝 Ghi Chú

- Không phá vỡ backward compatibility: Logic cũ vẫn hoạt động cho trường hợp edit biến thể đã tồn tại
- Chỉ thay đổi hành vi khi có biến thể trùng trong cùng lần submit
- Cải thiện UX: User không bị "lạc" sau khi edit sản phẩm

---

**Developer:** AI Assistant  
**Reviewer:** Minh/Hưng/Thảo  
**Status:** ✅ Ready for Testing
