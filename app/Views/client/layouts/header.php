<?php
// Bootstrap already loaded in view page
use App\Core\Auth;

Auth::initSession();

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
    <title><?= htmlspecialchars($title ?? 'ANTA Bán Giày Thể Thao') ?></title>
    <!-- Meta SEO -->
    <meta name="description" content="<?= htmlspecialchars($metaDescription ?? 'Cửa hàng giày thể thao uy tín, chính hãng, giá tốt nhất.') ?>">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Style CSS (ANTA Theme) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=' . time()) ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=' . time()) ?>">
</head>
<body>

    <!-- Top Announcement Bar (ANTA Style) -->
    <div class="top-bar" id="topAnnouncementBar">
        <div class="container position-relative text-center d-flex justify-content-center align-items-center py-1">
            <a href="<?= base_url('products') ?>" class="announcement-text fw-bold">
                <i class="fa-solid fa-truck-fast me-2 text-red"></i> MIỄN PHÍ GIAO HÀNG TOÀN QUỐC CHO ĐƠN HÀNG TỪ 1.000.000VNĐ
            </a>
            <button type="button" class="btn-close-topbar" id="closeTopBarBtn" title="Đóng thông báo">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Close top bar handler
            const closeBtn = document.getElementById('closeTopBarBtn');
            const topBar = document.getElementById('topAnnouncementBar');
            if (closeBtn && topBar) {
                closeBtn.addEventListener('click', function() {
                    topBar.style.display = 'none';
                });
            }

            // Dropdown click handler (Clicking keeps menu open, mouseout will not hide it if clicked)
            const dropdownItems = document.querySelectorAll('.dropdown-hover');
            dropdownItems.forEach(function(item) {
                const link = item.querySelector('.dropdown-toggle');
                const menu = item.querySelector('.custom-dropdown-menu');

                if (link && menu) {
                    link.addEventListener('click', function(e) {
                        // On mobile, prevent navigation and toggle menu
                        if (window.innerWidth < 992) {
                            e.preventDefault();
                            // Close other opened dropdowns
                            document.querySelectorAll('.custom-dropdown-menu.show').forEach(function(m) {
                                if (m !== menu) m.classList.remove('show');
                            });
                            // Toggle current dropdown menu open/close
                            menu.classList.toggle('show');
                        }
                        // On desktop, do not prevent default, allow navigation!
                    });
                }
            });

            // Close dropdowns when clicking anywhere outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown-hover')) {
                    document.querySelectorAll('.custom-dropdown-menu.show').forEach(function(m) {
                        m.classList.remove('show');
                    });
                }
            });
        });
    </script>

    <nav class="navbar navbar-expand-lg navbar-main sticky-top">
        <?php
        // Lấy dữ liệu danh mục và thương hiệu từ DB
        $categoryModel = new \App\Models\Category();
        $categories = $categoryModel->getAllCategories();
        
        $brandModel = new \App\Models\Brand();
        $brands = $brandModel->getAllBrands();
        ?>
        <div class="container">
            <!-- Brand Logo Image -->
            <a class="navbar-brand py-0 me-4 d-flex align-items-center" href="<?= base_url('/') ?>">
                <img src="<?= base_url('image/logo.webp') ?>" alt="Sport Shoes Logo" style="height: 46px; max-height: 48px; width: auto; object-fit: contain;">
            </a>

            <!-- Mobile Toggler -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <i class="fa-solid fa-bars fs-3 text-dark"></i>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <!-- Navigation Links -->
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>" href="<?= base_url('/') ?>">Trang chủ</a>
                    </li>
                    <?php
                        $isProducts = ($currentPage ?? '') === 'products' && empty($_GET['category_id']) && empty($_GET['brand_id']);
                        $isCategory = !empty($_GET['category_id']);
                        $isBrand = !empty($_GET['brand_id']);
                    ?>
                    <li class="nav-item dropdown dropdown-hover">
                        <a class="nav-link nav-link-custom no-red-hover dropdown-toggle <?= $isProducts ? 'active' : '' ?>" href="<?= base_url('products') ?>">
                            Sản phẩm <i class="fa-solid fa-chevron-down ms-1 dropdown-icon"></i>
                        </a>
                        <ul class="dropdown-menu custom-dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('products') ?>">Tất cả sản phẩm</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('products?sort=newest') ?>">Sản phẩm mới nhất</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('products?sort=bestseller') ?>">Bán chạy nhất</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown dropdown-hover">
                        <a class="nav-link nav-link-custom dropdown-toggle <?= $isCategory ? 'active' : '' ?>" href="<?= base_url('products') ?>">
                            Danh mục<i class="fa-solid fa-chevron-down ms-1 dropdown-icon"></i>
                        </a>
                        <ul class="dropdown-menu custom-dropdown-menu">
                            <?php foreach ($categories as $cat): ?>
                                <li><a class="dropdown-item" href="<?= base_url('products?category_id=' . $cat['category_id']) ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item dropdown dropdown-hover">
                        <a class="nav-link nav-link-custom dropdown-toggle <?= $isBrand ? 'active' : '' ?>" href="<?= base_url('products') ?>">
                            Thương hiệu <i class="fa-solid fa-chevron-down ms-1 dropdown-icon"></i>
                        </a>
                        <ul class="dropdown-menu custom-dropdown-menu">
                            <?php foreach ($brands as $brand): ?>
                                <li><a class="dropdown-item" href="<?= base_url('products?brand_id=' . $brand['brand_id']) ?>"><?= htmlspecialchars($brand['name']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="<?= base_url('/#sieu-khuyen-mai') ?>">Khuyến mãi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#contact-footer">Liên hệ</a>
                    </li>
                </ul>
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const navLinks = document.querySelectorAll('.nav-link-custom');
                        
                        function updateActiveNav() {
                            const hash = window.location.hash;
                            if (hash) {
                                navLinks.forEach(link => {
                                    if (link.getAttribute('href') && link.getAttribute('href').includes(hash)) {
                                        navLinks.forEach(l => l.classList.remove('active'));
                                        link.classList.add('active');
                                    }
                                });
                            }
                        }
                        
                        updateActiveNav();
                        window.addEventListener('hashchange', updateActiveNav);
                        
                        navLinks.forEach(link => {
                            link.addEventListener('click', function() {
                                const href = this.getAttribute('href');
                                if (href && href.includes('#')) {
                                    navLinks.forEach(l => l.classList.remove('active'));
                                    this.classList.add('active');
                                }
                            });
                        });
                    });
                </script>

                <!-- Search Form -->
                <form class="d-flex me-3 my-2 my-lg-0 search-box-group" action="<?= base_url('products') ?>" method="GET">
                    <input class="form-control" type="search" name="search" placeholder="Tìm kiếm sản phẩm..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button class="btn btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>

                <!-- Header Actions Icons -->
                <div class="d-flex align-items-center">
                    <a href="<?= base_url('cart') ?>" class="header-icon-link me-2" title="Giỏ hàng">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span class="badge-badge-count"><?= isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0 ?></span>
                    </a>

                    <!-- User Account / Auth Hover Dropdown -->
                    <div class="dropdown dropdown-hover d-inline-block position-relative ms-1">
                        <?php if (isset($_SESSION['user'])): ?>
                            <a href="<?= base_url('account') ?>" class="header-icon-link dropdown-toggle" title="Tài khoản">
                                <i class="fa-regular fa-user"></i>
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('login') ?>" class="header-icon-link dropdown-toggle" title="Tài khoản / Đăng nhập">
                                <i class="fa-regular fa-user"></i>
                            </a>
                        <?php endif; ?>
                        
                        <ul class="dropdown-menu custom-dropdown-menu auth-dropdown-menu">
                            <?php if (isset($_SESSION['user'])): ?>
                                <li class="px-3 py-1 border-bottom mb-1 text-start">
                                    <div class="fw-bold text-dark small"><?= htmlspecialchars($_SESSION['user']['name']) ?></div>
                                </li>
                                <li><a class="dropdown-item text-start" href="<?= base_url('account') ?>">Tài khoản</a></li>
                                <li><a class="dropdown-item text-start text-danger" href="<?= base_url('logout') ?>">Đăng xuất</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="<?= base_url('login') ?>">Đăng nhập</a></li>
                                <li><a class="dropdown-item" href="<?= base_url('register') ?>">Đăng ký</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Toast Notification Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="cartToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    <span id="cartToastMessage"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toastMessage = <?= json_encode($_SESSION['success']) ?>;
                showCartToast(toastMessage);
                updateCartBadge(); // Update badge sau khi hiển thị toast
            });
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
            <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const errorToastEl = document.getElementById('errorToast');
                if (errorToastEl) {
                    const errorToast = new bootstrap.Toast(errorToastEl, { delay: 4000 });
                    errorToast.show();
                }
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <script>
        function showCartToast(message) {
            const toastEl = document.getElementById('cartToast');
            const toastMsgEl = document.getElementById('cartToastMessage');
            if (toastEl && toastMsgEl) {
                toastMsgEl.textContent = message;
                const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
                toast.show();
                
                // Update cart count in header from session
                updateCartBadge();
            }
        }

        function updateCartBadge() {
            const cartCount = document.querySelector('.badge-badge-count');
            if (cartCount) {
                fetch('<?= base_url("cart/count") ?>', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.count !== undefined) {
                        cartCount.textContent = data.count;
                    }
                })
                .catch(() => {
                    // Fallback: reload page if API fails
                    window.location.reload();
                });
            }
        }
    </script>
