<?php
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

use App\Core\Auth;
$isLoggedIn = Auth::check();
$user = Auth::user();
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
$baseUrl = $_ENV['APP_URL'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Shop Giày Thể Thao' ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Style CSS -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/css/style.css">
</head>
<body>

<!-- Topbar Header -->
<div class="bg-dark text-white py-1 small border-bottom border-secondary d-none d-md-block">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <span class="me-3"><i class="bi bi-telephone-fill me-1 text-warning"></i> Hotline: 1900 6789</span>
            <span><i class="bi bi-envelope-fill me-1 text-warning"></i> Email: support@sportshoes.vn</span>
        </div>
        <div>
            <span class="me-3"><i class="bi bi-truck me-1 text-success"></i> Miễn phí giao hàng cho đơn từ 1.000.000đ</span>
        </div>
    </div>
</div>

<!-- Main Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-2">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 text-uppercase text-primary d-flex align-items-center" href="<?= htmlspecialchars($baseUrl) ?>/">
            <i class="bi bi-lightning-charge-fill me-1 text-warning"></i> ANTA STORE
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?= htmlspecialchars($baseUrl) ?>/">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?= htmlspecialchars($baseUrl) ?>/products">Sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?= htmlspecialchars($baseUrl) ?>/products?sort=newest">Mới nhất</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 text-danger" href="<?= htmlspecialchars($baseUrl) ?>/products?sale=1"><i class="bi bi-fire me-1"></i> Khuyến mãi</a>
                </li>
            </ul>
            
            <!-- Search Form -->
            <form class="d-flex me-3 my-2 my-lg-0" action="<?= htmlspecialchars($baseUrl) ?>/products" method="GET">
                <div class="input-group">
                    <input class="form-control rounded-start-pill border-end-0 border ps-3" type="search" name="search" placeholder="Tìm giày thể thao..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <button class="btn btn-outline-primary rounded-end-pill px-3 border-start-0 border" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
                <?php if(!$isLoggedIn || (isset($user['role']) && $user['role'] !== 'admin')): ?>
                <li class="nav-item me-3 position-relative">
                    <a class="nav-link text-dark fs-5 position-relative" href="<?= htmlspecialchars($baseUrl) ?>/cart" title="Giỏ hàng">
                        <i class="bi bi-bag-shopping"></i>
                        <?php if($cartCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem;">
                            <?= $cartCount ?>
                        </span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endif; ?>

                <?php if($isLoggedIn): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5 me-1 text-primary"></i> <?= htmlspecialchars($user['name'] ?? 'User') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3" aria-labelledby="userDropdown">
                            <?php if(isset($user['role']) && $user['role'] === 'admin'): ?>
                                <li><a class="dropdown-item fw-semibold text-primary" href="<?= htmlspecialchars($baseUrl) ?>/admin/dashboard"><i class="bi bi-speedometer2 me-2"></i> Admin Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= htmlspecialchars($baseUrl) ?>/profile"><i class="bi bi-person me-2"></i> Tài khoản của tôi</a></li>
                            <li><a class="dropdown-item" href="<?= htmlspecialchars($baseUrl) ?>/orders"><i class="bi bi-receipt me-2"></i> Đơn hàng của tôi</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="<?= htmlspecialchars($baseUrl) ?>/logout" method="POST">
                                    <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-primary btn-sm rounded-pill px-3" href="<?= htmlspecialchars($baseUrl) ?>/login">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm rounded-pill px-3" href="<?= htmlspecialchars($baseUrl) ?>/register">Đăng ký</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content wrapper -->
<main class="min-vh-100 pb-5">
    <?php if (isset($_SESSION['success']) || isset($_SESSION['error'])): ?>
    <div class="container mt-3">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
