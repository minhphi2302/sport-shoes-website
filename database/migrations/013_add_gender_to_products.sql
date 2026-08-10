ALTER TABLE products ADD COLUMN gender ENUM('male', 'female') DEFAULT 'male' AFTER brand_id;
