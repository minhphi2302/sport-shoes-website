<?php

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Models\Brand;
use App\Exceptions\CannotDeleteException;
use App\Exceptions\ValidationException;
use App\Core\FileUploader;

class BrandController extends AdminController
{
    private Brand $brandModel;
    private FileUploader $fileUploader;

    public function __construct()
    {
        parent::__construct();
        $this->brandModel = new Brand();
        $this->fileUploader = new FileUploader();
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
                $data = [
                    'name' => $name,
                    'description' => trim($_POST['description'] ?? '')
                ];
                
                if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $uploadDir = __DIR__ . '/../../../public/uploads/brands';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $data['image_url'] = 'brands/' . $this->fileUploader->uploadImage($_FILES['image'], $uploadDir, 'brand_');
                }

                $this->brandModel->create($data);
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
                $data = [
                    'name' => $name,
                    'description' => trim($_POST['description'] ?? ''),
                    'image_url' => $brand['image_url'] ?? null
                ];

                if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $uploadDir = __DIR__ . '/../../../public/uploads/brands';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $data['image_url'] = 'brands/' . $this->fileUploader->uploadImage($_FILES['image'], $uploadDir, 'brand_');
                    
                    if (!empty($brand['image_url'])) {
                        $oldPath = __DIR__ . '/../../../public/uploads/' . $brand['image_url'];
                        if (file_exists($oldPath)) @unlink($oldPath);
                    }
                }

                $this->brandModel->update($brandId, $data);
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
