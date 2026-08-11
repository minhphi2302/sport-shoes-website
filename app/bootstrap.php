<?php

if (!class_exists('App\Core\Auth')) {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $base_dir = __DIR__ . '/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    });

    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        \App\Core\Env::load($envFile);
    }
}

if (!function_exists('base_url')) {
    function base_url($path = '') {
        return rtrim($_ENV['APP_URL'] ?? '', '/') . '/' . ltrim($path, '/');
    }
}