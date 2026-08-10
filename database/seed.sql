-- Seed users (password is 'password')
<<<<<<< HEAD
INSERT INTO users (name, email, password, phone, address, role, status) VALUES
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0123456789', 'Hanoi, VN', 'admin', 'active'),
('Customer 1', 'customer1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0987654321', 'HCM, VN', 'customer', 'active');
=======
INSERT INTO users (name, email, password, phone, gender, address, role, status) VALUES
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0123456789', 'male', 'Hanoi, VN', 'admin', 'active'),
('Customer 1', 'customer1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0987654321', 'female', 'HCM, VN', 'customer', 'active');
>>>>>>> origin/feat/admin

-- Seed categories
INSERT INTO categories (name, description) VALUES
('Running', 'Giày chạy bộ chuyên dụng'),
('Lifestyle', 'Giày thời trang đi chơi hàng ngày');

-- Seed brands
INSERT INTO brands (name, description, logo_url) VALUES
('Nike', 'Thương hiệu đồ thể thao hàng đầu', 'nike_logo.png'),
('Adidas', 'Impossible is nothing', 'adidas_logo.png');

-- Seed products
<<<<<<< HEAD
INSERT INTO products (sku, name, description, price, sale_price, quantity, category_id, brand_id, status) VALUES
('NK-RN-001', 'Nike Air Zoom Pegasus 39', 'Giày chạy êm ái', 3000000.00, 2500000.00, 50, 1, 1, 'active'),
('NK-LF-002', 'Nike Air Force 1', 'Mẫu giày huyền thoại', 2500000.00, NULL, 100, 2, 1, 'active'),
('AD-RN-003', 'Adidas Ultraboost 22', 'Giày chạy siêu nhẹ', 3500000.00, 3200000.00, 30, 1, 2, 'active'),
('AD-LF-004', 'Adidas Stan Smith', 'Cổ điển và lịch lãm', 2000000.00, NULL, 80, 2, 2, 'active'),
('NK-RN-005', 'Nike React Infinity Run', 'Giày chạy chống chấn thương', 3200000.00, 2800000.00, 40, 1, 1, 'active');
=======
INSERT INTO products (sku, name, description, price, sale_price, quantity, category_id, brand_id, gender, status) VALUES
('NK-RN-001', 'Nike Air Zoom Pegasus 39', 'Giày chạy êm ái', 3000000.00, 2500000.00, 50, 1, 1, 'male', 'active'),
('NK-LF-002', 'Nike Air Force 1', 'Mẫu giày huyền thoại', 2500000.00, NULL, 100, 2, 1, 'unisex', 'active'),
('AD-RN-003', 'Adidas Ultraboost 22', 'Giày chạy siêu nhẹ', 3500000.00, 3200000.00, 30, 1, 2, 'male', 'active'),
('AD-LF-004', 'Adidas Stan Smith', 'Cổ điển và lịch lãm', 2000000.00, NULL, 80, 2, 2, 'unisex', 'active'),
('NK-RN-005', 'Nike React Infinity Run', 'Giày chạy chống chấn thương', 3200000.00, 2800000.00, 40, 1, 1, 'female', 'active');
>>>>>>> origin/feat/admin
