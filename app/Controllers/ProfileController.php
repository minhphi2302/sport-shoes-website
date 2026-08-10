<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;

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

        $this->view('client/profile', [
            'user' => $user
        ]);
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
            $this->redirect('/profile');
        }

        $fullUser = $this->userModel->find($userId);

        if (!$this->userModel->verifyPassword($currentPassword, $fullUser['password'])) {
            $_SESSION['error'] = 'Mật khẩu hiện tại không đúng.';
            $this->redirect('/profile');
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            $this->redirect('/profile');
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'Mật khẩu xác nhận không khớp.';
            $this->redirect('/profile');
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->userModel->update($userId, ['password' => $hash]);

        $_SESSION['success'] = 'Cập nhật mật khẩu thành công.';
        $this->redirect('/profile');
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
            $this->redirect('/profile');
        }

        if (!$this->userModel->verifyPassword($password, $fullUser['password'])) {
            $_SESSION['error'] = 'Mật khẩu không đúng. Hủy thao tác xóa tài khoản.';
            $this->redirect('/profile');
        }

        try {
            $this->userModel->deleteCustomer($userId);
            session_destroy();
            session_start();
            $_SESSION['success'] = 'Tài khoản của bạn đã được xóa thành công.';
            $this->redirect('/');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa tài khoản: ' . $e->getMessage();
            $this->redirect('/profile');
        }
    }
}
