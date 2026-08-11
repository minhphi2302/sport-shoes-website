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

if (!function_exists('get_product_image_url')) {
    function get_product_image_url(?string $imgUrl, int $productId = 1): string {
        if (!empty($imgUrl)) {
            $imgUrl = ltrim($imgUrl, '/');
            if (str_starts_with($imgUrl, 'uploads/')) {
                return base_url($imgUrl);
            }
            if (str_starts_with($imgUrl, 'products/')) {
                return base_url('uploads/' . $imgUrl);
            }
            if (file_exists(dirname(__DIR__) . '/public/uploads/' . $imgUrl)) {
                return base_url('uploads/' . $imgUrl);
            }
            return base_url('uploads/' . $imgUrl);
        }
        $slideNum = (($productId % 3) + 1);
        return base_url('image/slide/slide' . $slideNum . '.avif');
    }
}
