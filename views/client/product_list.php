<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container py-5">
    <!-- Breadcrumb & Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-uppercase m-0">Danh sách sản phẩm</h2>
            <small class="text-muted">Khám phá các mẫu giày thể thao mới nhất</small>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <span class="badge bg-dark px-3 py-2 fs-6">Tổng cộng: <?= count($products) ?> sản phẩm</span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Filter -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px; z-index: 1;">
                <div class="card-body p-4">
                    <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products" method="GET">
                        <?php if (isset($filters['search'])): ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($filters['search']) ?>">
                        <?php endif; ?>

                        <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-funnel me-2 text-primary"></i>Danh mục</h5>
                        <div class="mb-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="category_id" id="cat_all" value="" <?= empty($filters['category_id']) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="cat_all">Tất cả danh mục</label>
                            </div>
                            <?php foreach ($categories as $cat): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="category_id" id="cat_<?= $cat['category_id'] ?>" value="<?= $cat['category_id'] ?>" <?= (isset($filters['category_id']) && $filters['category_id'] == $cat['category_id']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="cat_<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-tags me-2 text-primary"></i>Thương hiệu</h5>
                        <div class="mb-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="brand_id" id="brand_all" value="" <?= empty($filters['brand_id']) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="brand_all">Tất cả thương hiệu</label>
                            </div>
                            <?php foreach ($brands as $brand): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="brand_id" id="brand_<?= $brand['brand_id'] ?>" value="<?= $brand['brand_id'] ?>" <?= (isset($filters['brand_id']) && $filters['brand_id'] == $brand['brand_id']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="brand_<?= $brand['brand_id'] ?>"><?= htmlspecialchars($brand['name']) ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold"><i class="bi bi-filter me-1"></i> Lọc sản phẩm</button>
                        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-outline-secondary w-100 rounded-pill mt-2 py-2 fw-semibold">Xóa lọc</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <?php if (empty($products)): ?>
                <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-emoji-frown fs-1 text-muted d-block mb-3"></i>
                        <h4 class="fw-bold">Không tìm thấy sản phẩm nào!</h4>
                        <p class="text-muted mb-4">Vui lòng thử nghiệm bộ lọc khác hoặc tìm kiếm với từ khóa khác.</p>
                        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-primary rounded-pill px-4">Xem tất cả sản phẩm</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="card h-100 border-0 shadow-sm rounded-4 product-card overflow-hidden">
                            <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                <?php $discountPercent = round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>
                                <span class="position-absolute top-0 start-0 bg-danger text-white px-3 py-1 fw-bold rounded-end-pill z-3" style="font-size: 0.85rem;">
                                    -<?= $discountPercent ?>%
                                </span>
                            <?php endif; ?>

                            <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products/<?= $product['product_id'] ?>">
                                <?php 
                                    $imgSrc = !empty($product['image_url']) ? '/uploads/' . $product['image_url'] : '/uploads/default-product.jpg';
                                    $baseUrl = htmlspecialchars($_ENV['APP_URL'] ?? '');
                                ?>
                                <img src="<?= $baseUrl . htmlspecialchars($imgSrc) ?>" class="card-img-top p-3" style="height: 240px; object-fit: cover;" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='<?= $baseUrl ?>/uploads/default-product.jpg'">
                            </a>
                            
                            <div class="card-body d-flex flex-column pt-0">
                                <span class="text-muted small mb-1"><?= htmlspecialchars($product['brand_name'] ?? 'Giày Thể Thao') ?></span>
                                <h6 class="card-title text-truncate fw-bold mb-2">
                                    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products/<?= $product['product_id'] ?>" class="text-dark text-decoration-none"><?= htmlspecialchars($product['name']) ?></a>
                                </h6>
                                
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                            <span class="fw-bold text-danger fs-5 me-2"><?= number_format($product['sale_price'], 0, ',', '.') ?>đ</span>
                                            <small class="text-muted text-decoration-line-through"><?= number_format($product['price'], 0, ',', '.') ?>đ</small>
                                        <?php else: ?>
                                            <span class="fw-bold text-primary fs-5"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if(!\App\Core\Auth::check() || \App\Core\Auth::user()['role'] !== 'admin'): ?>
                                    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products/<?= $product['product_id'] ?>" class="btn btn-outline-primary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; padding: 0;" title="Xem chi tiết & chọn Size/Màu">
                                        <i class="bi bi-cart-plus"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav class="mt-5" aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <?php 
                                $prevQuery = $_GET; 
                                $prevQuery['page'] = $currentPage - 1;
                            ?>
                            <a class="page-link rounded-start-pill" href="?<?= http_build_query($prevQuery) ?>">Trước</a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php 
                                $pageQuery = $_GET; 
                                $pageQuery['page'] = $i;
                            ?>
                            <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query($pageQuery) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                            <?php 
                                $nextQuery = $_GET; 
                                $nextQuery['page'] = $currentPage + 1;
                            ?>
                            <a class="page-link rounded-end-pill" href="?<?= http_build_query($nextQuery) ?>">Sau</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
