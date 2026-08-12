<?php 
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/layouts/header.php'; 
?>
<!-- Breadcrumb -->
<div class="bg-light py-3 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item active text-red fw-semibold" aria-current="page">Danh sách sản phẩm</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Main Content -->
<div class="container py-5">
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
                        <a href="<?= base_url('products') ?>" class="btn btn-primary rounded-pill px-4">Xem tất cả sản phẩm</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="product-card h-100 border rounded-2 bg-white overflow-hidden position-relative">
                            <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                <?php $discountPercent = round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>
                                <div class="badge-ribbon badge-ribbon-red"><small>-</small><?= $discountPercent ?>%</div>
                            <?php endif; ?>
                            
                            <?php 
                                $imgSrc = get_product_image_url($product['image_url'] ?? null, $product['product_id'] ?? 1); 
                            ?>
                            <div class="position-relative">
                                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $product['product_id'] ?>" class="text-decoration-none d-block">
                                    <div style="background-color: #f8f9fa;" class="text-center p-2">
                                        <img src="<?= htmlspecialchars($imgSrc) ?>" class="img-fluid" style="height: 190px; object-fit: contain; width: 100%;" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='<?= base_url('image/logo.webp') ?>'">
                                    </div>
                                </a>
                                <div class="product-center-action">
                                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $product['product_id'] ?>" class="search-icon-btn" title="Xem chi tiết"><i class="fa-solid fa-magnifying-glass"></i></a>
                                </div>
                                <?php if (($product['quantity'] ?? 50) > 0): ?>
                                    <a href="<?= base_url('cart/add/' . $product['product_id']) ?>" class="btn-cart-hover position-absolute" style="bottom: 10px; right: 10px;" title="Thêm vào giỏ">
                                        <span class="cart-text">Thêm vào giỏ</span>
                                        <span class="cart-icon-wrapper">
                                            <svg class="custom-bag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;"><path d="M9 6V4a3 3 0 0 1 6 0v2"></path><rect x="4" y="6" width="16" height="14" rx="2"></rect><circle cx="18" cy="18" r="5" class="bag-bg-circle" stroke="none"></circle><circle cx="18" cy="18" r="4"></circle><path d="M18 15v6m-3-3h6"></path></svg>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= base_url('cart/add/' . $product['product_id']) ?>" class="btn-cart-hover position-absolute btn-out-of-stock" style="bottom: 10px; right: 10px;" title="Tạm hết hàng">
                                        <span class="cart-text">Tạm hết hàng</span>
                                        <span class="cart-icon-wrapper">
                                            <svg class="custom-bag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;"><path d="M9 6V4a3 3 0 0 1 6 0v2"></path><rect x="4" y="6" width="16" height="14" rx="2"></rect><circle cx="18" cy="18" r="5" class="bag-bg-circle" stroke="none"></circle><circle cx="18" cy="18" r="4"></circle><path d="M18 15v6m-3-3h6"></path></svg>
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
                                    <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                        <span class="text-danger fw-bold" style="font-size: 1.1rem;"><?= number_format($product['sale_price'], 0, ',', '.') ?>đ</span>
                                        <span class="text-muted text-decoration-line-through" style="font-size: 0.85rem;"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                    <?php else: ?>
                                        <span class="text-dark fw-bold" style="font-size: 1.1rem;"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($product['quantity']) && $product['quantity'] <= 0): ?>
                                        <span class="badge bg-danger rounded-1 ms-2 p-1 px-2 fw-medium" style="font-size: 0.75rem;">Hết hàng</span>
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
