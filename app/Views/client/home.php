<?php
require __DIR__ . '/layouts/header.php';
?>

<!-- 1. HERO BANNER SLIDER -->
<div id="heroCarousel" class="carousel slide hero-slider" data-bs-ride="carousel" data-bs-interval="1000">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <!-- Slide 1 -->
        <div class="carousel-item active">
            <img src="<?= base_url('image/slide/slide1.avif') ?>" class="hero-slider-img" alt="Nike Air Zoom">
            <div class="carousel-caption carousel-caption-custom container">
                <span class="badge bg-red mb-2 px-3 py-2 text-uppercase">Bộ sưu tập mới 2026</span>
                <h1>NIKE AIR ZOOM PEGASUS 39</h1>
                <p>Êm ái vượt trội - Tối ưu từng bước chạy của bạn.</p>
                <div>
                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-red me-3"><i class="fa-solid fa-cart-shopping me-2"></i> Mua Ngay</a>
                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products?brand_id=1" class="btn btn-outline-light"><i class="fa-solid fa-eye me-2"></i> Xem Chi Tiết</a>
                </div>
            </div>
        </div>
        <!-- Slide 2 -->
        <div class="carousel-item">
            <img src="<?= base_url('image/slide/slide2.avif') ?>" class="hero-slider-img" alt="Adidas Ultraboost">
            <div class="carousel-caption carousel-caption-custom container">
                <span class="badge bg-danger mb-2 px-3 py-2 text-uppercase">Giảm Giá 30%</span>
                <h1>ADIDAS ULTRABOOST 22</h1>
                <p>Công nghệ đế Boost hoàn trả năng lượng tối đa.</p>
                <div>
                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-red me-3"><i class="fa-solid fa-bolt me-2"></i> Khám Phá</a>
                </div>
            </div>
        </div>
        <!-- Slide 3 -->
        <div class="carousel-item">
            <img src="<?= base_url('image/slide/slide3.avif') ?>" class="hero-slider-img" alt="Puma Shoes">
            <div class="carousel-caption carousel-caption-custom container">
                <span class="badge bg-warning text-dark mb-2 px-3 py-2 text-uppercase">Đẳng cấp phong cách</span>
                <h1>PUMA & CONVERSE 2026</h1>
                <p>Phong cách trẻ trung, năng động và sành điệu nhất.</p>
                <div>
                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-red me-3">Xem Tất Cả</a>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<script>
    // Khởi tạo carousel tự động chạy
    document.addEventListener('DOMContentLoaded', function () {
        var carouselEl = document.getElementById('heroCarousel');
        if (carouselEl) {
            var carousel = new bootstrap.Carousel(carouselEl, {
                interval: 4000,
                ride: 'carousel',
                wrap: true
            });
            carousel.cycle();
        }
    });
</script>

<!-- 2. THƯƠNG HIỆU NỔI BẬT -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center text-md-start">Thương hiệu</h2>
        <div class="row g-4">
            <?php if (!empty($brands)): ?>
                <?php foreach ($brands as $brand): ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="<?= base_url('products?brand_id=' . $brand['brand_id']) ?>" class="brand-card">
                            <?php 
                                $logo = !empty($brand['logo_url']) ? trim($brand['logo_url']) : '';
                                // Nếu người dùng chỉ nhập tên file (ví dụ: nike.jpg) thay vì đường dẫn đầy đủ
                                if (!empty($logo) && strpos($logo, '/') === false) {
                                    $logo = 'public/image/brand/' . $logo;
                                }
                                
                                if (empty($logo)) {
                                    $brandSlug = strtolower(str_replace(' ', '', $brand['name']));
                                    $jpgPath = 'public/image/brand/' . $brandSlug . '.jpg';
                                    $pngPath = 'public/image/brand/' . $brandSlug . '.png';
                                    if (file_exists(__DIR__ . '/../../../' . $jpgPath)) {
                                        $logo = $jpgPath;
                                    } else {
                                        $logo = $pngPath; // Default fallback to png
                                    }
                                }
                            ?>
                            <img src="<?= base_url(htmlspecialchars($logo)) ?>" alt="<?= htmlspecialchars($brand['name']) ?>" class="brand-logo-img">
                            <h6 class="brand-name-text"><?= htmlspecialchars(mb_strtoupper($brand['name'])) ?></h6>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="<?= base_url('products') ?>" class="brand-card bg-dark text-white">
                    <i class="fa-solid fa-arrow-right text-red fs-4 mb-2"></i>
                    <h6 class="brand-name-text text-white">XEM TẤT CẢ</h6>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 3. CHÍNH SÁCH CỬA HÀNG -->
<section class="py-4 border-bottom border-top">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="policy-box">
                    <i class="fa-solid fa-truck-fast"></i>
                    <h5>Giao hàng nhanh</h5>
                    <p>Giao toàn quốc từ 1-3 ngày</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="policy-box">
                    <i class="fa-solid fa-rotate-left"></i>
                    <h5>Đổi trả 7 ngày</h5>
                    <p>Miễn phí đổi size & mẫu</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="policy-box">
                    <i class="fa-solid fa-shield-halved"></i>
                    <h5>Thanh toán an toàn</h5>
                    <p>100% Bảo mật thông tin</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="policy-box">
                    <i class="fa-solid fa-headset"></i>
                    <h5>Hỗ trợ 24/7</h5>
                    <p>Tư vấn nhiệt tình tận tâm</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" id="sieu-khuyen-mai">
    <div class="container position-relative">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
            <h2 class="section-title mb-3 mb-lg-0 text-uppercase fw-bold" style="font-size: 1.5rem;"><i class="fa-solid fa-bolt me-2 text-warning"></i>SIÊU KHUYẾN MÃI</h2>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products?sale=1" class="btn btn-brand-filter bg-transparent border btn-sm px-3 py-2 text-dark fw-semibold text-uppercase" style="font-size: 0.85rem;">XEM TẤT CẢ</a>
            </div>
        </div>
        
        <div id="promoCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner pb-3 px-lg-5 px-md-4 px-2">
                <?php if (!empty($saleProducts)): ?>
                    <?php 
                        $chunks = array_chunk($saleProducts, 4); 
                        foreach ($chunks as $index => $chunk): 
                    ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <div class="row g-3">
                            <?php foreach ($chunk as $product): ?>
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="card h-100 rounded-1 border promo-card overflow-hidden bg-white product-card">
                                        <?php 
                                            $discountPercent = round((($product['price'] - $product['sale_price']) / $product['price']) * 100); 
                                        ?>
                                        <div class="badge-ribbon badge-ribbon-red"><small>-</small><?= $discountPercent ?>%</div>
                                        <?php 
                                            $fallbackImg = 'image/slide/slide' . (($product['product_id'] % 3) + 1) . '.avif';
                                            $imgSrc = !empty($product['image_url']) ? $product['image_url'] : $fallbackImg;
                                        ?>
                                        <div class="position-relative">
                                            <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $product['product_id'] ?>" class="text-decoration-none d-block">
                                                <div style="background-color: #f8f9fa;" class="text-center p-2">
                                                    <img src="<?= htmlspecialchars(base_url($imgSrc)) ?>" class="img-fluid" style="height: 190px; object-fit: contain; width: 100%;" alt="<?= htmlspecialchars($product['name']) ?>">
                                                </div>
                                            </a>
                                            <div class="product-center-action">
                                                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $product['product_id'] ?>" class="search-icon-btn" title="Xem chi tiết"><i class="fa-solid fa-magnifying-glass"></i></a>
                                            </div>
                                            <?php if (($product['quantity'] ?? 50) > 0): ?>
                                            <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/cart/add/<?= $product['product_id'] ?>" class="btn-cart-hover position-absolute" style="bottom: 10px; right: 10px;" title="Thêm vào giỏ">
                                                <span class="cart-text">Thêm vào giỏ</span>
                                                <span class="cart-icon-wrapper">
                                                <svg class="custom-bag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                                                    <path d="M9 6V4a3 3 0 0 1 6 0v2"></path>
                                                    <rect x="4" y="6" width="16" height="14" rx="2"></rect>
                                                    <circle cx="18" cy="18" r="5" class="bag-bg-circle" stroke="none"></circle>
                                                    <circle cx="18" cy="18" r="4"></circle>
                                                    <path d="M18 15v6m-3-3h6"></path>
                                                </svg>
                                                </span>
                                            </a>
                                            <?php else: ?>
                                            <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/cart/add/<?= $product['product_id'] ?>" class="btn-cart-hover position-absolute btn-out-of-stock" style="bottom: 10px; right: 10px;" title="Tạm hết hàng">
                                                <span class="cart-text">Tạm hết hàng</span>
                                                <span class="cart-icon-wrapper">
                                                <svg class="custom-bag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                                                    <path d="M9 6V4a3 3 0 0 1 6 0v2"></path>
                                                    <rect x="4" y="6" width="16" height="14" rx="2"></rect>
                                                    <circle cx="18" cy="18" r="5" class="bag-bg-circle" stroke="none"></circle>
                                                    <circle cx="18" cy="18" r="4"></circle>
                                                    <path d="M18 15v6m-3-3h6"></path>
                                                </svg>
                                                </span>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body px-2 py-3 text-start">
                                            <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $product['product_id'] ?>" class="text-dark text-decoration-none fw-semibold d-block mb-1" style="font-size: 0.95rem; line-height: 1.4;"><?= htmlspecialchars($product['name']) ?></a>
                                                <?php if (!empty($product['sku'])): ?> 
                                                <div class="text-muted mb-2" style="font-size: 0.85rem;"><?= htmlspecialchars($product['sku']) ?></div>
                                            <?php endif; ?> 
                                            <div class="d-flex justify-content-start align-items-baseline gap-2 flex-wrap">
                                                <span class="text-danger fw-bold" style="font-size: 1.1rem;"><?= number_format($product['sale_price'], 0, ',', '.') ?>đ</span>
                                                <span class="text-muted text-decoration-line-through" style="font-size: 0.85rem;"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                                <?php if (isset($product['quantity']) && $product['quantity'] <= 0): ?>
                                                    <span class="badge bg-danger rounded-1 ms-2 p-1 px-2 fw-medium" style="font-size: 0.75rem;">Hết hàng</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="mb-0 fs-5 text-muted">Hiện chưa có sản phẩm khuyến mãi nào.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($saleProducts) && count($saleProducts) > 4): ?>
                <!-- Carousel dots -->
                <div class="carousel-indicators position-static mt-3 mb-0">
                    <?php foreach ($chunks as $index => $chunk): ?>
                        <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>"></button>
                    <?php endforeach; ?>
                </div>

                <!-- Carousel arrows (custom style like image) -->
                <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev" style="width: auto; left: 0; z-index: 5;">
                    <span class="promo-nav-btn rounded-1" aria-hidden="true">
                        <span class="arrow-icon prev-icon"></span>
                    </span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next" style="width: auto; right: 0; z-index: 5;">
                    <span class="promo-nav-btn rounded-1" aria-hidden="true">
                        <span class="arrow-icon next-icon"></span>
                    </span>
                </button>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 4. SẢN PHẨM NỔI BẬT & BÁN CHẠY -->
<section class="py-5">
    <div class="container">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
            <h2 class="section-title mb-3 mb-lg-0 text-uppercase fw-bold" style="font-size: 1.5rem;">GIÀY SNEAKER</h2>
            <div class="d-flex flex-wrap gap-2" id="home-brand-filters">
                <?php if (!empty($brands)): ?>
                    <?php foreach ($brands as $b): ?>
                        <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products?brand_id=<?= $b['brand_id'] ?>" class="btn btn-brand-filter bg-transparent border btn-sm px-3 py-2 text-dark fw-semibold text-uppercase" style="font-size: 0.85rem;" data-brand-id="<?= $b['brand_id'] ?>">GIÀY <?= htmlspecialchars($b['name']) ?></a>
                    <?php endforeach; ?>
                <?php endif; ?>
                <a href="#" class="btn btn-brand-filter bg-transparent border btn-sm px-3 py-2 text-dark fw-semibold text-uppercase active" style="font-size: 0.85rem;" data-brand-id="">TẤT CẢ</a>
            </div>
        </div>

        <div id="home-product-container">
        <div class="row g-4">
            <?php if (!empty($featuredProducts)): ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card">
                            <div class="product-img-wrapper">
                                <?php 
                                    $isNew = isset($product['created_at']) && (strtotime($product['created_at']) > strtotime('-7 days'));
                                    $hasSale = (($product['sale_price'] ?? 0) > 0 && $product['sale_price'] < $product['price']);
                                ?>
                                <?php if ($isNew): ?>
                                    <div class="badge-ribbon badge-ribbon-black">NEW</div>
                                <?php endif; ?>
                                <?php if ($hasSale): ?>
                                    <?php 
                                        $discountPercent = round((($product['price'] - $product['sale_price']) / $product['price']) * 100); 
                                    ?>
                                    <div class="badge-ribbon badge-ribbon-red" <?= $isNew ? 'style="left: 43px;"' : '' ?>><small>-</small><?= $discountPercent ?>%</div>
                                <?php endif; ?>

                                <?php 
                                    $fallbackImg = 'image/slide/slide' . (($product['product_id'] % 3) + 1) . '.avif';
                                    $imgSrc = !empty($product['image_url']) ? $product['image_url'] : $fallbackImg;
                                ?>
                                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $product['product_id'] ?>">
                                    <img src="<?= htmlspecialchars(base_url($imgSrc)) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                </a>
                                <div class="product-center-action">
                                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $product['product_id'] ?>" class="search-icon-btn" title="Xem chi tiết"><i class="fa-solid fa-magnifying-glass"></i></a>
                                </div>
                                <?php if (($product['quantity'] ?? 50) > 0): ?>
                                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/cart/add/<?= $product['product_id'] ?>" class="btn-cart-hover" title="Thêm vào giỏ">
                                        <span class="cart-text">Thêm vào giỏ</span>
                                        <span class="cart-icon-wrapper">
                                            <svg class="custom-bag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                                                <path d="M9 6V4a3 3 0 0 1 6 0v2"></path>
                                                <rect x="4" y="6" width="16" height="14" rx="2"></rect>
                                                <circle cx="18" cy="18" r="5" class="bag-bg-circle" stroke="none"></circle>
                                                <circle cx="18" cy="18" r="4"></circle>
                                                <path d="M18 15v6m-3-3h6"></path>
                                            </svg>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/cart/add/<?= $product['product_id'] ?>" class="btn-cart-hover btn-out-of-stock" title="Tạm hết hàng">
                                        <span class="cart-text">Tạm hết hàng</span>
                                        <span class="cart-icon-wrapper">
                                            <svg class="custom-bag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                                                <path d="M9 6V4a3 3 0 0 1 6 0v2"></path>
                                                <rect x="4" y="6" width="16" height="14" rx="2"></rect>
                                                <circle cx="18" cy="18" r="5" class="bag-bg-circle" stroke="none"></circle>
                                                <circle cx="18" cy="18" r="4"></circle>
                                                <path d="M18 15v6m-3-3h6"></path>
                                            </svg>
                                        </span>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <div class="product-body">
                                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $product['product_id'] ?>" class="product-title mb-1"><?= htmlspecialchars($product['name']) ?></a>
                                <?php if (!empty($product['sku'])): ?>
                                    <div class="text-muted mb-2" style="font-size: 0.85rem;"><?= htmlspecialchars($product['sku']) ?></div>
                                <?php endif; ?>
                                <div class="product-price-box">
                                    <?php if (($product['sale_price'] ?? 0) > 0 && $product['sale_price'] < $product['price']): ?>
                                        <span class="product-price"><?= number_format($product['sale_price'], 0, ',', '.') ?>đ</span>
                                        <span class="product-price-old"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                    <?php else: ?>
                                        <span class="product-price"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($product['quantity']) && $product['quantity'] <= 0): ?>
                                        <span class="badge bg-danger rounded-1 ms-2 p-1 px-2 fw-medium" style="font-size: 0.75rem;">Hết hàng</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Dummy Products fallback -->
                <?php 
                $demoProducts = [
                    ['id' => 1, 'name' => 'Nike Air Zoom Pegasus 39', 'price' => 3000000, 'sale' => 2500000, 'brand' => 'Nike', 'img' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=500&q=80'],
                    ['id' => 2, 'name' => 'Nike Air Force 1 07 White', 'price' => 2800000, 'sale' => null, 'brand' => 'Nike', 'img' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=500&q=80'],
                    ['id' => 3, 'name' => 'Adidas Ultraboost 22 Core Black', 'price' => 3500000, 'sale' => 3200000, 'brand' => 'Adidas', 'img' => 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?auto=format&fit=crop&w=500&q=80'],
                    ['id' => 4, 'name' => 'Puma RS-X Reinvent', 'price' => 2400000, 'sale' => 1990000, 'brand' => 'Puma', 'img' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?auto=format&fit=crop&w=500&q=80'],
                ];
                foreach ($demoProducts as $dp): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card">
                            <div class="product-img-wrapper">
                                <?php if ($dp['sale']): ?>
                                    <div class="badge-ribbon badge-ribbon-red">SALE</div>
                                <?php endif; ?>
                                
                                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $dp['id'] ?>" class="d-block position-absolute w-100 h-100" style="top:0; left:0; z-index: 5;">
                                    <img src="<?= $dp['img'] ?>" alt="<?= $dp['name'] ?>">
                                </a>
                                <div class="product-actions-overlay">
                                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $dp['id'] ?>" class="btn-action-icon" title="Xem chi tiết"><i class="fa-solid fa-magnifying-glass"></i></a>
                                </div>
                                <?php if (($dp['quantity'] ?? 50) > 0): ?>
                                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/cart/add/<?= $dp['id'] ?>" class="btn-cart-hover" title="Thêm vào giỏ">
                                        <span class="cart-text">Thêm vào giỏ</span>
                                        <span class="cart-icon-wrapper">
                                            <svg class="custom-bag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                                                <path d="M9 6V4a3 3 0 0 1 6 0v2"></path>
                                                <rect x="4" y="6" width="16" height="14" rx="2"></rect>
                                                <circle cx="18" cy="18" r="5" class="bag-bg-circle" stroke="none"></circle>
                                                <circle cx="18" cy="18" r="4"></circle>
                                                <path d="M18 15v6m-3-3h6"></path>
                                            </svg>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/cart/add/<?= $product['product_id'] ?>" class="btn-cart-hover btn-out-of-stock" title="Tạm hết hàng">
                                        <span class="cart-text">Tạm hết hàng</span>
                                        <span class="cart-icon-wrapper">
                                            <svg class="custom-bag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                                                <path d="M9 6V4a3 3 0 0 1 6 0v2"></path>
                                                <rect x="4" y="6" width="16" height="14" rx="2"></rect>
                                                <circle cx="18" cy="18" r="5" class="bag-bg-circle" stroke="none"></circle>
                                                <circle cx="18" cy="18" r="4"></circle>
                                                <path d="M18 15v6m-3-3h6"></path>
                                            </svg>
                                        </span>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="product-body">
                                <span class="product-brand-tag"><?= $dp['brand'] ?></span>
                                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $dp['id'] ?>" class="product-title mb-1"><?= $dp['name'] ?></a>
                                <?php if (!empty($dp['sku'])): ?>
                                    <div class="text-muted mb-2" style="font-size: 0.85rem;"><?= htmlspecialchars($dp['sku']) ?></div>
                                <?php endif; ?>
                                <div class="product-rating">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                                <div class="product-price-box">
                                    <?php if ($dp['sale']): ?>
                                        <span class="product-price"><?= number_format($dp['sale'], 0, ',', '.') ?>đ</span>
                                        <span class="product-price-old"><?= number_format($dp['price'], 0, ',', '.') ?>đ</span>
                                    <?php else: ?>
                                        <span class="product-price"><?= number_format($dp['price'], 0, ',', '.') ?>đ</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        </div>
        </div>
</section>

<!-- 5. BANNER KHUYẾN MÃI LARGE -->
<section class="py-5 bg-dark text-white my-4 position-relative overflow-hidden" style="--promo-color: #ff006a;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="badge fs-6 mb-2 text-white border-0" style="background-color: var(--promo-color);">ƯU ĐÃI KHỦNG THÁNG NÀY</span>
                <h2 class="display-5 fw-bold text-uppercase text-white mb-3">SĂN GIÀY HIỆU - SIÊU SALE NỬA GIÁ</h2>
                <p class="fs-5 text-secondary mb-4">
                    Nhập mã <strong class="border px-2 py-1 rounded" style="border-color: var(--promo-color) !important; color: var(--promo-color) !important;">ANTA2026</strong> giảm ngay 20% cho đơn hàng đầu tiên từ 1.500.000đ.
                </p>
                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products?sale=1" class="btn btn-lg px-4 py-3 text-white border-0 fw-bold shadow-sm" style="background-color: var(--promo-color); transition: all 0.3s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'"><i class="fa-solid fa-bolt me-2"></i> MUA NGAY KẺO LỠ</a>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-center">
                <img src="<?= base_url('image/khuyen_mai.png') ?>" alt="Promo Shoe" class="img-fluid rounded-4 shadow-lg border border-2" style="max-height: 350px; object-fit: cover; border-color: var(--promo-color) !important;"> 
            </div> 
        </div>
    </div>
</section>

<!-- 6. GIỚI THIỆU CỬA HÀNG & ĐÁNH GIÁ KHÁCH HÀNG -->
<section id="about-brand" class="py-5" style="background-color: #fff; color: #333;">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9 text-center">
                <h3 class="fw-bold mb-4 text-uppercase text-dark" style="font-size: 1.8rem; line-height: 1.4;">
                     ANTA STORE – HIỆU NĂNG, PHONG CÁCH & ĐỔI MỚI
                </h3>
                
                <p class="mb-4 text-secondary" style="line-height: 1.8; font-size: 1.05rem; text-align: justify;">
                    Khám phá những đôi giày thể thao hiệu năng cao, ứng dụng công nghệ tiên tiến nhất để bạn chinh phục mọi giới hạn. Dù bạn là người đam mê chạy bộ, yêu thích bóng rổ hay đơn giản là theo đuổi lối sống năng động, ANTA Store luôn là điểm đến lý tưởng. Chúng tôi đồng hành để bạn tập luyện bền bỉ hơn, di chuyển linh hoạt hơn với những sản phẩm luôn theo kịp từng bước chân của bạn.
                </p>
                
                <p class="mb-5 text-secondary" style="line-height: 1.8; font-size: 1.05rem; text-align: justify;">
                    Cửa hàng giày thể thao ANTA không chỉ là nơi mua sắm, mà còn là không gian để bạn nâng tầm phong cách và bứt phá giới hạn bản thân. Mỗi đôi giày đều được chế tác tỉ mỉ, chú trọng tuyệt đối vào độ êm ái, độ bền và chi tiết thiết kế. Dù bạn chơi môn thể thao nào, ANTA luôn sẵn sàng đồng hành – giúp bạn tự tin tỏa sáng và từng bước vươn tới đỉnh cao.
                </p>
                
                <div class="mt-4">
                    <img src="<?= base_url('image/logo.webp') ?>" alt="ANTA Logo" style="height: 45px; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const brandFilters = document.querySelectorAll('#home-brand-filters .btn-brand-filter');
    const productContainer = document.getElementById('home-product-container');
    
    if (brandFilters.length > 0 && productContainer) {
        brandFilters.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Active state
                brandFilters.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const brandId = this.getAttribute('data-brand-id');
                const baseUrl = '<?= ($_ENV['APP_URL'] ?? '') ?>';
                const url = brandId ? `${baseUrl}/products?brand_id=${brandId}&home=1` : `${baseUrl}/products?home=1`;
                
                // Fetch new products
                productContainer.style.opacity = '0.5';
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    productContainer.innerHTML = html;
                    productContainer.style.opacity = '1';
                })
                .catch(err => {
                    console.error('Error fetching products:', err);
                    productContainer.style.opacity = '1';
                });
            });
        });
    }
});
</script>

<?php
require __DIR__ . '/layouts/footer.php';
?>
