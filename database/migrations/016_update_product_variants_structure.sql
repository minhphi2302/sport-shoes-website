ALTER TABLE product_variants ADD COLUMN model VARCHAR(100) DEFAULT NULL AFTER product_id;
ALTER TABLE product_variants DROP FOREIGN KEY product_variants_ibfk_1;
ALTER TABLE product_variants DROP INDEX unique_variant;
ALTER TABLE product_variants ADD UNIQUE KEY unique_variant (product_id, model, color, size);
ALTER TABLE product_variants ADD CONSTRAINT product_variants_ibfk_1 FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE;
