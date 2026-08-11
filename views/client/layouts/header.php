<?php
// Lùi 3 cấp để gọi bootstrap.php
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

use App\Core\Auth;
$isLoggedIn = Auth::check();
$user = Auth::user();
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$baseUrl = $_ENV['APP_URL'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Shop Giày' ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #0d6efd !important;
        }
        .nav-link {
            font-weight: 500;
        }
        .cart-badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(50%, -50%);
        }
        /* Product Card Styles */
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .product-card img, .product-img-fixed {
            width: 250px !important;
            height: 300px !important;
            object-fit: cover !important;
            margin: 0 auto;
            display: block;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .price-sale {
            color: #dc3545;
            font-weight: 700;
            font-size: 1.2rem;
        }
        .price-original {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .price-regular {
            color: #0d6efd;
            font-weight: 700;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= htmlspecialchars($baseUrl) ?>/">
            <i class="bi bi-lightning-fill"></i> Shop Giày
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= htmlspecialchars($baseUrl) ?>/">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= htmlspecialchars($baseUrl) ?>/products">Sản phẩm</a>
                </li>
            </ul>
            
            <form class="d-flex me-3" action="<?= htmlspecialchars($baseUrl) ?>/products" method="GET">
                <div class="input-group">
                    <input class="form-control border-end-0 border" type="search" name="search" placeholder="Tìm giày..." aria-label="Search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <span class="input-group-append">
                        <button class="btn btn-outline-secondary bg-white border-start-0 border ms-n5" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </span>
                </div>
            </form>

            <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
                <?php if(!$isLoggedIn || (isset($user['role']) && $user['role'] !== 'admin')): ?>
                <li class="nav-item me-3 position-relative">
                    <a class="nav-link" href="<?= htmlspecialchars($baseUrl) ?>/cart">
                        <i class="bi bi-cart3 fs-5"></i>
                        <?php if($cartCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">
                            <?= $cartCount ?>
                        </span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if($isLoggedIn): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($user['name'] ?? 'User') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <?php if(isset($user['role']) && $user['role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?= htmlspecialchars($baseUrl) ?>/admin/dashboard">Admin Dashboard</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= htmlspecialchars($baseUrl) ?>/profile">Tài khoản</a></li>
                            <li><a class="dropdown-item" href="<?= htmlspecialchars($baseUrl) ?>/orders">Đơn hàng</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="<?= htmlspecialchars($baseUrl) ?>/logout" method="POST">
                                    <button class="dropdown-item text-danger" type="submit">Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary me-2" href="<?= htmlspecialchars($baseUrl) ?>/login">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="<?= htmlspecialchars($baseUrl) ?>/register">Đăng ký</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content wrapper -->
<main class="min-vh-100">
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
