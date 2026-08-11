<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;
use App\Models\Order;

class ProfileController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $user = Auth::user();

        $orderModel = new Order();
        $orders = $orderModel->findByUserId($user['user_id'] ?? 0, 1, 50);

        $this->view('client/account', [
            'orders' => $orders,
            'user' => $user
        ]);
    }

    public function updateAccount(): void
    {
        if (!Auth::check()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $name = trim($input['name'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $address = trim($input['address'] ?? '');

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'T�n kh�ng du?c d? tr?ng']);
            return;
        }

        $userId = Auth::id();
        $this->userModel->update($userId, [
            'name' => $name,
            'phone' => $phone,
            'address' => $address
        ]);
        
        $user = $this->userModel->find($userId);
        Auth::login($user);

        echo json_encode(['success' => true]);
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
            $this->redirect('/account');
        }

        $fullUser = $this->userModel->find($userId);

        if (!$this->userModel->verifyPassword($currentPassword, $fullUser['password'])) {
            $_SESSION['error'] = 'Mật khẩu hiện tại không đúng.';
            $this->redirect('/account');
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            $this->redirect('/account');
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'Mật khẩu xác nhận không khớp.';
            $this->redirect('/account');
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->userModel->update($userId, ['password' => $hash]);

        $_SESSION['success'] = 'Cập nhật mật khẩu thành công.';
        $this->redirect('/account');
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
