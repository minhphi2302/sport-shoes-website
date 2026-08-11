-- Fix lỗi encoding UTF-8 trong bảng colors và sizes
-- Dữ liệu bị corrupt do thiếu SET NAMES utf8mb4 khi chạy migration 018
-- product_variants không có FK tới colors/sizes nên xóa và insert lại an toàn

SET NAMES utf8mb4;

-- ===== Fix bảng colors =====
DELETE FROM colors;

INSERT INTO colors (name) VALUES 
('Trắng'),
('Đen'),
('Đỏ'),
('Xanh dương'),
('Xanh lá'),
('Vàng'),
('Xám');

-- ===== Fix bảng sizes =====
DELETE FROM sizes;

INSERT INTO sizes (name, gender) VALUES 
('39', 'Nam'), ('40', 'Nam'), ('41', 'Nam'), ('42', 'Nam'), ('43', 'Nam'), ('44', 'Nam'), ('45', 'Nam'),
('35', 'Nữ'), ('36', 'Nữ'), ('37', 'Nữ'), ('38', 'Nữ'), ('39', 'Nữ'),
('28', 'Trẻ em'), ('29', 'Trẻ em'), ('30', 'Trẻ em'), ('31', 'Trẻ em'), ('32', 'Trẻ em'), ('33', 'Trẻ em'), ('34', 'Trẻ em');
