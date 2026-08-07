<?php

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Models\Category;
use App\Exceptions\CannotDeleteException;

class CategoryController extends AdminController
{
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new Category();
    }

    public function index(): void
    {
        $categories = $this->categoryModel->all();
        $this->view('admin/category_list', ['categories' => $categories]);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $_SESSION['error'] = 'Tên danh mục không được để trống';
                $this->redirect('/admin/categories/create');
            }

            $this->categoryModel->create([
                'name' => $name,
                'description' => trim($_POST['description'] ?? '')
            ]);
            $_SESSION['success'] = 'Thêm danh mục thành công';
            $this->redirect('/admin/categories');
        }

        $this->view('admin/category_form');
    }

    public function edit($id): void
    {
        $categoryId = (int)$id;
        $category = $this->categoryModel->find($categoryId);

        if (!$category) {
            $_SESSION['error'] = 'Danh mục không tồn tại';
            $this->redirect('/admin/categories');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $_SESSION['error'] = 'Tên danh mục không được để trống';
                $this->redirect("/admin/categories/{$categoryId}/edit");
            }

            $this->categoryModel->update($categoryId, [
                'name' => $name,
                'description' => trim($_POST['description'] ?? '')
            ]);
            $_SESSION['success'] = 'Cập nhật danh mục thành công';
            $this->redirect('/admin/categories');
        }

        $this->view('admin/category_form', ['category' => $category]);
    }

    public function delete($id): void
    {
        $categoryId = (int)$id;
        try {
            $this->categoryModel->delete($categoryId);
            $_SESSION['success'] = 'Xóa danh mục thành công';
        } catch (CannotDeleteException $e) {
            $_SESSION['error'] = $e->getMessage();
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa';
        }
        $this->redirect('/admin/categories');
    }
}
