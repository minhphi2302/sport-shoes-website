<?php

namespace App\Models;

use App\Core\Model;
use App\Exceptions\CannotDeleteException;
use App\Exceptions\ValidationException;
use PDO;

class Brand extends Model
{
    protected string $table = 'brands';
    protected string $primaryKey = 'brand_id';

    public function create(array $data): int|false
    {
        if ($this->nameExists($data['name'])) {
            throw new ValidationException('name', 'Tên thương hiệu đã tồn tại');
        }

        $stmt = $this->db->prepare('INSERT INTO brands (name, description, created_at, updated_at) VALUES (:name, :description, NOW(), NOW())');
        $success = $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ]);
        return $success ? (int)$this->db->lastInsertId() : false;
    }

    public function update(int $id, array $data): bool
    {
        if ($this->nameExists($data['name'], $id)) {
            throw new ValidationException('name', 'Tên thương hiệu đã tồn tại');
        }

        $stmt = $this->db->prepare('UPDATE brands SET name = :name, description = :description, updated_at = NOW() WHERE brand_id = :id');
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'id' => $id
        ]);
    }

    public function delete(int $id): bool
    {
        if ($this->hasProducts($id)) {
            throw new CannotDeleteException('Không thể xóa thương hiệu còn sản phẩm');
        }

        $stmt = $this->db->prepare('DELETE FROM brands WHERE brand_id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function hasProducts(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE brand_id = :id');
        $stmt->execute(['id' => $id]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM brands WHERE name = :name';
        $params = ['name' => $name];

        if ($excludeId !== null) {
            $sql .= ' AND brand_id != :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }
}
