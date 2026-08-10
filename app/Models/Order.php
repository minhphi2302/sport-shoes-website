<?php

namespace App\Models;

use App\Core\Model;
use Exception;

class InsufficientStockException extends Exception {}

class Order extends Model
{
    protected string $table = 'orders';

    /**
     * Tạo đơn hàng mới cùng chi tiết đơn hàng (Sử dụng Transaction)
     * 
     * @param int $userId ID khách hàng
     * @param array $shippingData Thông tin nhận hàng (name, phone, address, payment_method, notes)
     * @param array $cartItems Các sản phẩm trong giỏ hàng
     * @param float $totalAmount Tổng tiền
     * @param float $discountAmount Tiền giảm giá (nếu có)
     * @return int Trả về Order ID nếu thành công
     * @throws Exception|InsufficientStockException Nếu lỗi hoặc hết hàng
     */
    public function createOrder(int $userId, array $shippingData, array $cartItems, float $totalAmount, float $discountAmount = 0): int
    {
        $this->db->beginTransaction();

        try {
            // 1. Tạo Order
            $sql = "INSERT INTO {$this->table} (user_id, recipient_name, recipient_phone, shipping_address, total_amount, discount_amount, payment_method, notes, status) 
                    VALUES (:user_id, :name, :phone, :address, :total, :discount, :payment, :notes, 'pending')";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':name'    => $shippingData['name'],
                ':phone'   => $shippingData['phone'],
                ':address' => $shippingData['address'],
                ':total'   => $totalAmount,
                ':discount'=> $discountAmount,
                ':payment' => $shippingData['payment_method'],
                ':notes'   => $shippingData['notes'] ?? ''
            ]);

            $orderId = (int)$this->db->lastInsertId();

            // 2. Tạo Order Details & Trừ tồn kho
            $detailSql = "INSERT INTO order_details (order_id, product_id, quantity, unit_price, subtotal) 
                          VALUES (:order_id, :product_id, :quantity, :unit_price, :subtotal)";
            $detailStmt = $this->db->prepare($detailSql);

            // Update tồn kho có điều kiện (chống race condition)
            $stockSql = "UPDATE products SET quantity = quantity - :qty1 WHERE product_id = :id AND quantity >= :qty2";
            $stockStmt = $this->db->prepare($stockSql);

            foreach ($cartItems as $item) {
                // Trừ tồn kho trước
                $stockStmt->execute([
                    ':qty1' => $item['quantity'],
                    ':qty2' => $item['quantity'],
                    ':id'   => $item['product_id']
                ]);

                if ($stockStmt->rowCount() === 0) {
                    throw new InsufficientStockException("Sản phẩm {$item['name']} không đủ số lượng trong kho.");
                }

                // Thêm chi tiết đơn hàng
                $subtotal = $item['price'] * $item['quantity'];
                $detailStmt->execute([
                    ':order_id'   => $orderId,
                    ':product_id' => $item['product_id'],
                    ':quantity'   => $item['quantity'],
                    ':unit_price' => $item['price'], // Lưu giá tại thời điểm mua
                    ':subtotal'   => $subtotal
                ]);
            }

            $this->db->commit();
            return $orderId;

        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Lấy danh sách đơn hàng của một user
     */
    public function getOrdersByUserId(int $userId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
