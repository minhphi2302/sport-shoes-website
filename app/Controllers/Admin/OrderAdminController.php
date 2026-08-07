<?php

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Models\Order;
use App\Exceptions\InvalidOrderTransitionException;

class OrderAdminController extends AdminController
{
    private Order $orderModel;

    public function __construct()
    {
        parent::__construct();
        $this->orderModel = new Order();
    }

    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;

        $filters = [];
        if (!empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        if (!empty($_GET['search'])) {
            $filters['search'] = trim($_GET['search']);
        }

        $orders = $this->orderModel->findAll($filters, $page, $perPage);
        $totalOrders = $this->orderModel->countAll($filters);
        $totalPages = ceil($totalOrders / $perPage);

        $this->view('admin/order_list', [
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'filters' => $filters
        ]);
    }

    public function show($id): void
    {
        $orderId = (int)$id;
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            http_response_code(404);
            die('Đơn hàng không tồn tại.');
        }

        $orderDetails = $this->orderModel->getOrderDetails($orderId);

        $this->view('admin/order_detail', [
            'order' => $order,
            'orderDetails' => $orderDetails
        ]);
    }

    public function updateStatus($id): void
    {
        $orderId = (int)$id;
        $newStatus = $_POST['status'] ?? '';

        if (empty($newStatus)) {
            $_SESSION['error'] = 'Trạng thái không hợp lệ.';
            $this->redirect("/admin/orders/{$orderId}");
        }

        try {
            $this->orderModel->updateStatus($orderId, $newStatus);
            $_SESSION['success'] = 'Cập nhật trạng thái thành công.';
        } catch (InvalidOrderTransitionException $e) {
            $_SESSION['error'] = $e->getMessage();
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }

        $this->redirect("/admin/orders/{$orderId}");
    }
}
