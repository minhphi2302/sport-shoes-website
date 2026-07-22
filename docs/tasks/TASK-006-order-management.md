# TASK-006: Lịch sử đơn hàng & Quản lý đơn hàng (Admin)

## 1. Thông tin chung
- **Trạng thái:** To Do
- **Người được giao:** Nguyễn Chí Thảo
- **Role AI khuyên dùng:** 🛒 Feature Developer
- **Ngày tạo:** 2026-07-22
- **Branch:** `feat/quanly-donhang`
- **Phụ thuộc:** TASK-001 (bảng orders), TASK-002 (auth/AdminController), TASK-005 (đơn hàng phải tồn tại)
- **Ưu tiên:** 🟢 MEDIUM — tuần 3-4

## 2. Mô tả yêu cầu (Yêu cầu nghiệp vụ)

### 2A — Customer: Lịch sử đơn hàng
- Trang `/orders`: Danh sách đơn hàng của chính mình (mới nhất trước), phân trang.
- Trang `/orders/{id}`: Chi tiết đơn hàng: thông tin nhận hàng, danh sách sản phẩm, tổng tiền, trạng thái.
- Customer **không được xem** đơn hàng của người khác.

### 2B — Admin: Quản lý đơn hàng
- Trang `/admin/orders`: Danh sách tất cả đơn hàng, lọc theo trạng thái, tìm kiếm theo mã đơn hoặc tên khách, phân trang.
- Trang `/admin/orders/{id}`: Chi tiết, thông tin khách hàng, danh sách sản phẩm.
- Cập nhật trạng thái: `Pending → Confirmed`, `Confirmed → Completed`, `Pending → Cancelled` (các transition hợp lệ).
- Transition không hợp lệ (VD: `Completed → Pending`) → báo lỗi.

## 3. Tiêu chí nghiệm thu (Acceptance Criteria)

### Customer:
- [ ] AC1: Customer thấy đúng danh sách đơn hàng của mình, không thấy đơn của người khác
- [ ] AC2: Truy cập `/orders/{id}` của người khác → 403 Forbidden
- [ ] AC3: Chi tiết đơn hàng hiển thị đúng sản phẩm, số lượng, giá tại thời điểm đặt, tổng tiền
- [ ] AC4: Trạng thái đơn hàng hiển thị đúng màu theo AGENTS.md mục 12 (pending → badge secondary, v.v.)

### Admin:
- [ ] AC5: Admin thấy danh sách tất cả đơn hàng, lọc theo trạng thái hoạt động
- [ ] AC6: Tìm kiếm theo mã đơn hoặc tên khách trả về đúng kết quả
- [ ] AC7: Cập nhật trạng thái hợp lệ (`Pending → Confirmed`) → lưu thành công, hiển thị thông báo
- [ ] AC8: Cập nhật trạng thái không hợp lệ (`Completed → Pending`) → thông báo lỗi, không thay đổi DB
- [ ] AC9: Trang admin orders chỉ Admin mới truy cập được (middleware check)

## 4. Ghi chú kỹ thuật & Ràng buộc (Tech Notes)

### Model/Bảng liên quan: `orders`, `order_details`, `users`, `products`

### File dự kiến cần tạo/sửa:
- `app/Models/Order.php` — methods: `findByUserId(int $userId, int $page)`, `findById(int $id)`, `findAll(array $filters, int $page)`, `updateStatus(int $orderId, string $newStatus)`, `countAll(array $filters)`
- `app/Controllers/OrderController.php` — actions: `myOrders()`, `show(int $id)`
- `app/Controllers/Admin/OrderAdminController.php` — actions: `index()`, `show(int $id)`, `updateStatus(int $id)`
- `views/client/order_list.php`
- `views/client/order_detail.php`
- `views/admin/order_list.php`
- `views/admin/order_detail.php`
- Routes: `GET /orders`, `GET /orders/{id}`, `GET /admin/orders`, `GET /admin/orders/{id}`, `POST /admin/orders/{id}/status`

### Validate transition trong Model (AGENTS.md mục 10 — ORDERS):
```php
// app/Models/Order.php
private const VALID_TRANSITIONS = [
    'pending'   => ['confirmed', 'cancelled'],
    'confirmed' => ['completed'],
    'completed' => [],   // Terminal state
    'cancelled' => [],   // Terminal state
];

public function updateStatus(int $orderId, string $newStatus): void
{
    $order = $this->findById($orderId);
    $currentStatus = $order['status'];

    if (!in_array($newStatus, self::VALID_TRANSITIONS[$currentStatus] ?? [], true)) {
        throw new InvalidOrderTransitionException(
            "Không thể chuyển từ '{$currentStatus}' sang '{$newStatus}'"
        );
    }

    // UPDATE với prepared statement
    $stmt = $this->pdo->prepare(
        'UPDATE orders SET status = :status, updated_at = NOW() WHERE order_id = :id'
    );
    $stmt->execute(['status' => $newStatus, 'id' => $orderId]);
}
```

### Custom Exception cần tạo:
- `app/Exceptions/InvalidOrderTransitionException.php` (extends `AppException`)

### Trạng thái màu sắc (AGENTS.md mục 12):
| Trạng thái | Bootstrap badge | Label |
|---|---|---|
| pending | `bg-secondary` | Chờ xử lý |
| confirmed | `bg-info` | Đã xác nhận |
| completed | `bg-success` | Hoàn thành |
| cancelled | `bg-danger` | Đã hủy |

### Không N+1 query:
```sql
-- Lấy danh sách đơn hàng kèm tên khách — 1 query với JOIN
SELECT o.*, u.name AS customer_name, u.email AS customer_email
FROM orders o
INNER JOIN users u ON o.user_id = u.user_id
WHERE (:status IS NULL OR o.status = :status)
ORDER BY o.created_at DESC
LIMIT :perPage OFFSET :offset
```

## 5. Security Checklist bắt buộc cho Task này
- [ ] Customer chỉ xem đơn của mình: `WHERE order_id = ? AND user_id = ?` (kiểm tra ownership)
- [ ] Admin routes qua `AdminController` base class (TASK-002 cung cấp)
- [ ] Validate `$newStatus` là 1 trong 4 giá trị hợp lệ trước khi pass vào Model
- [ ] Prepared Statement cho tất cả SQL
- [ ] `htmlspecialchars()` cho mọi output (tên khách, địa chỉ, ghi chú)

## 6. PHPUnit Test cần viết (AGENTS.md mục 11)

```
tests/Unit/OrderModelTest.php:
- test_updateStatus_valid_transition_pending_to_confirmed_succeeds
- test_updateStatus_invalid_transition_completed_to_pending_throws_exception
- test_updateStatus_cancelled_order_cannot_be_changed
- test_findByUserId_only_returns_own_orders
```
