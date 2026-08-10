<?php

namespace App\Models;

use App\Core\Model;
use App\Exceptions\ValidationException;

class Color extends Model
{
    protected string $table = 'colors';
    protected string $primaryKey = 'id';

    public function create(array $data): int|false
    {
        if ($this->exists($data['name'])) {
            throw new ValidationException('name', 'Màu sắc này đã tồn tại');
        }

        $stmt = $this->db->prepare('INSERT INTO colors (name) VALUES (:name)');
        $success = $stmt->execute([
            'name' => $data['name']
        ]);
        return $success ? (int)$this->db->lastInsertId() : false;
    }

    public function update(int $id, array $data): bool
    {
        if ($this->exists($data['name'], $id)) {
            throw new ValidationException('name', 'Màu sắc này đã tồn tại');
        }

        $stmt = $this->db->prepare('UPDATE colors SET name = :name WHERE id = :id');
        return $stmt->execute([
            'name' => $data['name'],
            'id' => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM colors WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function exists(string $name, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM colors WHERE name = :name';
        $params = ['name' => $name];

        if ($excludeId !== null) {
            $sql .= ' AND id != :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }
}
