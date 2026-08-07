# TASK-001: Database Schema & Migration Setup

## 1. Thông tin chung
- **Trạng thái:** Done
- **Người được giao:** Phí Văn Minh (Trưởng nhóm)
- **Role AI khuyên dùng:** 🏗️ PHP Architect
- **Ngày tạo:** 2026-07-22
- **Branch:** `feat/database-schema`
- **Ưu tiên:** 🔴 CRITICAL — Phải hoàn thành trước mọi task khác

## 2. Mô tả yêu cầu (Yêu cầu nghiệp vụ)

Thiết lập toàn bộ cấu trúc database cho hệ thống, bao gồm:
- 6 bảng theo ERD trong SRS: `USERS`, `CATEGORIES`, `BRANDS`, `PRODUCTS`, `ORDERS`, `ORDER_DETAILS`
- Bảng `migrations` để theo dõi migration đã chạy
- Chuyển từ file `schema.sql` tĩnh sang **hệ thống migration có version** theo AGENTS.md mục 7
- Script `migrate.php` tự động chạy các migration chưa được áp dụng
- File seed data để cả nhóm có dữ liệu test giống nhau

**Lý do cần migration thay vì schema.sql tĩnh:** 3 người cùng làm, mỗi lần đổi schema tạo file mới → tránh conflict, biết ai chạy version nào, có thể trace lịch sử.

## 3. Tiêu chí nghiệm thu (Acceptance Criteria)

- [ ] AC1: Thư mục `database/migrations/` tồn tại với các file SQL đánh số theo thứ tự `001_`, `002_`,...
- [ ] AC2: File `database/migrate.php` chạy được, chỉ áp dụng migration chưa có trong bảng `migrations`
- [ ] AC3: Tất cả 6 bảng được tạo với đúng kiểu dữ liệu, constraints, và indexes theo AGENTS.md mục 9
- [ ] AC4: Engine InnoDB bắt buộc cho tất cả bảng (hỗ trợ transaction + row lock)
- [ ] AC5: FK constraints đúng (cascade delete/restrict hợp lý theo business rule mục 10)
- [ ] AC6: Index cho mọi cột FK và cột tìm kiếm (`product_name` FULLTEXT hoặc INDEX)
- [ ] AC7: File `database/seed.sql` có dữ liệu mẫu: ít nhất 2 category, 2 brand, 5 product, 1 admin user, 1 customer user
- [ ] AC8: Admin user mẫu dùng `password_hash()` — tuyệt đối không lưu plaintext trong seed
- [ ] AC9: `schema.sql` cũ cập nhật để vẫn dùng được như tài liệu tham chiếu (tổng hợp từ migration)

## 4. Ghi chú kỹ thuật & Ràng buộc (Tech Notes)

### Cấu trúc migration files đề xuất:
```
database/migrations/
├── 001_create_migrations_table.sql    ← bảng theo dõi migration (phải chạy đầu tiên)
├── 002_create_users_table.sql
├── 003_create_categories_table.sql
├── 004_create_brands_table.sql
├── 005_create_products_table.sql
├── 006_create_orders_table.sql
├── 007_create_order_details_table.sql
└── migrate.php
```

### Schema cần thiết (theo SRS & AGENTS.md):

**USERS:** `user_id` (PK), `name`, `email` (UNIQUE), `password` (hash), `phone`, `address`, `role` ENUM('admin','customer'), `status` ENUM('active','locked'), `created_at`, `updated_at`

**CATEGORIES:** `category_id` (PK), `name` (NOT NULL), `description`, `created_at`

**BRANDS:** `brand_id` (PK), `name` (NOT NULL, UNIQUE), `description`, `logo_url`, `created_at`

**PRODUCTS:** `product_id` (PK), `sku` (UNIQUE), `name` (NOT NULL), `description`, `price` DECIMAL(12,2) NOT NULL CHECK > 0, `sale_price` DECIMAL(12,2), `quantity` INT DEFAULT 0, `image_url`, `category_id` (FK), `brand_id` (FK), `status` ENUM('active','inactive'), `created_at`, `updated_at`

**ORDERS:** `order_id` (PK), `user_id` (FK), `recipient_name`, `recipient_phone`, `shipping_address`, `total_amount` DECIMAL(12,2), `discount_amount` DECIMAL(12,2) DEFAULT 0, `status` ENUM('pending','confirmed','completed','cancelled'), `payment_method`, `notes`, `created_at`, `updated_at`

**ORDER_DETAILS:** `detail_id` (PK), `order_id` (FK), `product_id` (FK), `quantity` INT NOT NULL, `unit_price` DECIMAL(12,2) NOT NULL ← *lưu giá tại thời điểm đặt hàng*, `subtotal` DECIMAL(12,2)

### Business Rules liên quan (AGENTS.md mục 10):
- PRODUCTS: `sale_price <= price` — enforce bằng CHECK constraint nếu MySQL 8.0+
- PRODUCTS: `quantity >= 0` — enforce bằng CHECK constraint
- ORDERS: FK `user_id` → RESTRICT (không xóa user có đơn hàng)
- CATEGORIES/BRANDS: không xóa nếu còn sản phẩm → RESTRICT trên FK của PRODUCTS

### Model/Bảng liên quan: Tất cả 6 bảng
### File dự kiến cần tạo:
- `database/migrations/001_create_migrations_table.sql`
- `database/migrations/002_create_users_table.sql`
- `database/migrations/003_create_categories_table.sql`
- `database/migrations/004_create_brands_table.sql`
- `database/migrations/005_create_products_table.sql`
- `database/migrations/006_create_orders_table.sql`
- `database/migrations/007_create_order_details_table.sql`
- `database/migrate.php`
- `database/seed.sql` (cập nhật file có sẵn)

## 5. Security Checklist bắt buộc cho Task này
- [ ] Seed data KHÔNG chứa plaintext password — dùng `password_hash()` để tạo hash rồi paste vào seed
- [ ] File `.env` không commit (chỉ `.env.example`)
- [ ] Script `migrate.php` đọc credentials từ `.env`, không hardcode
- [ ] File `migrate.php` không thể truy cập trực tiếp qua browser (chỉ chạy qua CLI hoặc bảo vệ bằng IP check)

## 6. Ghi chú cho các thành viên khác

> **Hưng & Thảo:** Task này là prerequisite. Sau khi Minh merge branch này vào `develop`, pull code về và chạy `php database/migrate.php` + import `database/seed.sql` để có DB chuẩn cho các task tiếp theo. Mỗi khi có migration mới được merge, nhớ chạy lại `migrate.php`.
