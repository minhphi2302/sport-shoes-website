<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\InsufficientStockException;

class CheckoutController extends Controller
{
    private ?Product $productModel = null;
    private ?Order $orderModel = null;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Bắt buộc đăng nhập
        if (!isset($_SESSION['user'])) {
            header('Location: ' . base_url('login') . '?redirect=checkout');
            exit;
        }

        try {
            $this->productModel = new Product();
            $this->orderModel = new Order();
        } catch (\Throwable $e) {
            // Error DB
        }
    }

    /**
     * Hiển thị trang thanh toán
     */
    public function index(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header('Location: ' . base_url('cart'));
            exit;
        }

        $cartItems = $this->getCartData($cart);
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        $shippingFee = ($totalAmount > 0 && $totalAmount < 500000) ? 50000 : 0;
        $finalAmount = $totalAmount + $shippingFee;

        $this->view('client/checkout', [
            'title' => 'Thanh toán — ANTA',
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'shippingFee' => $shippingFee,
            'finalAmount' => $finalAmount,
            'user' => $_SESSION['user']
        ]);
    }

    /**
     * Xử lý đặt hàng
     */
    public function process(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . base_url('checkout'));
            exit;
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header('Location: ' . base_url('cart'));
            exit;
        }

        $cartItems = $this->getCartData($cart);
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        $shippingData = [
            'name' => trim($_POST['recipient_name'] ?? ''),
            'phone' => trim($_POST['recipient_phone'] ?? ''),
            'address' => trim(($_POST['street_address'] ?? '') . ', ' . ($_POST['ward_district_city'] ?? '') . ', ' . ($_POST['country'] ?? '')),
            'payment_method' => trim($_POST['payment_method'] ?? 'COD'),
            'notes' => trim($_POST['notes'] ?? '')
        ];

        // Validate basic
        if (empty($shippingData['name']) || empty($shippingData['phone']) || empty($shippingData['address'])) {
            $_SESSION['checkout_error'] = "Vui lòng nhập đầy đủ thông tin nhận hàng.";
            header('Location: ' . base_url('checkout'));
            exit;
        }

        $shippingFee = ($totalAmount > 0 && $totalAmount < 500000) ? 50000 : 0;
        $finalAmount = $totalAmount + $shippingFee;

        try {
            $userId = $_SESSION['user']['id'];
            $orderId = $this->orderModel->createOrder($userId, $shippingData, $cartItems, $finalAmount);

            // Clear cart
            unset($_SESSION['cart']);

            // Redirect to success
            header('Location: ' . base_url("checkout/success?id={$orderId}"));
            exit;

        } catch (InsufficientStockException $e) {
            $_SESSION['checkout_error'] = $e->getMessage();
            header('Location: ' . base_url('checkout'));
            exit;
        } catch (\Throwable $e) {
            $_SESSION['checkout_error'] = "Đã xảy ra lỗi hệ thống: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
            header('Location: ' . base_url('checkout'));
            exit;
        }
    }

    /**
     * Trang thành công
     */
    public function success(): void
    {
        $orderId = $_GET['id'] ?? null;
        $this->view('client/checkout_success', [
            'title' => 'Đặt hàng thành công — ANTA',
            'orderId' => $orderId
        ]);
    }

    /**
     * Hàm helper lấy dữ liệu thật của giỏ hàng từ DB
     */
    private function getCartData(array $cart): array
    {
        $cartItems = [];
        foreach ($cart as $cartKey => $item) {
            $productId = $item['product_id'] ?? $cartKey;
            $product = $this->productModel ? $this->productModel->getProductById((int)$productId) : null;
            if ($product) {
                $price = ($product['sale_price'] ?? 0) > 0 ? $product['sale_price'] : $product['price'];
                $cartItems[] = [
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'sku' => $product['sku'] ?? '',
                    'image_url' => !empty($product['image_url']) ? $product['image_url'] : 'image/slide/slide' . (($product['product_id'] % 3) + 1) . '.avif',
                    'price' => $price,
                    'quantity' => $item['quantity'],
                    'size' => $item['size'] ?? 41,
                    'color' => $item['color'] ?? 'Black',
                ];
            }
        }
        return $cartItems;
    }
}
