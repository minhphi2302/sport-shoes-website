<?php

if (!defined('COD_FEE')) {
    define('COD_FEE', 30000); // Phí COD 30.000đ khi đơn dưới ngưỡng
}

if (!defined('FREE_COD_THRESHOLD')) {
    define('FREE_COD_THRESHOLD', 1000000); // Mốc 1.000.000đ để được Miễn phí COD
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string {
        $baseUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
        $path = ltrim($path, '/');
        return $path !== '' ? $baseUrl . '/' . $path : $baseUrl;
    }
}
