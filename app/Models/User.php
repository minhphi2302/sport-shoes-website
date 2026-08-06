<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Tìm người dùng qua email
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user ?: null;
    }

    /**
     * Tạo người dùng mới
     * 
     * @param array $data ['name', 'email', 'password']
     * @return int User ID
     */
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO users (name, email, password) 
            VALUES (:name, :email, :password)
        ');
        
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password']
        ]);
        
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Cập nhật hồ sơ/địa chỉ của người dùng
     * 
     * @param int $userId
     * @param array $data ['name', 'phone', 'address']
     * @return bool
     */
    public function updateProfile(int $userId, array $data): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE users 
            SET name = :name, phone = :phone, address = :address 
            WHERE user_id = :id
        ');
        
        return $stmt->execute([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'id' => $userId
        ]);
    }
}
