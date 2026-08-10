<?php

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Models\Size;
use App\Models\Color;
use App\Exceptions\ValidationException;

class AttributeAdminController extends AdminController
{
    private Size $sizeModel;
    private Color $colorModel;

    public function __construct()
    {
        parent::__construct();
        $this->sizeModel = new Size();
        $this->colorModel = new Color();
    }

    public function index(): void
    {
        $sizes = $this->sizeModel->all();
        $colors = $this->colorModel->all();

        $this->view('admin/attributes', [
            'sizes' => $sizes,
            'colors' => $colors
        ]);
    }

    public function storeSize(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'name' => trim($_POST['name'] ?? ''),
                    'gender' => trim($_POST['gender'] ?? 'Nam')
                ];

                if (empty($data['name'])) {
                    throw new ValidationException('name', 'Vui lòng nhập tên Size');
                }

                $this->sizeModel->create($data);
                $_SESSION['success'] = 'Thêm Size thành công';
            } catch (ValidationException $e) {
                $_SESSION['error'] = $e->getMessage();
            } catch (\Exception $e) {
                $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
            }
        }
        $this->redirect('/admin/attributes');
    }

    public function deleteSize($id): void
    {
        try {
            $this->sizeModel->delete((int)$id);
            $_SESSION['success'] = 'Xóa Size thành công';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa';
        }
        $this->redirect('/admin/attributes');
    }

    public function storeColor(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'name' => trim($_POST['name'] ?? '')
                ];

                if (empty($data['name'])) {
                    throw new ValidationException('name', 'Vui lòng nhập màu sắc');
                }

                $this->colorModel->create($data);
                $_SESSION['success'] = 'Thêm Màu sắc thành công';
            } catch (ValidationException $e) {
                $_SESSION['error'] = $e->getMessage();
            } catch (\Exception $e) {
                $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
            }
        }
        $this->redirect('/admin/attributes');
    }

    public function deleteColor($id): void
    {
        try {
            $this->colorModel->delete((int)$id);
            $_SESSION['success'] = 'Xóa Màu sắc thành công';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa';
        }
        $this->redirect('/admin/attributes');
    }
}
