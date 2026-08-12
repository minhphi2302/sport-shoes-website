-- Migration 024: Thêm cột shipping_fee vào orders (safe - kiểm tra trước khi add)
-- Chạy file này trong phpMyAdmin nếu chưa có cột shipping_fee

SET @exist := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'orders'
      AND COLUMN_NAME = 'shipping_fee'
);

SET @sql := IF(@exist = 0,
    'ALTER TABLE orders ADD COLUMN shipping_fee DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER total_amount',
    'SELECT ''shipping_fee column already exists, skipping.'''
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
