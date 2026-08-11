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
        $cartRaw = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $total = 0;

        foreach ($cartRaw as $key => $item) {
            $price = $item['price'] ?? 0;
            $salePrice = $item['sale_price'] ?? null;
            $priceToUse = (!empty($salePrice) && $salePrice < $price) ? $salePrice : $price;
            $total += $priceToUse * ($item['quantity'] ?? 1);

            // Parse available sizes/colors/genders from variant size field "Gender - Size"
            $variants = $this->productModel->getVariants((int)$item['product_id']);
            $availableSizes = [];
            $availableColors = [];
            $availableGenders = [];
            foreach ($variants as $v) {
                $parts = explode(' - ', $v['size']);
                $g = count($parts) > 1 ? trim($parts[0]) : 'Nam';
                $s = count($parts) > 1 ? trim($parts[1]) : trim($parts[0]);
                $availableSizes[] = $s;
                $availableColors[] = $v['color'];
                $availableGenders[] = $g;
            }
            $cartItems[$key] = array_merge($item, [
                'available_sizes'   => array_unique($availableSizes) ?: [$item['size']],
                'available_colors'  => array_unique($availableColors) ?: [$item['color']],
                'available_genders' => array_unique($availableGenders) ?: ['Nam'],
            ]);
        }

        $this->view('client/cart', [
            'cartItems' => $cartItems,
            'total'     => $total
        ]);
    }

    public function add(mixed $id = null): void
    {
        $productId = (int)($id ?: ($_POST['product_id'] ?? $_GET['product_id'] ?? 0));
        $quantity = (int)($_POST['quantity'] ?? $_GET['quantity'] ?? 1);
        $referer = $_SERVER['HTTP_REFERER'] ?? ($_ENV['APP_URL'] ?? '') . '/products';

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
                if ($v['variant_id'] === $variantId) {
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
                $variantId = (int)$firstV['variant_id']; // Using variant_id from db
                $maxQty = (int)$firstV['quantity'];
                $priceToUse = isset($firstV['price']) ? $firstV['price'] : $product['price'];
                $skuToUse = !empty($firstV['sku']) ? $firstV['sku'] : $product['sku'];
                $variantModel = $firstV['model'] ?? 'Mặc định';
                $variantSize = $firstV['size'] ?? '';
                $variantColor = $firstV['color'] ?? '';
                
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
            $msg = $_SESSION['success'];
            unset($_SESSION['success']); // Xoá ngay để không hiện lại khi reload
            header('Content-Type: application/json');
            echo json_encode([
                'success'    => true,
                'message'    => $msg,
                'cart_count' => array_sum(array_column($_SESSION['cart'], 'quantity'))
            ]);
            exit;
        }

        $this->redirect('/cart');
    }

    public function update(): void
    {
        if (Auth::check() && Auth::user()['role'] === 'admin') {
            $_SESSION['error'] = "Tài khoản admin không được phép sử dụng chức năng mua hàng.";
            $this->redirect('/cart');
        }

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // Batch update: quantity[cart_key] => qty
        $quantityMap = $_POST['quantity'] ?? [];

        if (!empty($quantityMap) && is_array($quantityMap)) {
            foreach ($quantityMap as $cartKey => $qty) {
                $qty = (int)$qty;
                if (!isset($_SESSION['cart'][$cartKey])) continue;

                if ($qty <= 0) {
                    unset($_SESSION['cart'][$cartKey]);
                    continue;
                }

                $productId = $_SESSION['cart'][$cartKey]['product_id'];
                $product   = $this->productModel->findById((int)$productId);
                if (!$product) { unset($_SESSION['cart'][$cartKey]); continue; }

                $variantId = $_SESSION['cart'][$cartKey]['variant_id'] ?? 0;
                $maxQty    = $product['quantity'];
                if ($variantId > 0) {
                    $variants = $this->productModel->getVariants((int)$productId);
                    foreach ($variants as $v) {
                        if ($v['variant_id'] === (int)$variantId) {
                            $maxQty = $v['quantity'];
                            break;
                        }
                    }
                }
                $_SESSION['cart'][$cartKey]['quantity'] = min($qty, $maxQty);
            }
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success'    => true,
                'cart_count' => array_sum(array_column($_SESSION['cart'], 'quantity'))
            ]);
            exit;
        }

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
