<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;


class CartController extends Controller
{
    private ?Product $productModel = null;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        try {
            $this->productModel = new Product();
        } catch (\Throwable $e) {
            // DB chưa khởi tạo
        }
    }

    /**
     * Hiển thị Trang giỏ hàng
     */
    public function index(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $totalAmount = 0;

        foreach ($cart as $productId => $item) {
            $product = $this->productModel ? $this->productModel->getProductById((int)$productId) : null;
            if ($product) {
                $price = !empty($product['sale_price']) ? $product['sale_price'] : $product['price'];
                $itemData = [
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'image_url' => !empty($product['image_url']) ? $product['image_url'] : 'image/slide/slide' . (($product['product_id'] % 3) + 1) . '.avif',
                    'price' => $price,
                    'quantity' => $item['quantity'],
                    'size' => $item['size'] ?? 41,
                    'gender' => $item['gender'] ?? 'male'
                ];
                $cartItems[] = $itemData;
                $totalAmount += $price * $item['quantity'];
            } else {
                // Fallback nếu chưa có trong DB
                $cartItems[] = [
                    'product_id' => $productId,
                    'name' => 'Nike Air Zoom Pegasus 39',
                    'image_url' => 'image/slide/slide' . (($productId % 3) + 1) . '.avif',
                    'price' => 2500000,
                    'quantity' => $item['quantity'] ?? 1,
                    'size' => $item['size'] ?? 41,
                    'gender' => $item['gender'] ?? 'male'
                ];
                $totalAmount += 2500000 * ($item['quantity'] ?? 1);
            }
        }

        $this->view('client/cart', [
            'title' => 'Giỏ hàng — Anta',
            'currentPage' => 'cart',
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount
        ]);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add(?int $id = null): void
    {
        $productId = $_POST['product_id'] ?? $id ?? $_GET['id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 1);
        $size = $_POST['size'] ?? 41;
        $gender = $_POST['gender'] ?? 'male';
        $action = $_POST['action'] ?? 'add';

        if ($productId) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] += $quantity;
                if (isset($_POST['size'])) $_SESSION['cart'][$productId]['size'] = $size;
                if (isset($_POST['gender'])) $_SESSION['cart'][$productId]['gender'] = $gender;
            } else {
                $_SESSION['cart'][$productId] = [
                    'quantity' => $quantity,
                    'size' => $size,
                    'gender' => $gender
                ];
            }
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            
            // Get product info for the response
            $product = $productId ? ($this->productModel ? $this->productModel->getProductById((int)$productId) : null) : null;
            $productName = $product ? $product['name'] : 'Sản phẩm';
            $productImage = $product && !empty($product['image_url']) ? base_url($product['image_url']) : base_url('image/slide/slide' . ((($productId ?? 1) % 3) + 1) . '.avif');
            
            // Calculate total amount
            $cart = $_SESSION['cart'] ?? [];
            $totalAmount = 0;
            $totalQuantity = 0;
            foreach ($cart as $id => $item) {
                $p = $this->productModel ? $this->productModel->getProductById((int)$id) : null;
                $price = $p ? (!empty($p['sale_price']) ? $p['sale_price'] : $p['price']) : 2500000;
                $totalAmount += $price * $item['quantity'];
                $totalQuantity += $item['quantity'];
            }

            echo json_encode([
                'success' => true,
                'cartCount' => $totalQuantity,
                'totalAmount' => number_format($totalAmount, 0, ',', '.'),
                'productName' => $productName,
                'productImage' => $productImage,
                'productSize' => $size,
                'message' => 'Thêm vào giỏ hàng thành công'
            ]);
            exit;
        }

        if ($action === 'buy_now') {
            $this->redirect('/checkout');
        } else {
            $this->redirect('/cart');
        }
    }

    /**
     * Cập nhật giỏ hàng (Số lượng, Size)
     */
    public function update(): void
    {
        if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
            foreach ($_POST['quantity'] as $productId => $qty) {
                if (isset($_SESSION['cart'][$productId])) {
                    $qty = (int)$qty;
                    if ($qty > 0) {
                        $_SESSION['cart'][$productId]['quantity'] = $qty;
                    }
                }
            }
        }
        
        if (isset($_POST['size']) && is_array($_POST['size'])) {
            foreach ($_POST['size'] as $productId => $size) {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['size'] = $size;
                }
            }
        }
        
        if (isset($_POST['gender']) && is_array($_POST['gender'])) {
            foreach ($_POST['gender'] as $productId => $gender) {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['gender'] = $gender;
                }
            }
        }

        $this->redirect('/cart');
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function remove(int $id): void
    {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        $this->redirect('/cart');
    }

    /**
     * Xóa sạch giỏ hàng
     */
    public function clear(): void
    {
        $_SESSION['cart'] = [];
        $this->redirect('/cart');
    }

    /**
     * Hiển thị Trang thanh toán
     */
    public function checkout(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $totalAmount = 0;

        foreach ($cart as $productId => $item) {
            $product = $this->productModel ? $this->productModel->getProductById((int)$productId) : null;
            $price = $product ? (!empty($product['sale_price']) ? $product['sale_price'] : $product['price']) : 2500000;
            
            $cartItems[] = [
                'product_id' => $productId,
                'name' => $product ? $product['name'] : 'Nike Air Zoom Pegasus 39',
                'image_url' => $product && !empty($product['image_url']) ? $product['image_url'] : 'image/slide/slide' . (($productId % 3) + 1) . '.avif',
                'price' => $price,
                'quantity' => $item['quantity'] ?? 1,
                'size' => $item['size'] ?? 41,
                'gender' => $item['gender'] ?? 'male'
            ];
            $totalAmount += $price * ($item['quantity'] ?? 1);
        }

        $this->view('client/checkout', [
            'title' => 'Thanh toán đơn hàng — Anta',
            'currentPage' => 'checkout',
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount
        ]);
    }

    /**
     * Xử lý Đặt hàng
     */
    public function processCheckout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Giả lập đặt hàng thành công & xóa giỏ hàng
            $_SESSION['cart'] = [];
            
            echo "<script>
                alert('Đặt hàng thành công! Cảm ơn bạn đã mua hàng tại Anta.');
                window.location.href = '" . base_url('/') . "';
            </script>";
            exit;
        }
    }
}
