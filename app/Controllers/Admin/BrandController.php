<?php

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Models\Brand;
use App\Exceptions\CannotDeleteException;
use App\Exceptions\ValidationException;

class BrandController extends AdminController
{
    private Brand $brandModel;

    public function __construct()
    {
        parent::__construct();
        $this->brandModel = new Brand();
    }

    public function index(): void
    {
        $brands = $this->brandModel->all();
        $this->view('admin/brand_list', ['brands' => $brands]);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $_SESSION['error'] = 'Tên thương hiệu không được để trống';
                $this->redirect('/admin/brands/create');
            }

            try {
                $this->brandModel->create([
                    'name' => $name,
                    'description' => trim($_POST['description'] ?? '')
                ]);
                $_SESSION['success'] = 'Thêm thương hiệu thành công';
                $this->redirect('/admin/brands');
            } catch (ValidationException $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('/admin/brands/create');
            }
        }

        $this->view('admin/brand_form');
    }

    public function edit($id): void
    {
        $brandId = (int)$id;
        $brand = $this->brandModel->find($brandId);

        if (!$brand) {
            $_SESSION['error'] = 'Thương hiệu không tồn tại';
            $this->redirect('/admin/brands');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $_SESSION['error'] = 'Tên thương hiệu không được để trống';
                $this->redirect("/admin/brands/{$brandId}/edit");
            }

            try {
                $this->brandModel->update($brandId, [
                    'name' => $name,
                    'description' => trim($_POST['description'] ?? '')
                ]);
                $_SESSION['success'] = 'Cập nhật thương hiệu thành công';
                $this->redirect('/admin/brands');
            } catch (ValidationException $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect("/admin/brands/{$brandId}/edit");
            }
        }

        $this->view('admin/brand_form', ['brand' => $brand]);
    }

    public function delete($id): void
    {
        $brandId = (int)$id;
        try {
            $this->brandModel->delete($brandId);
            $_SESSION['success'] = 'Xóa thương hiệu thành công';
        } catch (CannotDeleteException $e) {
            $_SESSION['error'] = $e->getMessage();
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa';
        }
        $this->redirect('/admin/brands');
    }
}
