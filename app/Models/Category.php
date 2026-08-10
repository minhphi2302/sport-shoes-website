<?php

namespace App\Models;

use App\Core\Model;
use App\Exceptions\CannotDeleteException;
use PDO;

class Category extends Model
{
    protected string $table = 'categories';
    protected string $primaryKey = 'category_id';

    public function create(array $data): int|false
    {
        $stmt = $this->db->prepare('INSERT INTO categories (name, description, created_at, updated_at) VALUES (:name, :description, NOW(), NOW())');
        $success = $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ]);
        return $success ? (int)$this->db->lastInsertId() : false;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare('UPDATE categories SET name = :name, description = :description, updated_at = NOW() WHERE category_id = :id');
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'id' => $id
        ]);
    }

    public function delete(int $id): bool
    {
        if ($this->hasProducts($id)) {
            throw new CannotDeleteException('Không thể xóa danh mục còn sản phẩm');
        }

        $stmt = $this->db->prepare('DELETE FROM categories WHERE category_id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function hasProducts(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE category_id = :id');
        $stmt->execute(['id' => $id]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
