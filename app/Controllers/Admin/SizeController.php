<?php

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Models\Size;
use App\Models\Category;

class SizeController extends AdminController
{
    private Size $sizeModel;
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->sizeModel = new Size();
        $this->categoryModel = new Category();
    }

    /**
     * API: Lấy size theo category
     * GET /admin/api/sizes-by-category/{categoryId}
     */
    public function getSizesByCategory($categoryId): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $category = $this->categoryModel->find((int)$categoryId);
            
            if (!$category) {
                echo json_encode(['success' => false, 'error' => 'Category not found']);
                exit;
            }

            // Lấy gender từ category name
            $categoryName = $category['name'];
            $gender = $this->mapCategoryToGender($categoryName);

            // Lấy sizes theo gender
            $sizes = $this->sizeModel->findByGender($gender);

            echo json_encode([
                'success' => true,
                'gender' => $gender,
                'sizes' => $sizes
            ], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    private function mapCategoryToGender(string $categoryName): string
    {
        $categoryLower = mb_strtolower($categoryName, 'UTF-8');
        
        if (str_contains($categoryLower, 'nữ')) {
            return 'Nữ';
        } elseif (str_contains($categoryLower, 'trẻ em') || str_contains($categoryLower, 'trẻem')) {
            return 'Trẻ em';
        } else {
            return 'Nam'; // Default
        }
    }
}
