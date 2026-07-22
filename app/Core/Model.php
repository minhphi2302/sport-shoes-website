<?php

namespace App\Core;

use Exception;

abstract class Model
{
    protected \PDO $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lấy tất cả bản ghi
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    /**
     * Tìm một bản ghi theo ID
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Thêm mới một bản ghi
     */
    public function create(array $data): int|false
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute($data)) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật một bản ghi
     */
    public function update(int $id, array $data): bool
    {
        $setPart = '';
        foreach (array_keys($data) as $key) {
            $setPart .= "{$key} = :{$key}, ";
        }
        $setPart = rtrim($setPart, ', ');
        
        $data['id_to_update'] = $id; // Tránh trùng lặp key 'id' nếu có trong $data
        
        $sql = "UPDATE {$this->table} SET {$setPart} WHERE {$this->primaryKey} = :id_to_update";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Xóa một bản ghi
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
