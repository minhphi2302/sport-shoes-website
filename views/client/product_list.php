<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Danh sách sản phẩm</h2>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 80px; z-index: 1;">
                <div class="card-body">
                    <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products" method="GET">
                        <?php if (isset($filters['search'])): ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($filters['search']) ?>">
                        <?php endif; ?>

                        <h5 class="fw-bold mb-3">Danh mục</h5>
                        <div class="mb-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="category_id" id="cat_all" value="" <?= empty($filters['category_id']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="cat_all">Tất cả</label>
                            </div>
                            <?php foreach ($categories as $cat): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="category_id" id="cat_<?= $cat['category_id'] ?>" value="<?= $cat['category_id'] ?>" <?= (isset($filters['category_id']) && $filters['category_id'] == $cat['category_id']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="cat_<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <h5 class="fw-bold mb-3">Thương hiệu</h5>
                        <div class="mb-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="brand_id" id="brand_all" value="" <?= empty($filters['brand_id']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="brand_all">Tất cả</label>
                            </div>
                            <?php foreach ($brands as $brand): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="brand_id" id="brand_<?= $brand['brand_id'] ?>" value="<?= $brand['brand_id'] ?>" <?= (isset($filters['brand_id']) && $filters['brand_id'] == $brand['brand_id']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="brand_<?= $brand['brand_id'] ?>"><?= htmlspecialchars($brand['name']) ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Lọc sản phẩm</button>
                        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-outline-secondary w-100 mt-2">Xóa lọc</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <?php if (empty($products)): ?>
                <div class="alert alert-info text-center py-5 rounded-3">
                    <i class="bi bi-emoji-frown fs-1 d-block mb-3"></i>
                    <h4>Không tìm thấy sản phẩm nào!</h4>
                    <p class="mb-0">Vui lòng thử lại với bộ lọc khác hoặc tìm kiếm với từ khóa khác.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                    <div class="col-md-4 col-sm-6">
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

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav class="mt-5" aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <?php 
                                $prevQuery = $_GET; 
                                $prevQuery['page'] = $currentPage - 1;
                            ?>
                            <a class="page-link" href="?<?= http_build_query($prevQuery) ?>">Trước</a>
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
                            <a class="page-link" href="?<?= http_build_query($nextQuery) ?>">Sau</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
