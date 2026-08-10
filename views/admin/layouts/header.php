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
    <title>Admin Dashboard - Shop Giày</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; }
        .sidebar { min-height: calc(100vh - 56px); background: #212529; }
        .sidebar .nav-link { color: #c2c7d0; padding: 12px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.1); }
        .product-img-fixed { width: 250px !important; height: 300px !important; object-fit: cover !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= htmlspecialchars($baseUrl) ?>/admin/dashboard">Shop Giày - ADMIN</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= htmlspecialchars($baseUrl) ?>/" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Xem trang khách</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($user['name'] ?? 'Admin') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="<?= htmlspecialchars($baseUrl) ?>/logout" method="POST">
                                <button class="dropdown-item" type="submit">Đăng xuất</button>
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
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : '' ?>" href="<?= htmlspecialchars($baseUrl) ?>/admin/dashboard">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/orders') !== false ? 'active' : '' ?>" href="<?= htmlspecialchars($baseUrl) ?>/admin/orders">
                            <i class="bi bi-cart-check me-2"></i> Quản lý đơn hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/products') !== false ? 'active' : '' ?>" href="<?= htmlspecialchars($baseUrl) ?>/admin/products">
                            <i class="bi bi-box-seam me-2"></i> Quản lý sản phẩm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/categories') !== false ? 'active' : '' ?>" href="<?= htmlspecialchars($baseUrl) ?>/admin/categories">
                            <i class="bi bi-tags me-2"></i> Quản lý danh mục
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/brands') !== false ? 'active' : '' ?>" href="<?= htmlspecialchars($baseUrl) ?>/admin/brands">
                            <i class="bi bi-award me-2"></i> Quản lý thương hiệu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/attributes') !== false ? 'active' : '' ?>" href="<?= htmlspecialchars($baseUrl) ?>/admin/attributes">
                            <i class="bi bi-ui-checks-grid me-2"></i> Quản lý thuộc tính (Size/Màu)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/customers') !== false ? 'active' : '' ?>" href="<?= htmlspecialchars($baseUrl) ?>/admin/customers">
                            <i class="bi bi-people me-2"></i> Quản lý khách hàng
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4 pb-5">
