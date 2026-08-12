<?php

namespace App\Models;

use App\Core\Model;
use Exception;

class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'user_id';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Kiểm tra độ mạnh mật khẩu theo business rule USERS:
     * - Tối thiểu 8 ký tự
     * - Ít nhất 1 chữ hoa (A-Z)
     * - Ít nhất 1 chữ thường (a-z)
     * - Ít nhất 1 ký tự đặc biệt (!@#$%^&* ...)
     *
     * @param string $password Mật khẩu cần kiểm tra.
     * @return string[] Mảng thông báo lỗi, rỗng nếu hợp lệ.
     */
    public static function validatePasswordStrength(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Mật khẩu phải có ít nhất 8 ký tự.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Mật khẩu phải có ít nhất 1 chữ hoa.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Mật khẩu phải có ít nhất 1 chữ thường.';
        }
        if (!preg_match('/[\W_]/', $password)) {
            $errors[] = 'Mật khẩu phải có ít nhất 1 ký tự đặc biệt (VD: !@#$%^&*).';
        }

        return $errors;
    }

    public function saveResetToken(int $userId, string $token, string $expiresAt): void
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET reset_token = :token, reset_token_expires_at = :expires_at WHERE user_id = :id");
        $stmt->execute([
            'token' => $token,
            'expires_at' => $expiresAt,
            'id' => $userId
        ]);
    }

    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE reset_token = :token AND reset_token_expires_at > NOW() LIMIT 1");
        $stmt->execute(['token' => $token]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function clearResetToken(int $userId): void
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET reset_token = NULL, reset_token_expires_at = NULL WHERE user_id = :id");
        $stmt->execute(['id' => $userId]);
    }

    public function isLocked(array $user): bool
    {
        return isset($user['status']) && $user['status'] === 'locked';
    }

    public function recordLoginAttempt(string $email, string $ip): void
    {
        $stmt = $this->db->prepare("INSERT INTO login_attempts (email, ip_address) VALUES (:email, :ip)");
        $stmt->execute(['email' => $email, 'ip' => $ip]);
    }

    public function checkRateLimit(string $email): bool
    {
        return $this->getRecentAttemptCount($email) >= 5;
    }

    public function getRecentAttemptCount(string $email): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM login_attempts WHERE email = :email AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
        $stmt->execute(['email' => $email]);
        return (int)$stmt->fetchColumn();
    }

    public function clearLoginAttempts(string $email): void
    {
        $stmt = $this->db->prepare("DELETE FROM login_attempts WHERE email = :email");
        $stmt->execute(['email' => $email]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getAllCustomers(array $filters, int $page, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $params = ['role' => 'customer'];
        $where = ["role = :role"];

        if (!empty($filters['search'])) {
            $where[] = "(name LIKE :search OR email LIKE :search)";
            $params['search'] = "%{$filters['search']}%";
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause} ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue(":$key", $val);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countAllCustomers(array $filters): int
    {
        $params = ['role' => 'customer'];
        $where = ["role = :role"];

        if (!empty($filters['search'])) {
            $where[] = "(name LIKE :search OR email LIKE :search)";
            $params['search'] = "%{$filters['search']}%";
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$whereClause}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    public function toggleStatus(int $id): void
    {
        $user = $this->findById($id);
        if ($user) {
            $newStatus = $user['status'] === 'active' ? 'locked' : 'active';
            $stmt = $this->db->prepare("UPDATE {$this->table} SET status = :status WHERE user_id = :id");
            $stmt->execute(['status' => $newStatus, 'id' => $id]);
        }
    }

    public function deleteCustomer(int $id): void
    {
        $this->db->beginTransaction();
        try {
            // Xóa login_attempts
            $this->db->prepare("DELETE FROM login_attempts WHERE email = (SELECT email FROM {$this->table} WHERE user_id = ?)")->execute([$id]);
            
            // Xóa order_details của các đơn hàng của khách
            $this->db->prepare("DELETE FROM order_details WHERE order_id IN (SELECT order_id FROM orders WHERE user_id = ?)")->execute([$id]);
            
            // Xóa orders
            $this->db->prepare("DELETE FROM orders WHERE user_id = ?")->execute([$id]);
            
            // Xóa user
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE user_id = :id AND role = 'customer'");
            $stmt->execute(['id' => $id]);
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function autoDeleteInactiveCustomers(): int
    {
        $this->db->beginTransaction();
        try {
            $sql = "SELECT user_id, email FROM {$this->table} 
                    WHERE role = 'customer' 
                    AND (
                        status = 'locked' 
                        OR (
                            created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)
                            AND user_id NOT IN (
                                SELECT user_id FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)
                            )
                        )
                    )";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll();

            if (empty($users)) {
                $this->db->commit();
                return 0;
            }

            $userIds = array_column($users, 'user_id');
            $emails = array_column($users, 'email');
            
            $placeholders = implode(',', array_fill(0, count($userIds), '?'));
            $emailPlaceholders = implode(',', array_fill(0, count($emails), '?'));
            
            $this->db->prepare("DELETE FROM order_details WHERE order_id IN (SELECT order_id FROM orders WHERE user_id IN ($placeholders))")->execute($userIds);
            $this->db->prepare("DELETE FROM orders WHERE user_id IN ($placeholders)")->execute($userIds);
            $this->db->prepare("DELETE FROM login_attempts WHERE email IN ($emailPlaceholders)")->execute($emails);
            $this->db->prepare("DELETE FROM {$this->table} WHERE user_id IN ($placeholders)")->execute($userIds);
            
            $this->db->commit();
            return count($userIds);
        } catch (\Exception $e) {
            $this->db->rollBack();
            return 0;
        }
    }
}
