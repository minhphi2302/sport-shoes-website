-- Migration 023: Cập nhật tất cả phương thức thanh toán trong đơn hàng sang COD (không dùng Banking)

UPDATE `orders`
SET `payment_method` = 'COD'
WHERE `payment_method` IS NULL OR `payment_method` != 'COD';
