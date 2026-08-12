<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;
use App\Models\Order;

class ProfileController extends Controller
{
    private User $userModel;
    private Order $orderModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->orderModel = new Order();
    }

    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $user = Auth::user();
        $userId = Auth::id();

        // Load danh sách đơn hàng của user (tất cả đơn hàng, không phân trang)
        $orders = $this->orderModel->findByUserId($userId, 1, 100); // Lấy tối đa 100 đơn

        $this->view('client/account', [
            'user' => $user,
            'orders' => $orders
        ]);
    }

    /**
     * Cập nhật thông tin cá nhân (name, phone, address) - AJAX endpoint
     */
    public function updateInfo(): void
    {
        if (!Auth::check()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $userId = Auth::id();
        
        // Đọc JSON từ request body
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        $name = trim($data['name'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $address = trim($data['address'] ?? '');

        if (empty($name)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Tên không được để trống'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            $updateData = [
                'name' => $name,
                'phone' => $phone,
                'address' => $address
            ];
            
            $this->userModel->update($userId, $updateData);
            
            // Cập nhật session
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['phone'] = $phone;
            $_SESSION['user']['address'] = $address;

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin thành công'], JSON_UNESCAPED_UNICODE);
            exit;
        } catch (\Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function updatePassword(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $userId = Auth::id();

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin.';
            $this->redirect('/account#password');
        }

        $fullUser = $this->userModel->find($userId);

        if (!$this->userModel->verifyPassword($currentPassword, $fullUser['password'])) {
            $_SESSION['error'] = 'Mật khẩu hiện tại không đúng.';
            $this->redirect('/account#password');
        }

        // Kiểm tra độ mạnh mật khẩu mới (business rule USERS)
        $pwErrors = User::validatePasswordStrength($newPassword);
        if (!empty($pwErrors)) {
            $_SESSION['error'] = implode(' ', $pwErrors);
            $this->redirect('/account#password');
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'Mật khẩu xác nhận không khớp.';
            $this->redirect('/account#password');
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->userModel->update($userId, ['password' => $hash]);

        $_SESSION['success'] = 'Cập nhật mật khẩu thành công.';
        $this->redirect('/account#password');
    }

    public function deleteAccount(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $userId = Auth::id();
        $password = $_POST['password'] ?? '';
        
        $fullUser = $this->userModel->find($userId);
        
        if ($fullUser['role'] === 'admin') {
            $_SESSION['error'] = 'Không thể xóa tài khoản quản trị viên.';
            $this->redirect('/account');
        }

        if (!$this->userModel->verifyPassword($password, $fullUser['password'])) {
            $_SESSION['error'] = 'Mật khẩu không đúng. Hủy thao tác xóa tài khoản.';
            $this->redirect('/account');
        }

        try {
            $this->userModel->deleteCustomer($userId);
            session_destroy();
            session_start();
            $_SESSION['success'] = 'Tài khoản của bạn đã được xóa thành công.';
            $this->redirect('/');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa tài khoản: ' . $e->getMessage();
            $this->redirect('/account');
        }
    }
}
