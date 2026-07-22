# TASK-005: Đặt hàng (Checkout) — Tạo đơn hàng & Trừ tồn kho

## 1. Thông tin chung
- **Trạng thái:** To Do
- **Người được giao:** Nguyễn Mạnh Hưng
- **Role AI khuyên dùng:** 🛒 Feature Developer
- **Ngày tạo:** 2026-07-22
- **Branch:** `feat/gio-hang-datHang`
- **Phụ thuộc:** TASK-001 (bảng orders/order_details), TASK-002 (auth), TASK-004 (giỏ hàng)
- **Ưu tiên:** 🟡 MEDIUM — tuần 3-4

## 2. Mô tả yêu cầu (Yêu cầu nghiệp vụ)

Luồng đặt hàng từ giỏ hàng sang đơn hàng hoàn chỉnh:

1. **Trang Checkout (`/checkout`):** Chỉ Customer đã đăng nhập mới truy cập được. Hiển thị form nhập thông tin nhận hàng (tên, SĐT, địa chỉ), tóm tắt đơn hàng, chọn phương thức thanh toán (COD là bắt buộc, tùy chọn thêm VNPAY nếu có thời gian).
2. **Xác nhận đặt hàng (POST `/checkout`):** Tạo record `Order` + các `OrderDetail` + trừ tồn kho trong **1 transaction duy nhất**. Nếu lỗi → rollback toàn bộ.
3. **Trang xác nhận thành công (`/orders/{id}/success`):** Hiển thị mã đơn hàng và tóm tắt, xóa giỏ hàng.

## 3. Tiêu chí nghiệm thu (Acceptance Criteria)

- [ ] AC1: Guest truy cập `/checkout` → redirect đến `/login` với thông báo "Vui lòng đăng nhập để đặt hàng"
- [ ] AC2: Giỏ hàng trống mà truy cập `/checkout` → redirect về `/cart` với thông báo lỗi
- [ ] AC3: Form checkout validate server-side: tên, SĐT, địa chỉ không trống
- [ ] AC4: Submit checkout thành công → Order tạo với status `pending`, OrderDetail đúng, tồn kho giảm đúng số lượng
- [ ] AC5: Đơn giá trong `order_details.unit_price` là giá tại thời điểm đặt hàng (sale_price nếu có, ngược lại price) — không thay đổi khi admin sửa giá sau này
- [ ] AC6: `order.total_amount` = tổng subtotal của tất cả order_details - discount_amount
- [ ] AC7: Nếu 1 sản phẩm không đủ tồn kho → rollback toàn bộ, thông báo cụ thể sản phẩm nào hết hàng
- [ ] AC8: 2 user cùng đặt hàng cùng lúc, tồn kho chỉ còn 1 → 1 user thành công, 1 user nhận lỗi hết hàng (race condition safe)
- [ ] AC9: Sau đặt hàng thành công → giỏ hàng session bị clear
- [ ] AC10: Trang success hiển thị mã đơn hàng và nút "Xem đơn hàng của tôi"

## 4. Ghi chú kỹ thuật & Ràng buộc (Tech Notes)

### Model/Bảng liên quan: `orders`, `order_details`, `products`

### File dự kiến cần tạo/sửa:
- `app/Models/Order.php` — method: `createOrder(int $userId, array $shippingInfo, array $cartItems): int`
- `app/Models/OrderDetail.php` — method: `insertDetail(int $orderId, array $item): void`
- `app/Controllers/OrderController.php` — actions: `showCheckout()`, `placeOrder()`, `showSuccess(int $orderId)`
- `views/client/checkout.php`
- `views/client/order_success.php`
- Route: `GET /checkout`, `POST /checkout`, `GET /orders/{id}/success`

### Transaction + Race condition (AGENTS.md mục 3 — BẮTBUỘC):
```php
public function createOrder(int $userId, array $shippingInfo, array $cartItems): int
{
    $this->pdo->beginTransaction();
    try {
        // 1. Tạo Order
        $orderId = $this->insertOrder($userId, $shippingInfo);

        foreach ($cartItems as $item) {
            // 2. Trừ tồn kho — UPDATE có điều kiện, atomic ở tầng DB
            $stmt = $this->pdo->prepare(
                'UPDATE products SET quantity = quantity - :qty
                 WHERE product_id = :id AND quantity >= :qty'
            );
            $stmt->execute(['qty' => $item['quantity'], 'id' => $item['product_id']]);

            if ($stmt->rowCount() === 0) {
                // Không đủ hàng hoặc sản phẩm không tồn tại
                throw new InsufficientStockException(
                    "Sản phẩm #{$item['product_id']} không đủ tồn kho"
                );
            }

            // 3. Tạo OrderDetail
            $this->insertOrderDetail($orderId, $item);
        }

        $this->pdo->commit();
        return $orderId;
    } catch (\Throwable $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}
```

> ❌ **KHÔNG** dùng kiểu: `getStock() → check → updateStock()` — đây là race condition

### Custom Exception cần tạo:
- `app/Exceptions/InsufficientStockException.php`
- `app/Exceptions/InvalidOrderDataException.php`

### Business Rules liên quan (AGENTS.md mục 10 — ORDERS & ORDER_DETAILS):
- Thuộc 1 khách hàng, có ít nhất 1 sản phẩm
- `subtotal = quantity × unit_price`
- `total_amount = SUM(subtotal) - discount_amount`
- Status mới tạo = `pending`
- Tồn kho không âm (đảm bảo bằng UPDATE có điều kiện)

## 5. Security Checklist bắt buộc cho Task này
- [ ] Chỉ Customer đã đăng nhập mới POST được `/checkout` (kiểm tra session)
- [ ] Validate tất cả input form server-side (tên, SĐT, địa chỉ)
- [ ] Không tin dữ liệu giá từ session/form — lấy giá từ DB khi tạo OrderDetail
- [ ] Transaction có rollback đầy đủ khi lỗi bất kỳ bước nào
- [ ] Prepared Statement cho tất cả SQL
- [ ] `htmlspecialchars()` cho mọi output ra view

## 6. PHPUnit Test cần viết (AGENTS.md mục 11)

```
tests/Unit/OrderModelTest.php:
- test_createOrder_success_decreases_stock_correctly
- test_createOrder_insufficient_stock_throws_exception_and_rollbacks
- test_createOrder_partial_stock_rollbacks_entire_order
- test_createOrder_concurrent_same_product_only_one_succeeds  ← test race condition
- test_orderDetail_unit_price_is_snapshot_not_current_price
```
