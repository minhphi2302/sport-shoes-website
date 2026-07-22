# TASK-007: Admin — Quản lý Sản phẩm, Danh mục, Thương hiệu

## 1. Thông tin chung
- **Trạng thái:** To Do
- **Người được giao:** Phí Văn Minh (Trưởng nhóm)
- **Role AI khuyên dùng:** 🛒 Feature Developer + 🎨 UI Developer
- **Ngày tạo:** 2026-07-22
- **Branch:** `feat/quanly-sanpham`
- **Phụ thuộc:** TASK-001 (bảng products/categories/brands), TASK-002 (AdminController)
- **Ưu tiên:** 🟡 HIGH — cần sản phẩm để test TASK-003/004/005

## 2. Mô tả yêu cầu (Yêu cầu nghiệp vụ)

Admin CRUD đầy đủ cho:
1. **Danh mục (`/admin/categories`):** Xem danh sách, thêm, sửa, xóa. Không xóa nếu còn sản phẩm.
2. **Thương hiệu (`/admin/brands`):** Xem danh sách, thêm, sửa, xóa. Không xóa nếu còn sản phẩm.
3. **Sản phẩm (`/admin/products`):** Xem danh sách (có phân trang + tìm kiếm), thêm, sửa, xóa. Upload ảnh.

## 3. Tiêu chí nghiệm thu (Acceptance Criteria)

### Danh mục & Thương hiệu:
- [ ] AC1: Thêm danh mục/thương hiệu thành công → hiển thị trong danh sách
- [ ] AC2: Tên thương hiệu trùng → báo lỗi "Tên thương hiệu đã tồn tại"
- [ ] AC3: Xóa danh mục/thương hiệu còn sản phẩm → báo lỗi, không xóa được
- [ ] AC4: Xóa danh mục/thương hiệu không còn sản phẩm → xóa thành công

### Sản phẩm:
- [ ] AC5: Thêm sản phẩm với ảnh → ảnh lưu đúng vào `public/uploads/products/`, tên file được đổi ngẫu nhiên
- [ ] AC6: Upload file không phải ảnh (VD: `.php`, `.exe`) → báo lỗi "Định dạng không hợp lệ", không lưu file
- [ ] AC7: Upload ảnh > 2MB → báo lỗi "Ảnh vượt quá 2MB"
- [ ] AC8: `sale_price > price` → báo lỗi "Giá khuyến mãi không được lớn hơn giá bán"
- [ ] AC9: `price <= 0` → báo lỗi validate
- [ ] AC10: SKU trùng → báo lỗi "Mã sản phẩm đã tồn tại"
- [ ] AC11: Sửa sản phẩm giữ ảnh cũ nếu không upload ảnh mới
- [ ] AC12: Xóa sản phẩm → xóa cả file ảnh trên server (nếu file tồn tại)
- [ ] AC13: Danh sách sản phẩm admin có phân trang và tìm kiếm theo tên

## 4. Ghi chú kỹ thuật & Ràng buộc (Tech Notes)

### Model/Bảng liên quan: `products`, `categories`, `brands`

### File dự kiến cần tạo/sửa:
- `app/Models/Product.php` — thêm: `create(array $data)`, `update(int $id, array $data)`, `delete(int $id)`, `skuExists(string $sku)`
- `app/Models/Category.php` — thêm: `create()`, `update()`, `delete()`, `hasProducts(int $id)`
- `app/Models/Brand.php` — thêm: `create()`, `update()`, `delete()`, `hasProducts(int $id)`
- `app/Controllers/Admin/CategoryController.php`
- `app/Controllers/Admin/BrandController.php`
- `app/Controllers/Admin/ProductAdminController.php`
- `app/Core/FileUploader.php` — utility class xử lý upload an toàn
- `views/admin/category_list.php`, `views/admin/category_form.php`
- `views/admin/brand_list.php`, `views/admin/brand_form.php`
- `views/admin/product_list.php`, `views/admin/product_form.php`
- `public/uploads/products/.htaccess` — chặn thực thi PHP

### Upload an toàn (AGENTS.md mục 4 — BẮT BUỘC):
```php
// app/Core/FileUploader.php
public function uploadProductImage(array $file): string
{
    // 1. Kiểm tra MIME type thật — không tin đuôi file
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];

    if (!in_array($mime, $allowed, true)) {
        throw new ValidationException('image', 'Định dạng ảnh không hợp lệ (chỉ jpg/png/webp)');
    }

    // 2. Giới hạn dung lượng 2MB
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new ValidationException('image', 'Ảnh vượt quá 2MB');
    }

    // 3. Đổi tên ngẫu nhiên — không giữ tên gốc
    $ext = match($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    };
    $newName = uniqid('product_', true) . '.' . $ext;

    // 4. Move vào thư mục uploads (không thực thi PHP)
    move_uploaded_file($file['tmp_name'], PRODUCT_IMAGE_DIR . '/' . $newName);
    return $newName;
}
```

**File `.htaccess` trong `public/uploads/products/`:**
```
php_flag engine off
Options -ExecCGI
AddType text/plain .php .php3 .phtml .phar
```

### Business Rules liên quan (AGENTS.md mục 10):
- PRODUCTS: `price > 0`, `sale_price <= price`, tồn kho `>= 0`
- PRODUCTS: SKU duy nhất, thuộc 1 category và 1 brand
- CATEGORIES/BRANDS: không xóa nếu còn sản phẩm

```php
// Ví dụ check trong Category Model trước DELETE
public function delete(int $id): void
{
    // Không xóa nếu còn sản phẩm — business rule CATEGORIES
    $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM products WHERE category_id = :id');
    $stmt->execute(['id' => $id]);
    if ($stmt->fetchColumn() > 0) {
        throw new CannotDeleteException('Không thể xóa danh mục còn sản phẩm');
    }
    // ... DELETE
}
```

## 5. Security Checklist bắt buộc cho Task này
- [ ] Upload: MIME type check bằng `finfo_file()`, không chỉ kiểm tra extension
- [ ] Upload: Giới hạn 2MB, đổi tên file ngẫu nhiên
- [ ] Upload: Thư mục `public/uploads/products/` có `.htaccess` chặn thực thi PHP
- [ ] Admin routes qua `AdminController` middleware (không ai ngoài admin CRUD được)
- [ ] Prepared Statement cho mọi SQL
- [ ] `htmlspecialchars()` cho mọi output tên danh mục, thương hiệu, sản phẩm

## 6. PHPUnit Test cần viết (AGENTS.md mục 11)

```
tests/Unit/ProductModelTest.php:
- test_create_product_with_sale_price_greater_than_price_throws_exception
- test_create_product_with_negative_price_throws_exception
- test_create_product_with_duplicate_sku_throws_exception

tests/Unit/CategoryModelTest.php:
- test_delete_category_with_products_throws_exception
- test_delete_category_without_products_succeeds

tests/Unit/BrandModelTest.php:
- test_delete_brand_with_products_throws_exception
- test_create_brand_with_duplicate_name_throws_exception
```
