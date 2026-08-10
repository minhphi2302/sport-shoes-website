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
            <div class="col-6 col-md-4 col-lg-2">
                <a href="<?= base_url('products?brand_id=1') ?>" class="brand-card">
                    <img src="<?= base_url('public/image/brand/nike.jpg') ?>" alt="Nike" class="brand-logo-img">
                    <h6 class="brand-name-text">NIKE</h6>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="<?= base_url('products?brand_id=2') ?>" class="brand-card">
                    <img src="<?= base_url('public/image/brand/adidas.png') ?>" alt="Adidas" class="brand-logo-img">
                    <h6 class="brand-name-text">ADIDAS</h6>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="<?= base_url('products?brand_id=3') ?>" class="brand-card">
                    <img src="<?= base_url('public/image/brand/asics.jpg') ?>" alt="Anta" class="brand-logo-img">
                    <h6 class="brand-name-text">ASICS</h6>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="<?= base_url('products?brand_id=4') ?>" class="brand-card">
                    <img src="<?= base_url('public/image/brand/puma.png') ?>" alt="Puma" class="brand-logo-img">
                    <h6 class="brand-name-text">PUMA</h6>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="<?= base_url('products?brand_id=5') ?>" class="brand-card">
                    <img src="<?= base_url('public/image/brand/conver.png') ?>" alt="Converse" class="brand-logo-img">
                    <h6 class="brand-name-text">CONVERSE</h6>
                </a>
            </div>
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

<!-- 4. SẢN PHẨM NỔI BẬT & BÁN CHẠY -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Sản phẩm nổi bật</h2>
            <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-outline-dark-custom btn-sm">Xem tất cả sản phẩm <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-4">
            <?php if (!empty($featuredProducts)): ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card">
                            <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                <?php 
                                    $discountPercent = round((($product['price'] - $product['sale_price']) / $product['price']) * 100); 
                                ?>
                                <span class="product-badge-sale">-<?= $discountPercent ?>%</span>
                            <?php endif; ?>

                            <div class="product-img-wrapper">
                                <?php 
                                    $fallbackImg = 'image/slide/slide' . (($product['product_id'] % 3) + 1) . '.avif';
                                    $imgSrc = !empty($product['image_url']) ? $product['image_url'] : $fallbackImg;
                                ?>
                                <img src="<?= htmlspecialchars(base_url($imgSrc)) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                <div class="product-actions-overlay">
                                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $product['product_id'] ?>" class="btn-action-icon" title="Xem chi tiết"><i class="fa-regular fa-eye"></i></a>
                                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/cart/add/<?= $product['product_id'] ?>" class="btn-action-icon" title="Thêm vào giỏ"><i class="fa-solid fa-bag-shopping"></i></a>
                                </div>
                            </div>

                            <div class="product-body">
                                <span class="product-brand-tag"><?= htmlspecialchars($product['brand_name'] ?? 'Chính Hãng') ?></span>
                                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $product['product_id'] ?>" class="product-title"><?= htmlspecialchars($product['name']) ?></a>
                                <div class="product-rating">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                    <span class="text-muted ms-1 fs-7">(4.8)</span>
                                </div>
                                <div class="product-price-box">
                                    <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                        <span class="product-price"><?= number_format($product['sale_price'], 0, ',', '.') ?>đ</span>
                                        <span class="product-price-old"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                    <?php else: ?>
                                        <span class="product-price"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
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
                            <?php if ($dp['sale']): ?>
                                <span class="product-badge-sale">SALE</span>
                            <?php endif; ?>
                            <div class="product-img-wrapper">
                                <img src="<?= $dp['img'] ?>" alt="<?= $dp['name'] ?>">
                                <div class="product-actions-overlay">
                                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $dp['id'] ?>" class="btn-action-icon" title="Xem chi tiết"><i class="fa-regular fa-eye"></i></a>
                                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/cart" class="btn-action-icon" title="Thêm vào giỏ"><i class="fa-solid fa-bag-shopping"></i></a>
                                </div>
                            </div>
                            <div class="product-body">
                                <span class="product-brand-tag"><?= $dp['brand'] ?></span>
                                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $dp['id'] ?>" class="product-title"><?= $dp['name'] ?></a>
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
</section>

<!-- 5. BANNER KHUYẾN MÃI LARGE -->
<section class="py-5 bg-dark text-white my-4 position-relative overflow-hidden">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="badge bg-red fs-6 mb-2">ƯU ĐÃI KHỦNG THÁNG NÀY</span>
                <h2 class="display-5 fw-bold text-uppercase text-white mb-3">SĂN GIÀY HIỆU - SIÊU SALE NỬA GIÁ</h2>
                <p class="fs-5 text-secondary mb-4">
                    Nhập mã <strong class="text-white border px-2 py-1 rounded border-danger">SPORTSHOES2026</strong> giảm ngay 20% cho đơn hàng đầu tiên từ 1.500.000đ.
                </p>
                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products?sale=1" class="btn btn-red btn-lg px-4 py-3"><i class="fa-solid fa-bolt me-2"></i> MUA NGAY KẺO LỠ</a>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-center">
                <img src="https://images.unsplash.com/photo-1552346154-21d32810aba3?auto=format&fit=crop&w=600&q=80" alt="Promo Shoe" class="img-fluid rounded-4 shadow-lg border border-secondary" style="max-height: 350px; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<!-- 6. GIỚI THIỆU CỬA HÀNG & ĐÁNH GIÁ KHÁCH HÀNG -->
<section class="py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <h2 class="section-title">Về Sport Shoes Store</h2>
                <p class="lead">Hệ thống phân phối giày thể thao chính hãng cao cấp hàng đầu.</p>
                <p class="text-secondary">
                    Chúng tôi mang đến những sản phẩm thể thao chất lượng đỉnh cao đến từ các thương hiệu hàng đầu thế giới như Nike, Adidas, Puma, Converse, New Balance. Cam kết mang đến trải nghiệm êm ái, nâng tầm phong cách sống năng động cho bạn.
                </p>
                <div class="row g-3 mt-3">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center border">
                            <h3 class="fw-bold text-red mb-0">10,000+</h3>
                            <small class="text-muted">Khách hàng tin dùng</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center border">
                            <h3 class="fw-bold text-red mb-0">100%</h3>
                            <small class="text-muted">Chính hãng 100%</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Reviews -->
            <div class="col-lg-6">
                <h2 class="section-title">Đánh giá từ khách hàng</h2>
                <div class="card border-0 bg-light p-4 rounded-4 shadow-sm mb-3">
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" class="rounded-circle me-3" width="50" height="50" alt="Avatar">
                        <div>
                            <h6 class="fw-bold mb-0">Nguyễn Văn Anh</h6>
                            <small class="text-warning"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></small>
                        </div>
                    </div>
                    <p class="card-text text-secondary fst-italic">"Giày Nike Air Zoom đi cực kỳ êm chân, giao hàng thần tốc chỉ 1 ngày tại Hà Nội. Đóng gói cẩn thận 2 lớp hộp rất chu đáo!"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require __DIR__ . '/layouts/footer.php';
?>
