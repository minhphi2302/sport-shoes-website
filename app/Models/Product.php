<?php

namespace App\Models;

use App\Core\Model;
use App\Exceptions\ValidationException;
use PDO;

class Product extends Model
{
    protected string $table = 'products';
    protected string $primaryKey = 'product_id';

    public function findAllWithFilters(array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = ["1=1"];

        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['brand_id'])) {
            $where[] = "p.brand_id = :brand_id";
            $params['brand_id'] = $filters['brand_id'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(p.name LIKE :search OR p.sku = :search_exact)";
            $params['search'] = "%{$filters['search']}%";
            $params['search_exact'] = $filters['search'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name,
                       (SELECT GROUP_CONCAT(DISTINCT size ORDER BY size SEPARATOR ', ') FROM product_variants WHERE product_id = p.product_id) as variant_sizes,
                       (SELECT GROUP_CONCAT(DISTINCT color ORDER BY color SEPARATOR ', ') FROM product_variants WHERE product_id = p.product_id) as variant_colors
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                WHERE {$whereClause}
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue(":$key", $val);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAllWithFilters(array $filters = []): int
    {
        $params = [];
        $where = ["1=1"];

        if (!empty($filters['category_id'])) {
            $where[] = "category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['brand_id'])) {
            $where[] = "brand_id = :brand_id";
            $params['brand_id'] = $filters['brand_id'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(name LIKE :search OR sku = :search_exact)";
            $params['search'] = "%{$filters['search']}%";
            $params['search_exact'] = $filters['search'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$whereClause}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    public function getFeatured(int $limit = 4): array
    {
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                ORDER BY p.created_at DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFeaturedProducts(int $limit = 4): array
    {
        return $this->getFeatured($limit);
    }

    public function getProductsFiltered(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        return $this->findAllWithFilters($filters, $page, $perPage);
    }

    public function getLatestProducts(int $limit = 8): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function saveVariants(int $productId, array $skus, array $models, array $sizes, array $colors, array $prices, array $qtys): void
    {
        // First delete existing variants
        $stmt = $this->db->prepare("DELETE FROM product_variants WHERE product_id = :id");
        $stmt->execute(['id' => $productId]);

        // Insert new variants
        $sql = "INSERT INTO product_variants (product_id, sku, model, size, color, price, quantity) VALUES (:product_id, :sku, :model, :size, :color, :price, :quantity)";
        $stmt = $this->db->prepare($sql);
        
        $product = $this->findById($productId);
        $basePrice = $product ? (float)$product['price'] : 0;

        $totalQty = 0;
        for ($i = 0; $i < count($sizes); $i++) {
            $sku = mb_strtoupper(trim($skus[$i] ?? ''), 'UTF-8');
            $model = trim($models[$i] ?? 'Mặc định');
            $size = trim($sizes[$i]);
            $color = trim($colors[$i]);
            $price = isset($prices[$i]) && $prices[$i] !== '' ? (float)$prices[$i] : null;
            $qty = (int)$qtys[$i];
            
            if ($price !== null && $price > $basePrice) {
                $price = $basePrice;
            }
            
            if ($model !== '' && $size !== '' && $color !== '' && $qty >= 0) {
                $stmt->execute([
                    'product_id' => $productId,
                    'sku' => $sku !== '' ? $sku : null,
                    'model' => $model,
                    'size' => $size,
                    'color' => $color,
                    'price' => $price,
                    'quantity' => $qty
                ]);
                $totalQty += $qty;
            }
        }
        
        // Update main product quantity to sum of variants
        $updateStmt = $this->db->prepare("UPDATE products SET quantity = :qty WHERE product_id = :id");
        $updateStmt->execute(['qty' => $totalQty, 'id' => $productId]);
    }

    public function getVariants(int $productId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM product_variants WHERE product_id = :id ORDER BY model, color, size");
        $stmt->execute(['id' => $productId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id)
    {
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                WHERE p.product_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function skuExists(string $sku, ?int $excludeId = null): bool
    {
        $sku = mb_strtoupper(trim($sku), 'UTF-8');
        $sql = 'SELECT COUNT(*) FROM products WHERE sku = :sku';
        $params = ['sku' => $sku];

        if ($excludeId !== null) {
            $sql .= ' AND product_id != :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function create(array $data): int|false
    {
        if (isset($data['sku'])) {
            $data['sku'] = mb_strtoupper(trim($data['sku']), 'UTF-8');
        }
        $this->validateProductData($data);

        $sql = 'INSERT INTO products (sku, name, description, category_id, brand_id, price, sale_price, quantity, image_url, created_at, updated_at) 
                VALUES (:sku, :name, :description, :category_id, :brand_id, :price, :sale_price, :quantity, :image_url, NOW(), NOW())';
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'sku' => $data['sku'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'category_id' => $data['category_id'],
            'brand_id' => $data['brand_id'],
            'price' => $data['price'],
            'sale_price' => $data['sale_price'] ?? null,
            'quantity' => $data['quantity'],
            'image_url' => $data['image_url'] ?? null
        ]);

        return $success ? (int)$this->db->lastInsertId() : false;
    }

    public function update(int $id, array $data): bool
    {
        if (isset($data['sku'])) {
            $data['sku'] = mb_strtoupper(trim($data['sku']), 'UTF-8');
        }
        $this->validateProductData($data, $id);

        $sql = 'UPDATE products SET 
                sku = :sku, 
                name = :name, 
                description = :description, 
                category_id = :category_id, 
                brand_id = :brand_id, 
                price = :price, 
                sale_price = :sale_price,
                quantity = :quantity';
        
        $params = [
            'sku' => $data['sku'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'category_id' => $data['category_id'],
            'brand_id' => $data['brand_id'],
            'price' => $data['price'],
            'sale_price' => $data['sale_price'] ?? null,
            'quantity' => $data['quantity'],
            'id' => $id
        ];

        if (array_key_exists('image_url', $data) && $data['image_url'] !== null) {
            $sql .= ', image_url = :image_url';
            $params['image_url'] = $data['image_url'];
        }

        $sql .= ', updated_at = NOW() WHERE product_id = :id';

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        // Get product to find image
        $product = $this->findById($id);
        
        $stmt = $this->db->prepare('DELETE FROM products WHERE product_id = :id');
        $success = $stmt->execute(['id' => $id]);

        // Delete image file if exists
        if ($success && $product && !empty($product['image_url'])) {
            $imagePath = __DIR__ . '/../../public/uploads/products/' . $product['image_url'];
            if (file_exists($imagePath)) {
                @unlink($imagePath);
            }
        }
        return $success;
    }

    private function validateProductData(array $data, ?int $excludeId = null): void
    {
        if ($this->skuExists($data['sku'], $excludeId)) {
            throw new ValidationException('sku', 'Mã sản phẩm đã tồn tại');
        }

        if ((float)$data['price'] <= 0) {
            throw new ValidationException('price', 'Giá bán phải lớn hơn 0');
        }

        if (!empty($data['sale_price']) && (float)$data['sale_price'] > (float)$data['price']) {
            throw new ValidationException('sale_price', 'Giá khuyến mãi không được lớn hơn giá bán');
        }

        if ((int)$data['quantity'] < 0) {
            throw new ValidationException('quantity', 'Tồn kho không được âm');
        }
    }
}
