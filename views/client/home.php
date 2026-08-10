<?php require_once __DIR__ . '/layouts/header.php'; ?>

<!-- Hero Section -->
<section class="bg-primary text-white py-5 mb-5">
    <div class="container text-center py-5">
        <h1 class="display-4 fw-bold">Bước chạy mới, cảm hứng mới</h1>
        <p class="lead mb-4">Khám phá bộ sưu tập giày thể thao đỉnh cao nhất mùa này.</p>
        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-light btn-lg rounded-pill px-5">Khám phá ngay</a>
    </div>
</section>

<!-- Featured Categories -->
<section class="container mb-5">
    <h2 class="text-center mb-4 fw-bold">Danh Mục Nổi Bật</h2>
    <div class="row g-4 justify-content-center">
        <?php foreach ($categories as $category): ?>
        <div class="col-md-4 col-sm-6">
            <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products?category_id=<?= $category['category_id'] ?>" class="text-decoration-none">
                <div class="card bg-dark text-white border-0 h-100 product-card overflow-hidden">
                    <!-- Bạn có thể thêm background image ở đây nếu có -->
                    <div class="card-body text-center p-5 d-flex align-items-center justify-content-center" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0);">
                        <h4 class="card-title fw-bold m-0"><?= htmlspecialchars($category['name']) ?></h4>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Products -->
<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0">Sản Phẩm Mới Nhất</h2>
        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products" class="text-decoration-none fw-bold">Xem tất cả <i class="bi bi-arrow-right"></i></a>
    </div>
    
    <div class="row g-4">
        <?php foreach ($featuredProducts as $product): ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card h-100 product-card">
                <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products/<?= $product['product_id'] ?>">
                    <?php 
                        $imgSrc = !empty($product['image_url']) ? '/uploads/' . $product['image_url'] : '/uploads/default-product.jpg';
                        $baseUrl = htmlspecialchars($_ENV['APP_URL'] ?? '');
                    ?>
                    <img src="<?= $baseUrl . htmlspecialchars($imgSrc) ?>" class="card-img-top product-img-fixed" style="width: 250px; height: 300px; object-fit: cover;" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='<?= $baseUrl ?>/uploads/default-product.jpg'">
                </a>
                <div class="card-body d-flex flex-column">
                    <span class="text-muted small mb-1"><?= htmlspecialchars($product['brand_name'] ?? 'Brand') ?></span>
                    <h5 class="card-title text-truncate">
                        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products/<?= $product['product_id'] ?>" class="text-dark text-decoration-none"><?= htmlspecialchars($product['name']) ?></a>
                    </h5>
                    
                    <div class="mt-auto pt-3 d-flex justify-content-between align-items-center">
                        <div>
                            <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                <span class="price-sale d-block"><?= number_format($product['sale_price'], 0, ',', '.') ?>đ</span>
                                <span class="price-original"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                            <?php else: ?>
                                <span class="price-regular d-block"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                            <?php endif; ?>
                        </div>
                        <?php if(!\App\Core\Auth::check() || \App\Core\Auth::user()['role'] !== 'admin'): ?>
                        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products/<?= $product['product_id'] ?>" class="btn btn-outline-primary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; padding: 0;" title="Chọn tuỳ chọn">
                            <i class="bi bi-cart-plus"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
