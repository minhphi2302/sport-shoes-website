# TASK-003: UI Bootstrap — Trang chủ & Danh sách / Chi tiết Sản phẩm

## 1. Thông tin chung
- **Trạng thái:** To Do
- **Người được giao:** Nguyễn Mạnh Hưng
- **Role AI khuyên dùng:** 🎨 UI Developer
- **Ngày tạo:** 2026-07-22
- **Branch:** `feat/tim-kiem-loc`
- **Phụ thuộc:** TASK-001 (cần bảng products/categories/brands)
- **Ưu tiên:** 🟡 HIGH — song song với TASK-002

## 2. Mô tả yêu cầu (Yêu cầu nghiệp vụ)

Xây dựng giao diện phía khách hàng (Guest & Customer) bao gồm:

1. **Layout chung:** Header (logo, nav, giỏ hàng icon, đăng nhập/đăng xuất), Footer, sidebar hoặc top filter bar. Tái sử dụng qua `views/client/layouts/`.
2. **Trang chủ (`/`):** Banner slider, section sản phẩm nổi bật (mới nhất/sale), danh mục nhanh.
3. **Danh sách sản phẩm (`/products`):** Grid sản phẩm, lọc theo danh mục + thương hiệu (query param), tìm kiếm theo tên, phân trang.
4. **Chi tiết sản phẩm (`/products/{id}`):** Ảnh, tên, giá (hiển thị giá khuyến mãi nếu có), mô tả, tồn kho, nút "Thêm vào giỏ".

## 3. Tiêu chí nghiệm thu (Acceptance Criteria)

- [ ] AC1: Trang chủ load được với header/footer chuẩn, hiển thị ít nhất 6 sản phẩm nổi bật từ DB
- [ ] AC2: Danh sách sản phẩm hiển thị đúng dạng grid Bootstrap, mỗi card có ảnh, tên, giá
- [ ] AC3: Lọc theo danh mục → URL thay đổi (`?category_id=1`), kết quả đúng, không cần reload toàn trang
- [ ] AC4: Lọc theo thương hiệu → tương tự AC3
- [ ] AC5: Tìm kiếm theo tên → hiển thị đúng kết quả, input không bị XSS
- [ ] AC6: Phân trang hoạt động (20 sản phẩm/trang), nút Previous/Next đúng
- [ ] AC7: Trang chi tiết hiển thị đúng thông tin; nếu `sale_price` có giá trị → gạch giá gốc, hiển thị giá sale nổi bật
- [ ] AC8: Nút "Thêm vào giỏ" trên trang chi tiết hoạt động (redirect đến TASK-004)
- [ ] AC9: Responsive: hiển thị đúng trên mobile (375px) và desktop (1280px)
- [ ] AC10: Không có lỗi console JS, không có output PHP error ra màn hình

## 4. Ghi chú kỹ thuật & Ràng buộc (Tech Notes)

### Model/Bảng liên quan: `products`, `categories`, `brands`

### File dự kiến cần tạo/sửa:
- `app/Models/Product.php` — methods: `getAll(array $filters, int $page)`, `findById(int $id)`, `countAll(array $filters)`, `getFeatured(int $limit)`
- `app/Models/Category.php` — method: `getAll()`
- `app/Models/Brand.php` — method: `getAll()`
- `app/Controllers/ProductController.php` — actions: `index()` (danh sách + filter), `show(int $id)`
- `views/client/layouts/header.php`
- `views/client/layouts/footer.php`
- `views/client/home.php`
- `views/client/product_list.php`
- `views/client/product_detail.php`
- `public/assets/css/style.css` — custom styles bổ sung Bootstrap
- Route đăng ký trong `public/index.php`: `GET /`, `GET /products`, `GET /products/{id}`

### Phân trang:
```php
// Dùng LIMIT + OFFSET — không SELECT * toàn bộ
$perPage = 20;
$offset = ($page - 1) * $perPage;
// SELECT ... LIMIT :perPage OFFSET :offset
```

### Filter query (không N+1):
```sql
-- 1 query duy nhất với JOIN, không query riêng trong vòng lặp
SELECT p.*, c.name AS category_name, b.name AS brand_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.category_id
LEFT JOIN brands b ON p.brand_id = b.brand_id
WHERE p.status = 'active'
  AND (:category_id IS NULL OR p.category_id = :category_id)
  AND (:brand_id IS NULL OR p.brand_id = :brand_id)
  AND (:search IS NULL OR p.name LIKE :search)
ORDER BY p.created_at DESC
LIMIT :perPage OFFSET :offset
```

### Business Rules liên quan (AGENTS.md mục 10 — PRODUCTS):
- Chỉ hiển thị sản phẩm `status = 'active'`
- Hiển thị đúng giá: nếu `sale_price` IS NOT NULL và `sale_price < price` → dùng `sale_price`

### UI Guidelines (AGENTS.md mục 12):
- Ngôn ngữ: Tiếng Việt
- Dùng Bootstrap 5 grid system
- Responsive test mobile + desktop

## 5. Security Checklist bắt buộc cho Task này
- [ ] Mọi output dữ liệu từ DB ra HTML qua `htmlspecialchars()` (tên sản phẩm, mô tả, v.v.)
- [ ] Input search param (`$_GET['search']`) sanitize trước khi dùng trong SQL (Prepared Statement)
- [ ] Validate `$_GET['page']`, `$_GET['category_id']`, `$_GET['brand_id']` là số nguyên hợp lệ trước khi dùng
- [ ] Không expose thông tin DB error ra view (dùng global error handler)

## 6. Lưu ý phối hợp với các task khác

> **Header component:** Cần kiểm tra `Auth::check()` để hiển thị "Đăng nhập / Đăng ký" hay "Xin chào [Tên] | Đăng xuất". Phụ thuộc vào `Auth` helper của TASK-002. Nếu TASK-002 chưa xong, dùng `$_SESSION['user']` tạm thời.

> **Giỏ hàng icon:** Header cần hiển thị số lượng item trong giỏ. Lấy từ `$_SESSION['cart']` (sẽ implement ở TASK-004).
