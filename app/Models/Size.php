<?php

namespace App\Models;

use App\Core\Model;
use App\Exceptions\ValidationException;

class Size extends Model
{
    protected string $table = 'sizes';
    protected string $primaryKey = 'id';

    public function create(array $data): int|false
    {
        if ($this->exists($data['name'], $data['gender'])) {
            throw new ValidationException('name', 'Size này đã tồn tại cho đối tượng này');
        }

        $stmt = $this->db->prepare('INSERT INTO sizes (name, gender) VALUES (:name, :gender)');
        $success = $stmt->execute([
            'name' => $data['name'],
            'gender' => $data['gender']
        ]);
        return $success ? (int)$this->db->lastInsertId() : false;
    }

    public function update(int $id, array $data): bool
    {
        if ($this->exists($data['name'], $data['gender'], $id)) {
            throw new ValidationException('name', 'Size này đã tồn tại cho đối tượng này');
        }

        $stmt = $this->db->prepare('UPDATE sizes SET name = :name, gender = :gender WHERE id = :id');
        return $stmt->execute([
            'name' => $data['name'],
            'gender' => $data['gender'],
            'id' => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM sizes WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function exists(string $name, string $gender, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM sizes WHERE name = :name AND gender = :gender';
        $params = ['name' => $name, 'gender' => $gender];

        if ($excludeId !== null) {
            $sql .= ' AND id != :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }
}
