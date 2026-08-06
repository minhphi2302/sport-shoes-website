<?php

namespace App\Models;

use App\Core\Model;

/**
 * Class Product
 * Quản lý dữ liệu và truy vấn sản phẩm
 */
class Product extends Model
{
    protected string $table = 'products';
    protected string $primaryKey = 'product_id';

    /**
     * Lấy sản phẩm nổi bật / mới nhất
     *
     * @param int $limit
     * @return array
     */
    public function getFeaturedProducts(int $limit = 8): array
    {
        try {
            $sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name 
                    FROM {$this->table} p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
                    WHERE p.status = 'active'
                    ORDER BY p.created_at DESC
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Lấy sản phẩm theo bộ lọc + phân trang
     *
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getProductsFiltered(array $filters = [], int $page = 1, int $perPage = 12): array
    {
        try {
            $offset = ($page - 1) * $perPage;
            $params = [];
            $whereClause = "WHERE p.status = 'active'";

            if (!empty($filters['category_id'])) {
                $whereClause .= " AND p.category_id = :category_id";
                $params[':category_id'] = (int)$filters['category_id'];
            }

            if (!empty($filters['brand_id'])) {
                $whereClause .= " AND p.brand_id = :brand_id";
                $params[':brand_id'] = (int)$filters['brand_id'];
            }

            if (!empty($filters['gender'])) {
                $whereClause .= " AND p.gender = :gender";
                $params[':gender'] = $filters['gender'];
            }

            if (!empty($filters['sale'])) {
                $whereClause .= " AND p.sale_price IS NOT NULL AND p.sale_price < p.price";
            }

            if (!empty($filters['search'])) {
                $whereClause .= " AND p.name LIKE :search";
                $params[':search'] = '%' . $filters['search'] . '%';
            }

            // Sắp xếp
            $sortQuery = "ORDER BY p.created_at DESC";
            if (!empty($filters['sort'])) {
                if ($filters['sort'] === 'price_asc') {
                    $sortQuery = "ORDER BY COALESCE(p.sale_price, p.price) ASC";
                } elseif ($filters['sort'] === 'price_desc') {
                    $sortQuery = "ORDER BY COALESCE(p.sale_price, p.price) DESC";
                } elseif ($filters['sort'] === 'newest') {
                    $sortQuery = "ORDER BY p.created_at DESC";
                }
            }

            $sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name 
                    FROM {$this->table} p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
                    {$whereClause}
                    {$sortQuery}
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Đếm tổng số sản phẩm theo bộ lọc
     */
    public function countProductsFiltered(array $filters = []): int
    {
        try {
            $params = [];
            $whereClause = "WHERE p.status = 'active'";

            if (!empty($filters['category_id'])) {
                $whereClause .= " AND p.category_id = :category_id";
                $params[':category_id'] = (int)$filters['category_id'];
            }

            if (!empty($filters['brand_id'])) {
                $whereClause .= " AND p.brand_id = :brand_id";
                $params[':brand_id'] = (int)$filters['brand_id'];
            }

            if (!empty($filters['gender'])) {
                $whereClause .= " AND p.gender = :gender";
                $params[':gender'] = $filters['gender'];
            }

            if (!empty($filters['sale'])) {
                $whereClause .= " AND p.sale_price IS NOT NULL AND p.sale_price < p.price";
            }

            if (!empty($filters['search'])) {
                $whereClause .= " AND p.name LIKE :search";
                $params[':search'] = '%' . $filters['search'] . '%';
            }

            $sql = "SELECT COUNT(*) FROM {$this->table} p {$whereClause}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return (int)$stmt->fetchColumn();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Lấy thông tin chi tiết một sản phẩm theo ID
     */
    public function getProductById(int $id): ?array
    {
        try {
            $sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name 
                    FROM {$this->table} p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
                    WHERE p.product_id = :id AND p.status = 'active'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $product = $stmt->fetch();

            return $product ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Lấy danh sách sản phẩm liên quan
     */
    public function getRelatedProducts(int $categoryId, int $currentProductId, int $limit = 4): array
    {
        try {
            $sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name 
                    FROM {$this->table} p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
                    WHERE p.category_id = :category_id 
                      AND p.product_id != :current_id 
                      AND p.status = 'active'
                    ORDER BY p.created_at DESC
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':category_id', $categoryId, \PDO::PARAM_INT);
            $stmt->bindValue(':current_id', $currentProductId, \PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
