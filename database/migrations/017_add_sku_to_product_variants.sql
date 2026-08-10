ALTER TABLE product_variants ADD COLUMN sku VARCHAR(100) DEFAULT NULL AFTER product_id;
ALTER TABLE product_variants ADD UNIQUE INDEX idx_variant_sku (sku);
