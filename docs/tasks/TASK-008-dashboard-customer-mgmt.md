# TASK-008: Dashboard Thống kê & Quản lý Khách hàng (Admin)

## 1. Thông tin chung
- **Trạng thái:** To Do
- **Người được giao:** Nguyễn Chí Thảo
- **Role AI khuyên dùng:** 🛒 Feature Developer
- **Ngày tạo:** 2026-07-22
- **Branch:** `feat/dashboard-thongke`
- **Phụ thuộc:** TASK-001, TASK-002, TASK-005 (cần có đơn hàng thật để thống kê)
- **Ưu tiên:** 🟢 LOW — tuần 4

## 2. Mô tả yêu cầu (Yêu cầu nghiệp vụ)

### 2A — Dashboard Admin (`/admin/dashboard`):
- Thẻ số liệu tổng quan: Tổng sản phẩm, Tổng khách hàng, Đơn hàng hôm nay, Doanh thu tháng này
- Danh sách 5 đơn hàng mới nhất (status = pending)
- Danh sách 5 sản phẩm bán chạy nhất (optional nếu có thời gian)

### 2B — Quản lý khách hàng (`/admin/customers`):
- Danh sách tất cả tài khoản role `customer`, phân trang
- Tìm kiếm theo tên hoặc email
- Xem chi tiết thông tin khách hàng
- Khóa/Mở khóa tài khoản (toggle `status` giữa `active` và `locked`)

## 3. Tiêu chí nghiệm thu (Acceptance Criteria)

### Dashboard:
- [ ] AC1: Các thẻ số liệu hiển thị đúng con số thực từ DB
- [ ] AC2: Doanh thu tháng này chỉ tính đơn `status = completed`
- [ ] AC3: Danh sách 5 đơn hàng mới nhất hiển thị đúng (mã đơn, tên khách, tổng tiền, thời gian)

### Quản lý khách hàng:
- [ ] AC4: Danh sách chỉ hiển thị tài khoản có `role = 'customer'`, không có admin
- [ ] AC5: Khóa tài khoản Customer → tài khoản đó đăng nhập bị từ chối ngay lần tiếp theo
- [ ] AC6: Mở khóa tài khoản → tài khoản đăng nhập bình thường
- [ ] AC7: Admin không thể tự khóa chính mình
- [ ] AC8: Tìm kiếm theo tên/email trả về đúng kết quả

## 4. Ghi chú kỹ thuật & Ràng buộc (Tech Notes)

### Model/Bảng liên quan: `users`, `orders`, `products`

### File dự kiến cần tạo/sửa:
- `app/Models/User.php` — thêm: `getAllCustomers(array $filters, int $page)`, `toggleStatus(int $id)`, `findById(int $id)`
- `app/Controllers/Admin/DashboardController.php` — action: `index()`
- `app/Controllers/Admin/CustomerAdminController.php` — actions: `index()`, `show(int $id)`, `toggleStatus(int $id)`
- `views/admin/dashboard.php`
- `views/admin/customer_list.php`
- `views/admin/customer_detail.php`
- Routes: `GET /admin/dashboard`, `GET /admin/customers`, `GET /admin/customers/{id}`, `POST /admin/customers/{id}/toggle-status`

### Query dashboard hiệu quả (tránh N+1):
```sql
-- Doanh thu tháng này (chỉ đơn completed)
SELECT SUM(total_amount) AS revenue
FROM orders
WHERE status = 'completed'
  AND MONTH(created_at) = MONTH(NOW())
  AND YEAR(created_at) = YEAR(NOW());

-- 5 đơn hàng mới nhất pending (JOIN 1 lần)
SELECT o.order_id, o.total_amount, o.created_at, u.name AS customer_name
FROM orders o
INNER JOIN users u ON o.user_id = u.user_id
WHERE o.status = 'pending'
ORDER BY o.created_at DESC
LIMIT 5;
```

### Business Rules:
- Admin không thể tự khóa mình: `if ($id === Auth::id()) throw new ...`
- Danh sách khách hàng: chỉ `role = 'customer'`

## 5. Security Checklist bắt buộc cho Task này
- [ ] Tất cả admin routes qua `AdminController` middleware
- [ ] Admin không tự khóa mình (validate trong Controller)
- [ ] Prepared Statement cho mọi SQL
- [ ] `htmlspecialchars()` cho tên khách hàng, email, địa chỉ output ra view
