<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Order;
use App\Exceptions\InsufficientStockException;

class OrderController extends Controller
{
    private Order $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    public function showCheckout(): void
    {
        if (!Auth::check()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiến hành đặt hàng.';
            $this->redirect('/login');
        }

        if (Auth::user()['role'] === 'admin') {
            $_SESSION['error'] = 'Tài khoản admin không được phép sử dụng chức năng mua hàng.';
            $this->redirect('/cart');
        }

        if (empty($_SESSION['cart'])) {
            $_SESSION['error'] = 'Giỏ hàng của bạn đang trống.';
            $this->redirect('/cart');
        }

        $user = Auth::user();
        $cart = $_SESSION['cart'];
        $subtotal = 0;

        foreach ($cart as $item) {
            $priceToUse = (!empty($item['sale_price']) && $item['sale_price'] < $item['price']) ? $item['sale_price'] : $item['price'];
            $subtotal += $priceToUse * $item['quantity'];
        }

        $threshold = defined('FREE_COD_THRESHOLD') ? FREE_COD_THRESHOLD : 1000000;
        $codFeeConfig = defined('COD_FEE') ? COD_FEE : 30000;
        $shippingFee = ($subtotal >= $threshold) ? 0 : $codFeeConfig;
        $grandTotal = $subtotal + $shippingFee;

        // Điền sẵn SĐT và địa chỉ từ đơn hàng gần nhất nếu profile chưa có
        if (empty($user['phone']) || empty($user['address'])) {
            $lastOrder = $this->orderModel->getLastOrderByUserId($user['user_id']);
            if ($lastOrder) {
                if (empty($user['phone'])) {
                    $user['phone'] = $lastOrder['recipient_phone'];
                }
                if (empty($user['address'])) {
                    $user['address'] = $lastOrder['shipping_address'];
                }
            }
        }

        $this->view('client/checkout', [
            'user' => $user,
            'cart' => $cart,
            'cartItems' => $cart, // View sử dụng biến này
            'subtotal' => $subtotal,
            'totalAmount' => $subtotal, // View sử dụng biến này
            'shippingFee' => $shippingFee,
            'grandTotal' => $grandTotal,
            'finalAmount' => $grandTotal, // View sử dụng biến này
            'freeThreshold' => $threshold,
            'codFeeConfig' => $codFeeConfig
        ]);
    }

    public function placeOrder(): void
    {
        if (!Auth::check()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiến hành đặt hàng.';
            $this->redirect('/login');
        }

        if (Auth::user()['role'] === 'admin') {
            $_SESSION['error'] = 'Tài khoản admin không được phép sử dụng chức năng mua hàng.';
            $this->redirect('/cart');
        }

        if (empty($_SESSION['cart'])) {
            $_SESSION['error'] = 'Giỏ hàng của bạn đang trống.';
            $this->redirect('/cart');
        }

        $userId = Auth::id();
        // Lấy đúng tên field từ form checkout
        $name = trim($_POST['recipient_name'] ?? '');
        $phone = trim($_POST['recipient_phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $streetAddress = trim($_POST['street_address'] ?? '');
        $address = $streetAddress; // Chỉ dùng địa chỉ đường
        $paymentMethod = $_POST['payment_method'] ?? 'COD';
        $notes = trim($_POST['notes'] ?? '');

        if (empty($name) || empty($phone) || empty($streetAddress)) {
            $_SESSION['checkout_error'] = 'Vui lòng nhập đầy đủ thông tin giao hàng.';
            $this->redirect('/checkout');
        }

        $shippingInfo = [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'payment_method' => $paymentMethod,
            'notes' => $notes
        ];

        try {
            $orderId = $this->orderModel->createOrder($userId, $shippingInfo, $_SESSION['cart']);
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            $this->redirect("/orders/{$orderId}/success");

        } catch (InsufficientStockException $e) {
            $_SESSION['checkout_error'] = $e->getMessage();
            $this->redirect('/cart');
        } catch (\Exception $e) {
            // Debug: hiển thị lỗi chi tiết
            $_SESSION['checkout_error'] = 'Lỗi: ' . $e->getMessage() . ' (File: ' . basename($e->getFile()) . ' Line: ' . $e->getLine() . ')';
            error_log('Order Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $this->redirect('/checkout');
        }
    }

    public function myOrders(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $userId = Auth::id();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        $orders = $this->orderModel->findByUserId($userId, $page, $perPage);
        $totalOrders = $this->orderModel->countByUserId($userId);
        $totalPages = ceil($totalOrders / $perPage);

        $this->view('client/order_list', [
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function show($id): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $orderId = (int)$id;
        $order = $this->orderModel->find($orderId);

        if (!$order || $order['user_id'] != Auth::id()) {
            http_response_code(403);
            die('403 Forbidden - Bạn không có quyền xem đơn hàng này.');
        }

        $orderDetails = $this->orderModel->getOrderDetails($orderId);

        $this->view('client/order_detail', [
            'order' => $order,
            'orderDetails' => $orderDetails
        ]);
    }

    public function showSuccess($id): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $orderId = (int)$id;
        $order = $this->orderModel->find($orderId);

        if (!$order || $order['user_id'] != Auth::id()) {
            $this->redirect('/');
        }

        $this->view('client/order_success', [
            'order' => $order
        ]);
    }

    public function cancel($id): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $orderId = (int)$id;
        $order = $this->orderModel->find($orderId);

        if (!$order || $order['user_id'] != Auth::id()) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này.';
            $this->redirect('/orders');
        }

        if ($order['status'] !== 'pending') {
            $_SESSION['error'] = 'Chỉ có thể hủy đơn hàng khi ở trạng thái chờ xử lý.';
            $this->redirect("/orders/{$orderId}");
        }

        try {
            $this->orderModel->updateStatus($orderId, 'cancelled');
            $_SESSION['success'] = 'Hủy đơn hàng thành công.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }

        $this->redirect("/orders/{$orderId}");
    }
}
