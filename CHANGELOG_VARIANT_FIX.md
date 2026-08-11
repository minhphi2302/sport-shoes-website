# Changelog - Sửa Lỗi Biến Thể Trùng Lặp

## Vấn đề

Khi người dùng thêm nhiều biến thể cùng lúc trong form (VD: từ Ma trận tạo biến thể), nếu có 2 dòng cùng Model+Size+Màu nhưng **khác giá**, hệ thống báo lỗi:

```
"Lỗi: Biến thể Force 1 - Nam - 41 - Trắng đã sử dụng giá khác không giá 2,400,000 VND..."
```

Lỗi này gây khó chịu vì:
1. Người dùng có thể muốn cập nhật giá của biến thể đã tồn tại
2. Trong form có thể có nhiều dòng trùng do auto-generate từ matrix
3. Logic quá nghiêm ngặt, không linh hoạt

## Giải pháp

### Thay đổi 1: Cho phép cập nhật giá khi thêm biến thể trùng

**Trước đây:**
```php
if (abs($existingVar['price'] - $newVar['price']) > 0.01) {
    throw new ValidationException('Không thể có 2 mức giá khác nhau');
}
```

**Bây giờ:**
```php
// Cộng dồn số lượng + Cập nhật giá theo giá mới
$finalVariants[$key] = [
    'price' => $newVar['price'], // Lấy giá mới từ form
    'quantity' => $existingVar['quantity'] + $newVar['quantity']
];
```

### Thay đổi 2: Tự động gộp các dòng trùng trong form

**Trước đây:**
```php
if (isset($seenInForm[$key])) {
    if (abs($existing['price'] - $nv['price']) > 0.01) {
        throw new ValidationException('Giá khác nhau trong form');
    }
    $seenInForm[$key]['quantity'] += $nv['quantity'];
}
```

**Bây giờ:**
```php
if (isset($seenInForm[$key])) {
    // Cộng dồn số lượng, lấy giá và SKU của dòng cuối cùng
    $seenInForm[$key]['quantity'] += $nv['quantity'];
    $seenInForm[$key]['price'] = $nv['price'];
    $seenInForm[$key]['sku'] = $nv['sku'];
}
```

### Thay đổi 3: Frontend không còn báo lỗi giá khác

**Trước đây:**
```javascript
if (Math.abs(matchedPrice - newPrice) > 0.01) {
    showError('Không thể có 2 mức giá khác nhau!');
    return;
}
```

**Bây giờ:**
```javascript
// Luôn cộng dồn + cập nhật giá, không kiểm tra
qtyInput.value = currentQty + addQty;
priceInput.value = newPrice; // Cập nhật giá mới
```

## Hành vi mới

### Case 1: Thêm biến thể đã tồn tại trong DB

```
DB có:
- Model: Force 1, Size: Nam - 41, Màu: Trắng
- Giá: 2,400,000 VNĐ
- Số lượng: 10

User submit:
- Model: Force 1, Size: Nam - 41, Màu: Trắng
- Giá: 2,200,000 VNĐ (giảm giá)
- Số lượng: 5

Kết quả:
✅ Số lượng = 15 (10 + 5)
✅ Giá = 2,200,000 VNĐ (cập nhật)
✅ Thông báo: "Đã cộng dồn và cập nhật giá"
```

### Case 2: Nhiều dòng trùng trong form

```
Form có:
Dòng 1: Force 1 - Nam 41 - Trắng - 2,400,000 - Qty 10
Dòng 2: Force 1 - Nam 41 - Trắng - 2,200,000 - Qty 5
Dòng 3: Force 1 - Nam 42 - Đen - 2,300,000 - Qty 8

Sau xử lý:
✅ Biến thể 1: Force 1 - Nam 41 - Trắng - 2,200,000 (giá dòng 2) - Qty 15
✅ Biến thể 2: Force 1 - Nam 42 - Đen - 2,300,000 - Qty 8
```

## Lợi ích

1. ✅ **Linh hoạt hơn**: Cho phép cập nhật giá qua form thêm biến thể
2. ✅ **Ít lỗi hơn**: Không còn throw exception khi giá khác
3. ✅ **Tự động gộp**: Xử lý thông minh các dòng trùng trong form
4. ✅ **UX tốt hơn**: Người dùng không bị chặn khi muốn điều chỉnh giá

## Lưu ý

- Nếu có nhiều dòng trùng, hệ thống lấy **giá và SKU của dòng cuối cùng**
- Số lượng luôn được **cộng dồn** cho tất cả dòng trùng
- Biến thể vẫn duy nhất theo (Model + Size + Màu), nhưng giá có thể thay đổi

## Files thay đổi

- `app/Models/Product.php`: Bỏ throw exception khi giá khác
- `views/admin/components/variant_matrix_generator.php`: Cập nhật giá thay vì báo lỗi
- `docs/VARIANT_RULES.md`: Cập nhật tài liệu quy tắc mới

## Migration

Không cần migration DB. Chỉ cần deploy code mới.

## Test

```bash
# Test trên UI
1. Tạo sản phẩm mới
2. Thêm biến thể: Force 1 - Nam 41 - Trắng - 2,400,000 - Qty 10
3. Lưu
4. Thêm lại biến thể: Force 1 - Nam 41 - Trắng - 2,200,000 - Qty 5
5. Kỳ vọng: Số lượng = 15, Giá = 2,200,000 ✅
```

## Rollback

Nếu cần quay lại hành vi cũ (strict mode - báo lỗi khi giá khác), xem commit trước đó.

---

**Version:** 2.0 - Flexible Variant Management  
**Date:** 2026-08-11  
**Author:** Kiro AI Assistant
