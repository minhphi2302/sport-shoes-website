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
        if (!empty($filters['gender'])) {
            $where[] = "EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = p.product_id AND pv.size LIKE :gender)";
            $params['gender'] = $filters['gender'] . ' - %';
        }
        if (!empty($filters['size'])) {
            $where[] = "EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = p.product_id AND pv.size LIKE :size)";
            $params['size'] = '% - ' . $filters['size'];
        }
        if (!empty($filters['color'])) {
            $where[] = "EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = p.product_id AND pv.color = :color)";
            $params['color'] = $filters['color'];
        }

        $whereClause = implode(' AND ', $where);

        // Sắp xếp theo yêu cầu — whitelist để tránh SQL injection
        $orderBy = match ($filters['sort'] ?? '') {
            'newest' => 'p.created_at DESC',
            'bestseller' => '(SELECT COALESCE(SUM(od.quantity), 0) FROM order_details od INNER JOIN orders o ON od.order_id = o.order_id WHERE od.product_id = p.product_id AND o.status IN ("completed","confirmed")) DESC, p.created_at DESC',
            'price_asc' => 'COALESCE(NULLIF(p.sale_price, 0), p.price) ASC',
            'price_desc' => 'COALESCE(NULLIF(p.sale_price, 0), p.price) DESC',
            default => 'p.created_at DESC',
        };

        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name,
                       (SELECT GROUP_CONCAT(DISTINCT size ORDER BY size SEPARATOR ', ') FROM product_variants WHERE product_id = p.product_id) as variant_sizes,
                       (SELECT GROUP_CONCAT(DISTINCT color ORDER BY color SEPARATOR ', ') FROM product_variants WHERE product_id = p.product_id) as variant_colors
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                WHERE {$whereClause}
                ORDER BY {$orderBy}
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

        if (!empty($filters['gender'])) {
            $where[] = "EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = products.product_id AND pv.size LIKE :gender)";
            $params['gender'] = $filters['gender'] . ' - %';
        }
        if (!empty($filters['size'])) {
            $where[] = "EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = products.product_id AND pv.size LIKE :size)";
            $params['size'] = '% - ' . $filters['size'];
        }
        if (!empty($filters['color'])) {
            $where[] = "EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = products.product_id AND pv.color = :color)";
            $params['color'] = $filters['color'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$whereClause}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
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

    public function getLatestProducts(int $limit = 8): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function saveVariants(int $productId, array $skus, array $models, array $sizes, array $colors, array $prices, array $qtys): void
    {
        $product = $this->findById($productId);
        $basePrice = $product ? (float) $product['price'] : 0;

        // 1. Lấy variants hiện có từ DB
        $existingVariants = $this->getVariants($productId);
        $existingMap = [];
        foreach ($existingVariants as $ev) {
            $key = trim($ev['model']) . '|' . trim($ev['size']) . '|' . trim($ev['color']);
            $evPrice = (float) $ev['price'];
            $existingMap[$key] = [
                'variant_id' => $ev['variant_id'],
                'sku' => $ev['sku'],
                'model' => $ev['model'],
                'size' => $ev['size'],
                'color' => $ev['color'],
                'price' => $evPrice,
                'quantity' => (int) $ev['quantity']
            ];
        }

        // 2. Chuẩn hóa variants mới từ form
        $newVariants = [];
        for ($i = 0; $i < count($sizes); $i++) {
            $model = trim($models[$i] ?? 'Mặc định');
            $size = trim($sizes[$i]);
            $color = trim($colors[$i]);
            $price = isset($prices[$i]) && $prices[$i] !== '' ? (float) $prices[$i] : null;
            $qty = (int) $qtys[$i];
            $sku = mb_strtoupper(trim($skus[$i] ?? ''), 'UTF-8');

            // Giới hạn giá không vượt giá gốc
            if ($price !== null && $price > $basePrice) {
                $price = $basePrice;
            }

            if ($model !== '' && $size !== '' && $color !== '' && $qty >= 0) {
                $key = $model . '|' . $size . '|' . $color;
                $newVariants[] = [
                    'key' => $key,
                    'sku' => $sku !== '' ? $sku : null,
                    'model' => $model,
                    'size' => $size,
                    'color' => $color,
                    'price' => $price,
                    'quantity' => $qty
                ];
            }
        }

        // 3. Kiểm tra trùng lặp TRONG form submit
        // Business rule: CHỈ CHO PHÉP thêm biến thể nếu hoàn toàn mới (không trùng model+size+màu)
        // Nếu trùng → BÁO LỖI, KHÔNG cộng dồn
        $seenInForm = [];
        foreach ($newVariants as $nv) {
            $key = $nv['key'];
            if (isset($seenInForm[$key])) {
                // Trùng model+size+màu trong cùng lần submit → Báo lỗi
                throw new ValidationException(
                    'variants',
                    "Biến thể trùng lặp: {$nv['model']} - {$nv['size']} - {$nv['color']}. Vui lòng kiểm tra lại."
                );
            }
            $seenInForm[$key] = $nv;
        }

        // 4. Kiểm tra trùng với variants ĐÃ TỒN TẠI trong DB
        $finalVariants = [];

        foreach ($seenInForm as $key => $newVar) {
            if (isset($existingMap[$key])) {
                // Biến thể ĐÃ TỒN TẠI trong DB → Cho phép CẬP NHẬT (giá, số lượng, SKU)
                // Đây là trường hợp EDIT biến thể hiện có, không phải thêm mới
                $existingVar = $existingMap[$key];

                $finalVariants[$key] = [
                    'variant_id' => $existingVar['variant_id'],
                    'sku' => $newVar['sku'] ?? $existingVar['sku'],
                    'model' => $newVar['model'],
                    'size' => $newVar['size'],
                    'color' => $newVar['color'],
                    'price' => $newVar['price'], // Cập nhật giá mới
                    'quantity' => $newVar['quantity'], // Cập nhật số lượng mới (GHI ĐÈ, không cộng dồn)
                    'is_update' => true
                ];
            } else {
                // Biến thể MỚI hoàn toàn → Thêm vào DB
                $finalVariants[$key] = [
                    'sku' => $newVar['sku'],
                    'model' => $newVar['model'],
                    'size' => $newVar['size'],
                    'color' => $newVar['color'],
                    'price' => $newVar['price'],
                    'quantity' => $newVar['quantity'],
                    'is_update' => false
                ];
            }
        }

        // 5. Xóa các variants KHÔNG còn trong form (bị user xóa khỏi danh sách)
        foreach ($existingMap as $key => $ev) {
            if (!isset($finalVariants[$key])) {
                $deleteStmt = $this->db->prepare("DELETE FROM product_variants WHERE id = :id");
                $deleteStmt->execute(['id' => $ev['variant_id']]);
            }
        }

        // 6. INSERT hoặc UPDATE variants
        $insertSql = "INSERT INTO product_variants (product_id, sku, model, size, color, price, quantity) 
                      VALUES (:product_id, :sku, :model, :size, :color, :price, :quantity)";
        $insertStmt = $this->db->prepare($insertSql);

        $updateSql = "UPDATE product_variants 
                      SET sku = :sku, model = :model, size = :size, color = :color, price = :price, quantity = :quantity
                      WHERE id = :variant_id";
        $updateStmt = $this->db->prepare($updateSql);

        $totalQty = 0;
        foreach ($finalVariants as $variant) {
            if ($variant['is_update']) {
                // UPDATE biến thể đã tồn tại (ghi đè số lượng mới)
                $updateStmt->execute([
                    'variant_id' => $variant['variant_id'],
                    'sku' => $variant['sku'],
                    'model' => $variant['model'],
                    'size' => $variant['size'],
                    'color' => $variant['color'],
                    'price' => $variant['price'],
                    'quantity' => $variant['quantity']
                ]);
            } else {
                // INSERT biến thể mới
                $insertStmt->execute([
                    'product_id' => $productId,
                    'sku' => $variant['sku'],
                    'model' => $variant['model'],
                    'size' => $variant['size'],
                    'color' => $variant['color'],
                    'price' => $variant['price'],
                    'quantity' => $variant['quantity']
                ]);
            }
            $totalQty += $variant['quantity'];
        }

        // 7. Cập nhật tổng số lượng vào bảng products
        $updateProductStmt = $this->db->prepare("UPDATE products SET quantity = :qty WHERE product_id = :id");
        $updateProductStmt->execute(['qty' => $totalQty, 'id' => $productId]);
    }

    public function getVariants(int $productId): array
    {
        $stmt = $this->db->prepare("SELECT id as variant_id, product_id, sku, model, size, color, price, quantity FROM product_variants WHERE product_id = :id ORDER BY model, color, size");
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
        return (int) $stmt->fetchColumn() > 0;
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

        return $success ? (int) $this->db->lastInsertId() : false;
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

        if ((float) $data['price'] <= 0) {
            throw new ValidationException('price', 'Giá bán phải lớn hơn 0');
        }

        if (!empty($data['sale_price']) && (float) $data['sale_price'] > (float) $data['price']) {
            throw new ValidationException('sale_price', 'Giá khuyến mãi không được lớn hơn giá bán');
        }

        if ((int) $data['quantity'] < 0) {
            throw new ValidationException('quantity', 'Tồn kho không được âm');
        }
    }
}
