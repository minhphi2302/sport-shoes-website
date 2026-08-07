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
            $_SESSION['error'] = 'Tài khoản admin không được sử dụng chức năng mua hàng.';
            $this->redirect('/cart');
        }

        if (empty($_SESSION['cart'])) {
            $_SESSION['error'] = 'Giỏ hàng của bạn đang trống.';
            $this->redirect('/cart');
        }

        $user = Auth::user();
        $cart = $_SESSION['cart'];
        $total = 0;

        foreach ($cart as $item) {
            $priceToUse = (!empty($item['sale_price']) && $item['sale_price'] < $item['price']) ? $item['sale_price'] : $item['price'];
            $total += $priceToUse * $item['quantity'];
        }

        $this->view('client/checkout', [
            'user' => $user,
            'cart' => $cart,
            'total' => $total
        ]);
    }

    public function placeOrder(): void
    {
        if (!Auth::check()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiến hành đặt hàng.';
            $this->redirect('/login');
        }

        if (Auth::user()['role'] === 'admin') {
            $_SESSION['error'] = 'Tài khoản admin không được sử dụng chức năng mua hàng.';
            $this->redirect('/cart');
        }

        if (empty($_SESSION['cart'])) {
            $_SESSION['error'] = 'Giỏ hàng của bạn đang trống.';
            $this->redirect('/cart');
        }

        $userId = Auth::id();
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $paymentMethod = $_POST['payment_method'] ?? 'COD';
        $notes = trim($_POST['notes'] ?? '');

        if (empty($name) || empty($phone) || empty($address)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ tên, số điện thoại và địa chỉ giao hàng.';
            $this->redirect('/checkout');
        }

        $shippingInfo = [
            'name' => $name,
            'phone' => $phone,
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
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cart');
        } catch (\Exception $e) {
            // Log error in real app
            $_SESSION['error'] = 'Đã có lỗi xảy ra trong quá trình đặt hàng: ' . $e->getMessage();
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
