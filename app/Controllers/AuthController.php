<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Class AuthController
 * Quản lý Đăng nhập & Đăng ký tài khoản
 */
class AuthController extends Controller
{
    private \App\Models\User $userModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new \App\Models\User();
    }

    /**
     * Trang & Xử lý Đăng nhập
     */
    public function login(): void
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = 'Vui lòng nhập đầy đủ Email và Mật khẩu!';
            } else {
                $user = $this->userModel->findByEmail($email);
                
                if ($user && password_verify($password, $user['password'])) {
                    if ($user['status'] === 'locked') {
                        $error = 'Tài khoản của bạn đã bị khóa!';
                    } else {
                        // Đăng nhập thành công
                        $_SESSION['user'] = [
                            'id' => $user['user_id'],
                            'name' => $user['name'],
                            'email' => $user['email'],
                            'role' => $user['role'],
                            'phone' => $user['phone'],
                            'address' => $user['address']
                        ];
                        
                        $redirectUrl = '/';
                        if (!empty($_GET['redirect'])) {
                            $allowedRedirects = ['/checkout', '/cart', '/account'];
                            $target = '/' . ltrim($_GET['redirect'], '/');
                            if (in_array($target, $allowedRedirects)) {
                                $redirectUrl = $target;
                            }
                        }
                        
                        $this->redirect($redirectUrl);
                        return;
                    }
                } else {
                    $error = 'Email hoặc mật khẩu không chính xác!';
                }
            }
        }

        $this->view('client/login', [
            'title' => 'Đăng nhập — Anta',
            'currentPage' => 'login',
            'error' => $error
        ]);
    }

    /**
     * Trang & Xử lý Đăng ký
     */
    public function register(): void
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            if (empty($name) || empty($email) || empty($password)) {
                $error = 'Vui lòng nhập đầy đủ thông tin!';
            } elseif ($password !== $passwordConfirm) {
                $error = 'Mật khẩu xác nhận không khớp!';
            } else {
                // Kiểm tra email tồn tại
                if ($this->userModel->findByEmail($email)) {
                    $error = 'Email này đã được sử dụng!';
                } else {
                    // Hash password và lưu
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $userId = $this->userModel->create([
                        'name' => $name,
                        'email' => $email,
                        'password' => $hashedPassword
                    ]);
                    
                    // Tự động đăng nhập
                    $_SESSION['user'] = [
                        'id' => $userId,
                        'name' => $name,
                        'email' => $email,
                        'role' => 'customer',
                        'phone' => null,
                        'address' => null
                    ];
                    
                    $redirectUrl = '/';
                    if (!empty($_GET['redirect'])) {
                        $allowedRedirects = ['/checkout', '/cart', '/account'];
                        $target = '/' . ltrim($_GET['redirect'], '/');
                        if (in_array($target, $allowedRedirects)) {
                            $redirectUrl = $target;
                        }
                    }
                    
                    $this->redirect($redirectUrl);
                    return;
                }
            }
        }

        $this->view('client/register', [
            'title' => 'Đăng ký tài khoản — Anta',
            'currentPage' => 'register',
            'error' => $error
        ]);
    }

    /**
     * Đăng xuất
     */
    public function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
        $this->redirect('/');
    }

    /**
     * Trang Tài khoản
     */
    public function account(): void
    {
        // Chặn nếu chưa đăng nhập
        if (empty($_SESSION['user'])) {
            $this->redirect('/login?redirect=account');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $orderModel = new \App\Models\Order();
        $orders = $orderModel->getOrdersByUserId($userId);

        $this->view('client/account', [
            'title' => 'Tài khoản của tôi — Anta',
            'currentPage' => 'account',
            'orders' => $orders
        ]);
    }

    /**
     * Cập nhật thông tin (POST)
     */
    public function updateProfile(): void
    {
        if (empty($_SESSION['user'])) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy JSON payload nếu là AJAX fetch, nếu không lấy từ $_POST
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                $data = $_POST;
            }

            $userId = $_SESSION['user']['id'] ?? 0;
            $name = trim($data['name'] ?? '');
            $phone = trim($data['phone'] ?? '');
            $address = trim($data['address'] ?? '');

            if (empty($name)) {
                echo json_encode(['success' => false, 'message' => 'Tên không được để trống']);
                return;
            }

            $success = $this->userModel->updateProfile($userId, [
                'name' => $name,
                'phone' => $phone,
                'address' => $address
            ]);

            if ($success) {
                // Cập nhật lại session
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['phone'] = $phone;
                $_SESSION['user']['address'] = $address;

                echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
            }
            return;
        }
        
        // Không phải POST
        header('HTTP/1.1 405 Method Not Allowed');
    }
}
