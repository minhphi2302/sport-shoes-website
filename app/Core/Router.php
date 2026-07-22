<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string|array $action): void
    {
        $this->addRoute('GET', $path, $action);
    }

    public function post(string $path, string|array $action): void
    {
        $this->addRoute('POST', $path, $action);
    }

    private function addRoute(string $method, string $path, string|array $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'action' => $action
        ];
    }

    public function resolve(string $uri, string $method): mixed
    {
        // Lấy đường dẫn bỏ qua query string
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Cắt bỏ phần subfolder nếu có (VD: /sport-shoes-website/public)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && str_starts_with($path, $scriptName)) {
            $path = substr($path, strlen($scriptName));
        }
        
        if ($path === '' || $path === false) {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                // Chuyển /product/{id} thành regex
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $route['path']);
                $pattern = "#^" . $pattern . "$#";

                if (preg_match($pattern, $path, $matches)) {
                    // Lọc lấy các tham số là chuỗi (được đặt tên bằng {id})
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                    return $this->executeAction($route['action'], $params);
                }
            }
        }

        // Nếu không tìm thấy
        http_response_code(404);
        echo "404 Not Found";
        exit;
    }

    private function executeAction(string|array $action, array $params = []): mixed
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
