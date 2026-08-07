<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }
        $this->view('client/auth/login');
    }

    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (empty($email) || empty($password)) {
            $this->view('client/auth/login', ['error' => 'Vui lòng nhập đầy đủ email và mật khẩu.', 'old_email' => $email]);
            return;
        }

        if ($this->userModel->checkRateLimit($email)) {
            $this->view('client/auth/login', ['error' => 'Quá nhiều lần thử, vui lòng thử lại sau 15 phút.', 'old_email' => $email]);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            $this->userModel->recordLoginAttempt($email, $ip);
            $this->view('client/auth/login', ['error' => 'Email hoặc mật khẩu không đúng.', 'old_email' => $email]);
            return;
        }

        if ($this->userModel->isLocked($user)) {
            $this->view('client/auth/login', ['error' => 'Tài khoản đã bị khóa.', 'old_email' => $email]);
            return;
        }

        $this->userModel->clearLoginAttempts($email);
        Auth::login($user);

        if ($user['role'] === 'admin') {
            $this->redirect('/admin/dashboard');
        } else {
            $this->redirect('/');
        }
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }
        $this->view('client/auth/register');
    }

    public function register(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $gender = $_POST['gender'] ?? 'other';
        if (!in_array($gender, ['male', 'female', 'other'])) {
            $gender = 'other';
        }

        $old = ['name' => $name, 'email' => $email, 'gender' => $gender];

        if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
            $this->view('client/auth/register', ['error' => 'Vui lòng nhập đầy đủ thông tin.', 'old' => $old]);
            return;
        }

        if (strlen($password) < 6) {
            $this->view('client/auth/register', ['error' => 'Mật khẩu phải có ít nhất 6 ký tự.', 'old' => $old]);
            return;
        }

        if ($password !== $confirm_password) {
            $this->view('client/auth/register', ['error' => 'Mật khẩu xác nhận không khớp.', 'old' => $old]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('client/auth/register', ['error' => 'Email không hợp lệ.', 'old' => $old]);
            return;
        }

        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser) {
            $this->view('client/auth/register', ['error' => 'Email này đã được sử dụng.', 'old' => $old]);
            return;
        }

        try {
            $userId = $this->userModel->create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'gender' => $gender,
                'role' => 'customer',
                'status' => 'active'
            ]);

            if ($userId) {
                $this->view('client/auth/login', ['success' => 'Đăng ký thành công. Vui lòng đăng nhập.', 'old_email' => $email]);
            } else {
                $this->view('client/auth/register', ['error' => 'Có lỗi xảy ra khi tạo tài khoản, vui lòng thử lại sau.', 'old' => $old]);
            }
        } catch (\Exception $e) {
            $this->view('client/auth/register', ['error' => 'Lỗi hệ thống: ' . $e->getMessage(), 'old' => $old]);
        }
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/');
    }

    public function showForgotPassword(): void
    {
        $this->view('client/auth/forgot_password');
    }

    public function processForgotPassword(): void
    {
        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $this->view('client/auth/forgot_password', ['error' => 'Vui lòng nhập email.']);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            $this->userModel->saveResetToken($user['user_id'], $token, $expiresAt);

            // Giả lập gửi email bằng cách ghi ra file log
            $resetLink = ($_ENV['APP_URL'] ?? 'http://localhost') . "/reset-password?token=" . $token;
            $logDir = dirname(__DIR__, 2) . '/storage/logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            $logContent = "[" . date('Y-m-d H:i:s') . "] Reset Password Link for {$email}: {$resetLink}\n";
            file_put_contents($logDir . '/emails.log', $logContent, FILE_APPEND);
        }

        // Luôn hiển thị thông báo thành công dù có tìm thấy email hay không (chống dò email)
        $this->view('client/auth/forgot_password', [
            'success' => "Một email kèm hướng dẫn khôi phục mật khẩu đã được gửi (Giả lập: Hãy kiểm tra file <code>storage/logs/emails.log</code> để lấy link)."
        ]);
    }

    public function showResetPassword(): void
    {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $this->redirect('/login');
        }

        $user = $this->userModel->findByResetToken($token);

        if (!$user) {
            $this->view('client/auth/login', ['error' => 'Đường dẫn khôi phục mật khẩu không hợp lệ hoặc đã hết hạn.']);
            return;
        }

        $this->view('client/auth/reset_password', ['token' => $token]);
    }

    public function processResetPassword(): void
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($password) || empty($confirmPassword)) {
            $this->view('client/auth/reset_password', ['error' => 'Vui lòng nhập đầy đủ thông tin.', 'token' => $token]);
            return;
        }

        if (strlen($password) < 6) {
            $this->view('client/auth/reset_password', ['error' => 'Mật khẩu phải có ít nhất 6 ký tự.', 'token' => $token]);
            return;
        }

        if ($password !== $confirmPassword) {
            $this->view('client/auth/reset_password', ['error' => 'Mật khẩu xác nhận không khớp.', 'token' => $token]);
            return;
        }

        $user = $this->userModel->findByResetToken($token);

        if (!$user) {
            $this->view('client/auth/login', ['error' => 'Đường dẫn khôi phục mật khẩu không hợp lệ hoặc đã hết hạn.']);
            return;
        }

        // Cập nhật mật khẩu và xóa token
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->userModel->update($user['user_id'], ['password' => $hash]);
        $this->userModel->clearResetToken($user['user_id']);

        if (Auth::check()) {
            Auth::logout();
        }

        $this->view('client/auth/login', ['success' => 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập với mật khẩu mới.']);
    }
}
