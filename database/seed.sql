-- Seed users (password is 'password')
INSERT INTO users (name, email, password, phone, address, role, status) VALUES
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0123456789', 'Hanoi, VN', 'admin', 'active'),
('Customer 1', 'customer1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0987654321', 'HCM, VN', 'customer', 'active');

-- Seed categories
INSERT INTO categories (name, description) VALUES 
('Giày chạy bộ', 'Giày chạy bộ chuyên dụng'),
('Giày thời trang', 'Giày thời trang đi chơi hàng ngày'),
('Giày bóng rổ', 'Giày bóng rổ chuyên nghiệp'),
('Giày tập luyện', 'Giày tập luyện đa năng');

-- Seed brands
INSERT INTO brands (name, description, logo_url) VALUES
('Nike', 'Thương hiệu đồ thể thao hàng đầu', 'public/image/brand/nike.jpg'),
('Adidas', 'Impossible is nothing', 'public/image/brand/adidas.png'),
('Puma', 'Puma dep', 'public/image/brand/puma.png');

-- Seed products
INSERT INTO products (sku, name, description, price, sale_price, quantity, category_id, brand_id, status) VALUES
('NK-RN-001', 'Giày Nike Air Zoom Pegasus Nữ', 'Giày chạy êm ái', 3000000.00, 2500000.00, 50, 1, 1, 'active'),
('AD-RN-002', 'Giày Adidas Ultraboost 22 Nam', 'Giày chạy siêu nhẹ', 3500000.00, 3200000.00, 30, 1, 2, 'active'),
('NK-RN-003', 'Giày Nike React Infinity Run Nam', 'Giày chạy chống chấn thương', 3200000.00, 2800000.00, 40, 1, 1, 'active'),
('NK-RN-004', 'Giày Nike React Infinity Run Nữ', 'Giày chạy chống chấn thương 1', 200000.00, 80000.00, 0, 1, 1, 'active'),
('NK-RN-005', 'Giày Puma 123 Nam', 'Giày chạy chống chấn thương 1', 20000.00, 0, 5, 1, 3, 'active'), 
('NK-RN-006', 'Giày Nike React Infinity Run Nam', 'Giày chạy chống chấn thương 1234', 20000.00, 8000.00, 0, 1, 1, 'active'); 

-- Seed product_variants
INSERT INTO product_variants (product_id, sku, model, size, color, quantity) VALUES
(1, 'NK-RN-001-40', 'Pegasus', '40', 'Đen', 10),
(1, 'NK-RN-001-41', 'Pegasus', '41', 'Đen', 15),
(2, 'AD-RN-003-42', 'Ultraboost', '42', 'Trắng', 5),
(3, 'NK-RN-005-39', 'Infinity', '39', 'Đỏ', 20);
