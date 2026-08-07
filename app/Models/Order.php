<?php

namespace App\Models;

use App\Core\Model;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidOrderTransitionException;
use PDO;
use Exception;

class Order extends Model
{
    protected string $table = 'orders';
    protected string $primaryKey = 'order_id';

    private const VALID_TRANSITIONS = [
        'pending'   => ['confirmed', 'cancelled'],
        'confirmed' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function createOrder(int $userId, array $shippingInfo, array $cartItems): int
    {
        $this->db->beginTransaction();
        try {
            $totalAmount = 0;
            $productModel = new Product();
            $itemsWithCurrentPrice = [];
            
            foreach ($cartItems as $item) {
                $product = $productModel->findById($item['product_id']);
                if (!$product) {
                    throw new Exception("Sản phẩm #{$item['product_id']} không tồn tại.");
                }
                
                $priceToUse = (!empty($product['sale_price']) && $product['sale_price'] < $product['price']) ? $product['sale_price'] : $product['price'];
                $subtotal = $priceToUse * $item['quantity'];
                $totalAmount += $subtotal;
                
                $item['current_price'] = $priceToUse;
                $item['subtotal'] = $subtotal;
                $item['product_name'] = $product['name'];
                $item['size'] = $item['size'] ?? null;
                $item['color'] = $item['color'] ?? null;
                $itemsWithCurrentPrice[] = $item;
            }

            $orderSql = "INSERT INTO orders (user_id, recipient_name, recipient_phone, shipping_address, total_amount, payment_method, notes, status, created_at, updated_at) 
                         VALUES (:user_id, :recipient_name, :recipient_phone, :shipping_address, :total_amount, :payment_method, :notes, 'pending', NOW(), NOW())";
            $stmt = $this->db->prepare($orderSql);
            $stmt->execute([
                'user_id' => $userId,
                'recipient_name' => $shippingInfo['name'],
                'recipient_phone' => $shippingInfo['phone'],
                'shipping_address' => $shippingInfo['address'],
                'total_amount' => $totalAmount,
                'payment_method' => $shippingInfo['payment_method'],
                'notes' => $shippingInfo['notes'] ?? null,
            ]);
            
            $orderId = (int)$this->db->lastInsertId();

            foreach ($itemsWithCurrentPrice as $item) {
                if (!empty($item['size']) && !empty($item['color'])) {
                    $stmtVar = $this->db->prepare(
                        'UPDATE product_variants SET quantity = quantity - :qty
                         WHERE product_id = :id AND size = :size AND color = :color AND quantity >= :qty_check'
                    );
                    $stmtVar->execute([
                        'qty' => $item['quantity'], 
                        'id' => $item['product_id'],
                        'size' => $item['size'],
                        'color' => $item['color'],
                        'qty_check' => $item['quantity']
                    ]);
                    
                    if ($stmtVar->rowCount() === 0) {
                        $varName = $item['product_name'] . ' (Size ' . $item['size'] . ' - Màu ' . $item['color'] . ')';
                        throw new InsufficientStockException("Phân loại \"{$varName}\" hiện không đủ tồn kho.");
                    }
                }

                $stmt = $this->db->prepare(
                    'UPDATE products SET quantity = quantity - :qty
                     WHERE product_id = :id AND quantity >= :qty_check'
                );
                $stmt->execute([
                    'qty' => $item['quantity'], 
                    'id' => $item['product_id'],
                    'qty_check' => $item['quantity']
                ]);

                if ($stmt->rowCount() === 0) {
                    throw new InsufficientStockException(
                        "Sản phẩm \"{$item['product_name']}\" hiện không đủ tồn kho chung."
                    );
                }

                $detailSql = "INSERT INTO order_details (order_id, product_id, quantity, unit_price, subtotal, size, color) 
                              VALUES (:order_id, :product_id, :quantity, :unit_price, :subtotal, :size, :color)";
                $stmtDetail = $this->db->prepare($detailSql);
                $stmtDetail->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['current_price'],
                    'subtotal' => $item['subtotal'],
                    'size' => $item['size'],
                    'color' => $item['color']
                ]);
            }

            $this->db->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function findByUserId(int $userId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countByUserId(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = ["1=1"];

        if (!empty($filters['status'])) {
            $where[] = "o.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(o.order_id = :search_id OR u.name LIKE :search_name)";
            $params['search_id'] = (int)$filters['search'];
            $params['search_name'] = '%' . $filters['search'] . '%';
        }

        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT o.*, u.name AS customer_name, u.email AS customer_email 
                FROM {$this->table} o
                INNER JOIN users u ON o.user_id = u.user_id
                WHERE {$whereClause}
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll(array $filters = []): int
    {
        $params = [];
        $where = ["1=1"];

        if (!empty($filters['status'])) {
            $where[] = "o.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(o.order_id = :search_id OR u.name LIKE :search_name)";
            $params['search_id'] = (int)$filters['search'];
            $params['search_name'] = '%' . $filters['search'] . '%';
        }

        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT COUNT(*) FROM {$this->table} o INNER JOIN users u ON o.user_id = u.user_id WHERE {$whereClause}";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getOrderDetails(int $orderId): array
    {
        $sql = "SELECT od.*, p.name AS product_name, p.image_url, p.sku 
                FROM order_details od
                INNER JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $orderId, string $newStatus): void
    {
        $order = $this->find($orderId);
        if (!$order) {
            throw new Exception("Đơn hàng không tồn tại.");
        }
        $currentStatus = $order['status'];

        if (!in_array($newStatus, self::VALID_TRANSITIONS[$currentStatus] ?? [], true)) {
            throw new InvalidOrderTransitionException(
                "Không thể chuyển từ '{$currentStatus}' sang '{$newStatus}'"
            );
        }

        if ($newStatus === 'cancelled') {
            $this->db->beginTransaction();
            try {
                $stmt = $this->db->prepare(
                    'UPDATE orders SET status = :status, updated_at = NOW() WHERE order_id = :id'
                );
                $stmt->execute(['status' => $newStatus, 'id' => $orderId]);

                $orderDetails = $this->getOrderDetails($orderId);
                foreach ($orderDetails as $item) {
                    if (!empty($item['size']) && !empty($item['color'])) {
                        $stmtVar = $this->db->prepare(
                            'UPDATE product_variants SET quantity = quantity + :qty
                             WHERE product_id = :id AND size = :size AND color = :color'
                        );
                        $stmtVar->execute([
                            'qty' => $item['quantity'],
                            'id' => $item['product_id'],
                            'size' => $item['size'],
                            'color' => $item['color']
                        ]);
                    }

                    $stmtProd = $this->db->prepare(
                        'UPDATE products SET quantity = quantity + :qty
                         WHERE product_id = :id'
                    );
                    $stmtProd->execute([
                        'qty' => $item['quantity'],
                        'id' => $item['product_id']
                    ]);
                }

                $this->db->commit();
            } catch (\Throwable $e) {
                $this->db->rollBack();
                throw $e;
            }
        } else {
            $stmt = $this->db->prepare(
                'UPDATE orders SET status = :status, updated_at = NOW() WHERE order_id = :id'
            );
            $stmt->execute(['status' => $newStatus, 'id' => $orderId]);
        }
    }

    public function getMonthlyRevenue(): float
    {
        $sql = "SELECT SUM(total_amount) FROM {$this->table} 
                WHERE status = 'completed' 
                AND MONTH(created_at) = MONTH(NOW()) 
                AND YEAR(created_at) = YEAR(NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (float)$stmt->fetchColumn();
    }

    public function getTodayOrdersCount(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getLatestPendingOrders(int $limit = 5): array
    {
        $sql = "SELECT o.*, u.name AS customer_name 
                FROM {$this->table} o
                INNER JOIN users u ON o.user_id = u.user_id
                WHERE o.status = 'pending'
                ORDER BY o.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
