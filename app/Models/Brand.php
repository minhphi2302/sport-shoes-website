<?php

namespace App\Models;

use App\Core\Model;

/**
 * Class Brand
 * Quản lý dữ liệu thương hiệu
 */
class Brand extends Model
{
    protected string $table = 'brands';
    protected string $primaryKey = 'brand_id';

    /**
     * Lấy danh sách thương hiệu
     *
     * @return array
     */
    public function getAllBrands(): array
    {
        try {
            $stmt = $this->db->query("SELECT min(brand_id) as brand_id, name FROM {$this->table} GROUP BY name ORDER BY name ASC");
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            return [
                ['brand_id' => 1, 'name' => 'Nike'],
                ['brand_id' => 2, 'name' => 'Adidas'],
                ['brand_id' => 3, 'name' => 'Puma'],
                ['brand_id' => 4, 'name' => 'Converse'],
                ['brand_id' => 5, 'name' => 'New Balance']
            ];
        }
    }
}
