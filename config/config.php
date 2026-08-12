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

if (!function_exists('get_brand_image_url')) {
    /**
     * Lấy đường dẫn ảnh thương hiệu
     * Ảnh brand nằm trong public/image/brand/
     */
    function get_brand_image_url(?string $logoUrl, string $brandName): string {
        // Nếu có logo_url trong DB
        if (!empty($logoUrl)) {
            $logoUrl = ltrim($logoUrl, '/');
            
            // Nếu đã có prefix image/ hoặc public/image/
            if (str_starts_with($logoUrl, 'image/brand/')) {
                return base_url($logoUrl);
            }
            if (str_starts_with($logoUrl, 'public/image/brand/')) {
                return base_url(str_replace('public/', '', $logoUrl));
            }
            
            // Nếu chỉ có tên file (vd: nike.jpg)
            if (strpos($logoUrl, '/') === false) {
                return base_url('image/brand/' . $logoUrl);
            }
            
            // Fallback: trả về như cũ
            return base_url($logoUrl);
        }
        
        // Tự động tìm file theo tên brand
        $brandSlug = strtolower(str_replace(' ', '', $brandName));
        $basePath = dirname(__DIR__) . '/public/image/brand/';
        
        // Thử các extension
        $extensions = ['jpg', 'png', 'webp', 'jpeg'];
        foreach ($extensions as $ext) {
            if (file_exists($basePath . $brandSlug . '.' . $ext)) {
                return base_url('image/brand/' . $brandSlug . '.' . $ext);
            }
        }
        
        // Fallback: logo ANTA mặc định
        return base_url('image/logo.webp');
    }
}
