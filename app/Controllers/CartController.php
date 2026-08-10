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

        foreach ($cart as $cartKey => $item) {
            $productId = $item['product_id'] ?? $cartKey;
            $product = $this->productModel ? $this->productModel->getProductById((int)$productId) : null;
            if ($product) {
                // Lấy variants từ DB
                $variants = $this->productModel->getProductVariants((int)$productId);
                $availableSizes = array_unique(array_column($variants, 'size'));
                $availableColors = array_unique(array_column($variants, 'color'));
                if (empty($availableSizes)) $availableSizes = [39, 40, 41, 42, 43, 44];
                if (empty($availableColors)) $availableColors = ['Đen', 'Đỏ'];

                $price = ($product['sale_price'] ?? 0) > 0 ? $product['sale_price'] : $product['price'];
                $itemData = [
                    'cart_key' => $cartKey,
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'sku' => $product['sku'] ?? 'N/A',
                    'image_url' => !empty($product['image_url']) ? $product['image_url'] : 'image/slide/slide' . (($product['product_id'] % 3) + 1) . '.avif',
                    'price' => $price,
                    'quantity' => $item['quantity'],
                    'size' => $item['size'] ?? 41,
                    'color' => $item['color'] ?? 'Black',
                    'gender' => $item['gender'] ?? 'male',
                    'available_sizes' => $availableSizes,
                    'available_colors' => $availableColors,
                    'available_genders' => $this->productModel ? $this->productModel->getAllAvailableGenders() : ['male', 'female']
                ];
                $cartItems[] = $itemData;
                $totalAmount += $price * $item['quantity'];
            }
        }

        $shippingFee = ($totalAmount > 0 && $totalAmount < 500000) ? 50000 : 0;
        $finalAmount = $totalAmount + $shippingFee;

        $this->view('client/cart', [
            'title' => 'Giỏ hàng của bạn — ANTA',
            'currentPage' => 'cart',
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'shippingFee' => $shippingFee,
            'finalAmount' => $finalAmount
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
        $color = $_POST['color'] ?? 'Black';
        $gender = $_POST['gender'] ?? 'male';
        $action = $_POST['action'] ?? 'add';

        if ($productId) {
            $product = $this->productModel ? $this->productModel->getProductById((int)$productId) : null;
            if (!$product || ($product['quantity'] ?? 0) <= 0) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Sản phẩm này đã tạm hết hàng.'
                    ]);
                    exit;
                } else {
                    $_SESSION['error'] = 'Sản phẩm này đã tạm hết hàng.';
                    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
                    exit;
                }
            }

            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            $foundKey = null;
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['product_id'] == $productId && $item['size'] == $size && ($item['color'] ?? '') == $color && ($item['gender'] ?? '') == $gender) {
                    $foundKey = $key;
                    break;
                }
            }

            if ($foundKey !== null) {
                $_SESSION['cart'][$foundKey]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'size' => $size,
                    'color' => $color,
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
            foreach ($cart as $cartKey => $item) {
                $pId = $item['product_id'] ?? $cartKey;
                $p = $this->productModel ? $this->productModel->getProductById((int)$pId) : null;
                $price = $p ? ((($p['sale_price'] ?? 0) > 0) ? $p['sale_price'] : $p['price']) : 2500000;
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
                'message' => 'Sản phẩm đã được thêm vào giỏ hàng.'
            ]);
            exit;
        }

        if ($action === 'buy_now') {
            $this->redirect('/checkout');
        } else {
            $this->redirect('/cart');
        }
    }

    public function update(): void
    {
        if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
            foreach ($_POST['quantity'] as $cartKey => $qty) {
                if (isset($_SESSION['cart'][$cartKey])) {
                    $qty = (int)$qty;
                    if ($qty > 0) {
                        $_SESSION['cart'][$cartKey]['quantity'] = $qty;
                    }
                }
            }
        }
        
        if (isset($_POST['size']) && is_array($_POST['size'])) {
            foreach ($_POST['size'] as $cartKey => $size) {
                if (isset($_SESSION['cart'][$cartKey])) {
                    $_SESSION['cart'][$cartKey]['size'] = $size;
                }
            }
        }
        
        if (isset($_POST['color']) && is_array($_POST['color'])) {
            foreach ($_POST['color'] as $cartKey => $color) {
                if (isset($_SESSION['cart'][$cartKey])) {
                    $_SESSION['cart'][$cartKey]['color'] = $color;
                }
            }
        }
        
        if (isset($_POST['gender']) && is_array($_POST['gender'])) {
            foreach ($_POST['gender'] as $cartKey => $gender) {
                if (isset($_SESSION['cart'][$cartKey])) {
                    $_SESSION['cart'][$cartKey]['gender'] = $gender;
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

        foreach ($cart as $cartKey => $item) {
            $productId = $item['product_id'] ?? $cartKey;
            $product = $this->productModel ? $this->productModel->getProductById((int)$productId) : null;
            if ($product) {
                $price = ($product['sale_price'] ?? 0) > 0 ? $product['sale_price'] : $product['price'];
                
                $cartItems[] = [
                    'cart_key' => $cartKey,
                    'product_id' => $productId,
                    'name' => $product['name'],
                    'sku' => $product['sku'] ?? 'N/A',
                    'image_url' => !empty($product['image_url']) ? $product['image_url'] : 'image/slide/slide' . (($productId % 3) + 1) . '.avif',
                    'price' => $price,
                    'quantity' => $item['quantity'] ?? 1,
                    'size' => $item['size'] ?? 41,
                    'color' => $item['color'] ?? 'Black',
                    'gender' => $item['gender'] ?? 'male'
                ];
                $totalAmount += $price * ($item['quantity'] ?? 1);
            }
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
