ALTER TABLE users ADD COLUMN gender ENUM('male', 'female', 'other') DEFAULT 'other' AFTER phone;
