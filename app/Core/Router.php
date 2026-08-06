<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string|array|callable $action): void
    {
        $this->addRoute('GET', $path, $action);
    }

    public function post(string $path, string|array|callable $action): void
    {
        $this->addRoute('POST', $path, $action);
    }

    private function addRoute(string $method, string $path, string|array|callable $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'action' => $action
        ];
    }

    public function resolve(string $uri, string $method): mixed
    {
        // 1. Lấy đường dẫn URI
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // 2. Loại bỏ index.php
        $path = preg_replace('#/index\.php#i', '', $path);

        // 3. Chuẩn hóa đường dẫn subfolder Laragon/Localhost
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $baseDir = preg_replace('#/public$#i', '', $scriptDir);

        if ($baseDir !== '' && $baseDir !== '/' && $baseDir !== '.' && str_starts_with($path, $baseDir)) {
            $path = substr($path, strlen($baseDir));
        }

        // Loại bỏ /public ở đầu nếu còn
        if (str_starts_with($path, '/public')) {
            $path = substr($path, strlen('/public'));
        }

        if ($path === '' || $path === false) {
            $path = '/';
        }

        // Loại bỏ dấu / ở cuối ngoại trừ '/' Trang chủ
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                // Chuyển /product/{id} thành regex
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $route['path']);
                $pattern = "#^" . $pattern . "$#";

                if (preg_match($pattern, $path, $matches)) {
                    // Lọc lấy các tham số là chuỗi
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    return $this->executeAction($route['action'], $params);
                }
            }
        }

        // Nếu không tìm thấy
        http_response_code(404);
        echo "<div style='font-family: sans-serif; text-align: center; padding: 50px;'>
                <h1 style='font-size: 3rem; color: #e63946;'>404 NOT FOUND</h1>
                <p style='color: #666;'>Đường dẫn <strong>" . htmlspecialchars($path) . "</strong> không tồn tại.</p>
                <a href='" . base_url('/') . "' style='display: inline-block; padding: 10px 20px; background: #111; color: #fff; text-decoration: none; border-radius: 5px;'>Quay lại Trang chủ</a>
              </div>";
        exit;
    }

    private function executeAction(string|array|callable $action, array $params = []): mixed
    {
        if (is_callable($action)) {
            return call_user_func_array($action, $params);
        }

        if (is_array($action)) {
            [$controllerClass, $method] = $action;
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $method)) {
                    return call_user_func_array([$controller, $method], $params);
                }
            }
        }

        throw new \Exception("Route action is invalid.");
    }
}
