CREATE TABLE sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    gender ENUM('Nam', 'Nữ', 'Trẻ em') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (name, gender)
);

CREATE TABLE colors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO sizes (name, gender) VALUES 
('39', 'Nam'), ('40', 'Nam'), ('41', 'Nam'), ('42', 'Nam'), ('43', 'Nam'), ('44', 'Nam'), ('45', 'Nam'),
('35', 'Nữ'), ('36', 'Nữ'), ('37', 'Nữ'), ('38', 'Nữ'), ('39', 'Nữ'),
('28', 'Trẻ em'), ('29', 'Trẻ em'), ('30', 'Trẻ em'), ('31', 'Trẻ em'), ('32', 'Trẻ em'), ('33', 'Trẻ em'), ('34', 'Trẻ em');

INSERT INTO colors (name) VALUES 
('Trắng'), ('Đen'), ('Đỏ'), ('Xanh dương'), ('Xanh lá'), ('Vàng'), ('Xám');
