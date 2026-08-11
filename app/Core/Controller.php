<?php

namespace App\Core;

abstract class Controller
{
    /**
     * Render một file View và truyền dữ liệu vào
     *
     * @param string $view Tên file view (VD: client/product_list)
     * @param array $data Dữ liệu truyền vào view
     */
    protected function view(string $view, array $data = []): void
    {
        // Giải nén mảng data thành các biến riêng lẻ
        extract($data);
        
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            $viewPath = __DIR__ . '/../../views/' . $view . '.php';
        }
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("View {$view} không tồn tại.");
        }
    }

    /**
     * Trả về JSON cho các request AJAX
     */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    /**
     * Chuyển hướng trang
     */
    protected function redirect(string $url): void
    {
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            header("Location: " . $url);
            exit;
        }

        $baseUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
        if (!str_starts_with($url, '/')) {
            $url = '/' . $url;
        }

        header("Location: " . $baseUrl . $url);
        exit;
    }
}
