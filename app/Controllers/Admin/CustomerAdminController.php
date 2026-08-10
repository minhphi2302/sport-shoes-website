<?php

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Models\User;
use App\Core\Auth;

class CustomerAdminController extends AdminController
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index(): void
    {
        // Tự động dọn dẹp khách hàng không hoạt động hoặc bị khóa
        $deletedCount = $this->userModel->autoDeleteInactiveCustomers();
        if ($deletedCount > 0) {
            $_SESSION['success'] = "Đã tự động xóa {$deletedCount} tài khoản khách hàng không hoạt động (đã khóa hoặc không có đơn hàng trong 1 năm qua).";
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        
        $filters = [];
        if (!empty($_GET['search'])) {
            $filters['search'] = trim($_GET['search']);
        }

        $customers = $this->userModel->getAllCustomers($filters, $page, $perPage);
        $totalCustomers = $this->userModel->countAllCustomers($filters);
        $totalPages = ceil($totalCustomers / $perPage);

        $this->view('admin/customer_list', [
            'customers' => $customers,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'filters' => $filters
        ]);
    }

    public function show($id): void
    {
        $userId = (int)$id;
        $customer = $this->userModel->findById($userId);

        if (!$customer || $customer['role'] !== 'customer') {
            $_SESSION['error'] = 'Khách hàng không tồn tại.';
            $this->redirect('/admin/customers');
        }

        $this->view('admin/customer_detail', [
            'customer' => $customer
        ]);
    }

    public function toggleStatus($id): void
    {
        $userId = (int)$id;
        
        if ($userId === Auth::id()) {
            $_SESSION['error'] = 'Bạn không thể tự khóa chính mình.';
            $this->redirect('/admin/customers');
        }

        try {
            $this->userModel->toggleStatus($userId);
            $_SESSION['success'] = 'Cập nhật trạng thái thành công.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra.';
        }

        $this->redirect('/admin/customers');
    }

    public function delete($id): void
    {
        $userId = (int)$id;
        
        if ($userId === Auth::id()) {
            $_SESSION['error'] = 'Bạn không thể tự xóa chính mình.';
            $this->redirect('/admin/customers');
        }

        $customer = $this->userModel->findById($userId);
        if (!$customer || $customer['role'] !== 'customer') {
            $_SESSION['error'] = 'Khách hàng không tồn tại.';
            $this->redirect('/admin/customers');
        }

        try {
            $this->userModel->deleteCustomer($userId);
            $_SESSION['success'] = 'Xóa tài khoản khách hàng thành công.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa: ' . $e->getMessage();
        }

        $this->redirect('/admin/customers');
    }
}
