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

    /**
     * Lấy tất cả size theo giới tính
     */
    public function findByGender(string $gender): array
    {
        $stmt = $this->db->prepare('SELECT * FROM sizes WHERE gender = :gender ORDER BY name ASC');
        $stmt->execute(['gender' => $gender]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả size nhóm theo gender
     */
    public function getAllGroupedByGender(): array
    {
        $stmt = $this->db->query('SELECT * FROM sizes ORDER BY gender, CAST(name AS UNSIGNED) ASC');
        $allSizes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $grouped = [
            'Nam' => [],
            'Nữ' => [],
            'Trẻ em' => []
        ];

        foreach ($allSizes as $size) {
            $grouped[$size['gender']][] = $size;
        }

        return $grouped;
    }
}
