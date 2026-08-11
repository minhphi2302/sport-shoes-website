-- Seed users (password is 'password')
INSERT INTO users (name, email, password, phone, gender, address, role, status) VALUES
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0123456789', 'male', 'Hanoi, VN', 'admin', 'active'),
('Customer 1', 'customer1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0987654321', 'female', 'HCM, VN', 'customer', 'active');

-- Seed categories
INSERT INTO categories (name, description) VALUES
('Running', 'Giày chạy bộ chuyên dụng'),
('Lifestyle', 'Giày thời trang đi chơi hàng ngày');

-- Seed brands
INSERT INTO brands (name, description, logo_url) VALUES
('Nike', 'Thương hiệu đồ thể thao hàng đầu', 'nike_logo.png'),
('Adidas', 'Impossible is nothing', 'adidas_logo.png');

-- Seed products
INSERT INTO products (sku, name, description, price, sale_price, quantity, category_id, brand_id, gender, status) VALUES
('NK-RN-001', 'Nike Air Zoom Pegasus 39', 'Giày chạy êm ái', 3000000.00, 2500000.00, 50, 1, 1, 'male', 'active'),
('NK-LF-002', 'Nike Air Force 1', 'Mẫu giày huyền thoại', 2500000.00, NULL, 100, 2, 1, 'unisex', 'active'),
('AD-RN-003', 'Adidas Ultraboost 22', 'Giày chạy siêu nhẹ', 3500000.00, 3200000.00, 30, 1, 2, 'male', 'active'),
('AD-LF-004', 'Adidas Stan Smith', 'Cổ điển và lịch lãm', 2000000.00, NULL, 80, 2, 2, 'unisex', 'active'),
('NK-RN-005', 'Nike React Infinity Run', 'Giày chạy chống chấn thương', 3200000.00, 2800000.00, 40, 1, 1, 'female', 'active');

-- Seed 10 product_variants
INSERT INTO product_variants (product_id, sku, model, size, color, price, quantity) VALUES
(1, 'NK-RN-001-PEG-DEN-40', 'Pegasus', 'Nam - 40', 'Đen', 2500000.00, 15),
(1, 'NK-RN-001-PEG-TRANG-41', 'Pegasus', 'Nam - 41', 'Trắng', 2500000.00, 20),
(1, 'NK-RN-001-PEG-DO-42', 'Pegasus', 'Nam - 42', 'Đỏ', 2500000.00, 15),
(2, 'NK-LF-002-AF1-TRANG-38', 'Air Force 1', 'Nữ - 38', 'Trắng', 2500000.00, 50),
(2, 'NK-LF-002-AF1-DEN-39', 'Air Force 1', 'Nữ - 39', 'Đen', 2500000.00, 50),
(3, 'AD-RN-003-UB-DEN-41', 'Ultraboost', 'Nam - 41', 'Đen', 3200000.00, 10),
(3, 'AD-RN-003-UB-XANH-42', 'Ultraboost', 'Nam - 42', 'Xanh dương', 3200000.00, 20),
(4, 'AD-LF-004-SS-TRANG-40', 'Stan Smith', 'Nam - 40', 'Trắng', 2000000.00, 40),
(4, 'AD-LF-004-SS-XANH-41', 'Stan Smith', 'Nam - 41', 'Xanh lá', 2000000.00, 40),
(5, 'NK-RN-005-INF-HONG-37', 'Infinity', 'Nữ - 37', 'Hồng', 2800000.00, 40);
