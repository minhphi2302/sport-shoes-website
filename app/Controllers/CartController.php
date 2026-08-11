<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Core\Auth;

class CartController extends Controller
{
    private Product $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
        Auth::initSession();
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function index(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $total = 0;

        foreach ($cart as $item) {
            $price = $item['price'] ?? 0;
            $salePrice = $item['sale_price'] ?? null;
            $priceToUse = (!empty($salePrice) && $salePrice < $price) ? $salePrice : $price;
            $qty = $item['quantity'] ?? 1;
            $total += $priceToUse * $qty;
        }

        $this->view('client/cart', [
            'cart' => $cart,
            'cartItems' => $cart,
            'total' => $total
        ]);
    }

    public function add(mixed $id = null): void
    {
        $productId = (int)($id ?: ($_POST['product_id'] ?? $_GET['product_id'] ?? 0));
        $quantity = (int)($_POST['quantity'] ?? $_GET['quantity'] ?? 1);
        $referer = $_SERVER['HTTP_REFERER'] ?? base_url('products');

        if (Auth::check() && Auth::user()['role'] === 'admin') {
            $_SESSION['error'] = "Tài khoản admin không được phép sử dụng chức năng mua hàng.";
            $this->redirect($referer);
        }

        if ($productId <= 0 || $quantity <= 0) {
            $this->redirect($referer);
        }

        $product = $this->productModel->findById($productId);
        if (!$product) {
            $this->redirect($referer);
        }

        $variantId = (int)($_POST['variant_id'] ?? 0);
        $cartKey = $productId . ($variantId ? '_v' . $variantId : '');

        $currentQtyInCart = isset($_SESSION['cart'][$cartKey]) ? $_SESSION['cart'][$cartKey]['quantity'] : 0;
        $newQty = $currentQtyInCart + $quantity;

        // Fetch variant to check stock and exact price
        $maxQty = $product['quantity'];
        $priceToUse = $product['price'];
        $skuToUse = $product['sku'];
        $variantModel = '';
        $variantSize = '';
        $variantColor = '';

        if ($variantId > 0) {
            $variants = $this->productModel->getVariants($productId);
            $foundVariant = false;
            foreach ($variants as $v) {
                if ($v['id'] === $variantId) {
                    $maxQty = $v['quantity'];
                    $priceToUse = isset($v['price']) ? $v['price'] : $product['price'];
                    $skuToUse = !empty($v['sku']) ? $v['sku'] : $product['sku'];
                    $variantModel = $v['model'] ?? 'Mặc định';
                    $variantSize = $v['size'];
                    $variantColor = $v['color'];
                    $foundVariant = true;
                    break;
                }
            }
            if (!$foundVariant) {
                $_SESSION['error'] = "Biến thể sản phẩm không hợp lệ.";
                $this->redirect($referer);
            }
        } else {
            // Check if product HAS variants, if yes, select the first available variant for quick add
            $variants = $this->productModel->getVariants($productId);
            if (!empty($variants)) {
                $firstV = $variants[0];
                $variantId = $firstV['id'];
                $maxQty = $firstV['quantity'];
                $priceToUse = isset($firstV['price']) ? $firstV['price'] : $product['price'];
                $skuToUse = !empty($firstV['sku']) ? $firstV['sku'] : $product['sku'];
                $variantModel = $firstV['model'] ?? 'Mặc định';
                $variantSize = $firstV['size'];
                $variantColor = $firstV['color'];
                $cartKey = $productId . '_v' . $variantId;
                $currentQtyInCart = isset($_SESSION['cart'][$cartKey]) ? $_SESSION['cart'][$cartKey]['quantity'] : 0;
                $newQty = $currentQtyInCart + $quantity;
            }
        }

        if ($newQty > $maxQty) {
            $_SESSION['error'] = "Phân loại này chỉ còn {$maxQty} sản phẩm trong kho.";
            $this->redirect($referer);
        }

        $_SESSION['cart'][$cartKey] = [
            'cart_key' => $cartKey,
            'product_id' => $product['product_id'],
            'variant_id' => $variantId,
            'name' => $product['name'],
            'price' => $priceToUse,
            'sale_price' => $product['sale_price'],
            'quantity' => $newQty,
            'image_url' => $product['image_url'],
            'sku' => $skuToUse,
            'model' => $variantModel,
            'size' => $variantSize,
            'color' => $variantColor
        ];

        $_SESSION['success'] = "Đã thêm \"" . $product['name'] . "\" vào giỏ hàng thành công.";

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $_SESSION['success'],
                'cart_count' => array_sum(array_column($_SESSION['cart'], 'quantity'))
            ]);
            unset($_SESSION['success']);
            exit;
        }

        $this->redirect('/cart');
    }

    public function update(): void
    {
        $cartKey = $_POST['cart_key'] ?? '';
        $quantity = (int)($_POST['quantity'] ?? 0);

        if (Auth::check() && Auth::user()['role'] === 'admin') {
            $_SESSION['error'] = "Tài khoản admin không được phép sử dụng chức năng mua hàng.";
            $this->redirect('/cart');
        }

        if (empty($cartKey)) {
            $this->redirect('/cart');
        }

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$cartKey]);
            $_SESSION['success'] = "Đã xóa sản phẩm khỏi giỏ hàng.";
            $this->redirect('/cart');
        }
        
        $productId = isset($_SESSION['cart'][$cartKey]) ? $_SESSION['cart'][$cartKey]['product_id'] : 0;
        $product = $this->productModel->findById($productId);
        
        if (!$product || !isset($_SESSION['cart'][$cartKey])) {
            unset($_SESSION['cart'][$cartKey]);
            $this->redirect('/cart');
        }

        $variantId = $_SESSION['cart'][$cartKey]['variant_id'] ?? 0;
        
        $maxQty = $product['quantity'];
        if ($variantId > 0) {
            $variants = $this->productModel->getVariants($productId);
            foreach ($variants as $v) {
                if ($v['id'] === $variantId) {
                    $maxQty = $v['quantity'];
                    break;
                }
            }
        }

        if ($quantity > $maxQty) {
            $_SESSION['error'] = "Phân loại này chỉ còn {$maxQty} sản phẩm trong kho.";
            $this->redirect('/cart');
        }

        $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
        $_SESSION['success'] = "Đã cập nhật giỏ hàng.";

        $this->redirect('/cart');
    }

    public function remove(): void
    {
        $cartKey = $_POST['cart_key'] ?? '';
        if (!empty($cartKey) && isset($_SESSION['cart'][$cartKey])) {
            unset($_SESSION['cart'][$cartKey]);
            $_SESSION['success'] = "Đã xóa sản phẩm khỏi giỏ hàng.";
        }
        $this->redirect('/cart');
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
        $_SESSION['success'] = "Đã xóa toàn bộ giỏ hàng.";
        $this->redirect('/cart');
    }
}
