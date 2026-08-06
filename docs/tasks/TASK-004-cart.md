# TASK-004: Giỏ hàng (Cart) — Thêm / Sửa / Xóa / Xem tổng tiền

## 1. Thông tin chung
- **Trạng thái:** To Do
- **Người được giao:** Nguyễn Mạnh Hưng
- **Role AI khuyên dùng:** 🛒 Feature Developer
- **Ngày tạo:** 2026-07-22
- **Branch:** `feat/gio-hang-datHang`
- **Phụ thuộc:** TASK-001 (bảng products), TASK-003 (UI sản phẩm)
- **Ưu tiên:** 🟡 HIGH — prerequisite của TASK-005 (Đặt hàng)

## 2. Mô tả yêu cầu (Yêu cầu nghiệp vụ)

Chức năng giỏ hàng lưu trong **PHP Session** (không cần bảng DB riêng cho giỏ hàng):

1. **Thêm vào giỏ:** Từ trang chi tiết sản phẩm hoặc danh sách. Nếu sản phẩm đã có trong giỏ → tăng số lượng.
2. **Xem giỏ hàng (`/cart`):** Danh sách sản phẩm, số lượng, đơn giá, thành tiền, tổng tiền.
3. **Cập nhật số lượng:** Tăng/giảm số lượng từng item, không cho vượt tồn kho hiện tại.
4. **Xóa item:** Xóa 1 sản phẩm khỏi giỏ.
5. **Xóa toàn bộ giỏ:** Clear cart.
6. **Tính tổng tiền:** Dùng `sale_price` nếu có, ngược lại dùng `price`.

## 3. Tiêu chí nghiệm thu (Acceptance Criteria)

- [ ] AC1: Click "Thêm vào giỏ" → item xuất hiện trong giỏ, số lượng giỏ trên header tăng lên (không full page reload nếu dùng AJAX)
- [ ] AC2: Thêm cùng 1 sản phẩm 2 lần → quantity trong giỏ cộng dồn, không tạo 2 record trùng
- [ ] AC3: Trang `/cart` hiển thị đúng danh sách, tổng tiền tính đúng (kể cả khi sản phẩm có sale_price)
- [ ] AC4: Cập nhật quantity > tồn kho hiện tại → báo lỗi "Chỉ còn X sản phẩm trong kho", không cho lưu
- [ ] AC5: Cập nhật quantity về 0 hoặc xóa item → item bị loại khỏi giỏ
- [ ] AC6: Giỏ hàng của Guest vẫn hoạt động (lưu session), khi Guest đăng nhập giỏ hàng giữ nguyên
- [ ] AC7: Nút "Tiến hành đặt hàng" → redirect đến `/checkout` (TASK-005)
- [ ] AC8: Giỏ hàng trống → hiển thị thông báo "Giỏ hàng của bạn đang trống" và nút "Tiếp tục mua sắm"

## 4. Ghi chú kỹ thuật & Ràng buộc (Tech Notes)

### Cấu trúc session cart:
```php
$_SESSION['cart'] = [
    'product_id_1' => [
        'product_id' => 1,
        'name'       => 'Nike Air Max',
        'price'      => 2500000,   // giá tại thời điểm thêm vào giỏ
        'sale_price' => 2000000,   // null nếu không có sale
        'quantity'   => 2,
        'image_url'  => 'product_xxx.jpg',
    ],
    // ...
];
```

### Model/Bảng liên quan: `products` (chỉ đọc để validate tồn kho)
**Không có bảng cart riêng** — cart lưu trong session.

### File dự kiến cần tạo/sửa:
- `app/Controllers/CartController.php` — actions: `index()`, `add()`, `update()`, `remove()`, `clear()`
- `views/client/cart.php`
- Route trong `public/index.php`: `GET /cart`, `POST /cart/add`, `POST /cart/update`, `POST /cart/remove`, `POST /cart/clear`

### Validate tồn kho khi cập nhật:
```php
// Khi user thay đổi quantity trong giỏ, query DB để lấy tồn kho thực tế
$product = $this->productModel->findById($productId);
if ($newQty > $product['quantity']) {
    // Báo lỗi — không ghi vào session
}
```

> **Lưu ý:** Không cần SELECT FOR UPDATE ở đây vì chưa trừ kho. Việc trừ kho thật sự xảy ra tại bước đặt hàng (TASK-005) với transaction + UPDATE có điều kiện.

### Business Rules:
- Giá trong giỏ lưu tại thời điểm thêm vào (snapshot), nhưng tại trang `/cart` nên hiển thị giá hiện tại từ DB và cảnh báo nếu giá thay đổi
- Quantity trong giỏ không được vượt tồn kho hiện tại

## 5. Security Checklist bắt buộc cho Task này
- [ ] Validate `product_id` và `quantity` từ POST request (là số nguyên dương)
- [ ] Không tin quantity từ session khi checkout — luôn validate lại với DB
- [ ] `htmlspecialchars()` cho mọi output tên sản phẩm trong cart view
- [ ] CSRF protection cho các POST action (ít nhất check Referer hoặc thêm CSRF token nếu có thời gian)
