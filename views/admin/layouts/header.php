<?php
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

use App\Core\Auth;
$user = Auth::user();
$baseUrl = $_ENV['APP_URL'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANTA Store - Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons & FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Main Black & Red Theme CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
    <style>
        body { font-family: 'Poppins', 'Outfit', sans-serif; background-color: #f4f6f9; }
        .sidebar { min-height: calc(100vh - 62px); background: #111111; }
        .sidebar .nav-link { color: #cccccc; padding: 12px 20px; font-weight: 500; transition: all 0.2s ease; }
        .sidebar .nav-link:hover { color: #ffffff !important; background: rgba(230, 0, 18, 0.25) !important; border-radius: 8px; }
        .sidebar .nav-link.active { color: #ffffff !important; background-color: #e60012 !important; border-radius: 8px; font-weight: 700; }
        .product-img-fixed { width: 250px !important; height: 300px !important; object-fit: cover !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm py-2 border-bottom">
    <div class="container-fluid">
        <a class="navbar-brand py-0 me-4 d-flex align-items-center" href="<?= base_url('admin/dashboard') ?>">
            <img src="<?= base_url('image/logo.webp') ?>" alt="ANTA Store Logo" style="height: 42px; width: auto; object-fit: contain;">
            <span class="badge bg-danger ms-2 px-2 py-1 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 1px;">ADMIN PANEL</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-3">
                    <a class="nav-link text-dark fw-semibold" href="<?= base_url('/') ?>" target="_blank"><i class="bi bi-box-arrow-up-right me-1 text-danger"></i> Xem trang khách</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark fw-bold d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5 me-1 text-danger"></i> <?= htmlspecialchars($user['name'] ?? 'Admin') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                        <li>
                            <form action="<?= base_url('logout') ?>" method="POST">
                                <button class="dropdown-item text-danger fw-semibold" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-2">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column gap-1">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/orders') !== false ? 'active' : '' ?>" href="<?= base_url('admin/orders') ?>">
                            <i class="bi bi-cart-check me-2"></i> Quản lý đơn hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/products') !== false ? 'active' : '' ?>" href="<?= base_url('admin/products') ?>">
                            <i class="bi bi-box-seam me-2"></i> Quản lý sản phẩm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/categories') !== false ? 'active' : '' ?>" href="<?= base_url('admin/categories') ?>">
                            <i class="bi bi-tags me-2"></i> Quản lý danh mục
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/brands') !== false ? 'active' : '' ?>" href="<?= base_url('admin/brands') ?>">
                            <i class="bi bi-award me-2"></i> Quản lý thương hiệu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/attributes') !== false ? 'active' : '' ?>" href="<?= base_url('admin/attributes') ?>">
                            <i class="bi bi-ui-checks-grid me-2"></i> Quản lý thuộc tính (Size/Màu)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/customers') !== false ? 'active' : '' ?>" href="<?= base_url('admin/customers') ?>">
                            <i class="bi bi-people me-2"></i> Quản lý khách hàng
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4 pb-5">
