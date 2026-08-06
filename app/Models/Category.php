<?php

namespace App\Models;

use App\Core\Model;

/**
 * Class Category
 * Quản lý dữ liệu danh mục sản phẩm
 */
class Category extends Model
{
    protected string $table = 'categories';
    protected string $primaryKey = 'category_id';

    /**
     * Lấy tất cả danh mục
     *
     * @return array
     */
    public function getAllCategories(): array
    {
        try {
            $stmt = $this->db->query("SELECT category_id, name FROM {$this->table} ORDER BY name ASC");
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            return [
                ['category_id' => 1, 'name' => 'Running (Giày chạy bộ)']
            ];
        }
    }
}
