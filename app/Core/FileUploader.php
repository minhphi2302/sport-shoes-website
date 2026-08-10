<?php

namespace App\Core;

use App\Exceptions\ValidationException;

class FileUploader
{
    public function uploadProductImage(array $file, string $uploadDir): string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new ValidationException('image', 'Có lỗi khi upload file.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($mime, $allowed, true)) {
            throw new ValidationException('image', 'Định dạng ảnh không hợp lệ (chỉ jpg/png/webp)');
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            throw new ValidationException('image', 'Ảnh vượt quá 2MB');
        }

        $ext = match($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        };
        $newName = uniqid('product_', true) . '.' . $ext;
        
        $destination = rtrim($uploadDir, '/') . '/' . $newName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new ValidationException('image', 'Không thể lưu file ảnh.');
        }

        return $newName;
    }
}
