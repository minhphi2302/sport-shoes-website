<?php if (empty($_GET['home'])): ?>
<!-- Top Filter & Sorting Bar -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-light p-3 rounded-3 mb-4 border">
    <div class="mb-2 mb-md-0">
        <?php if (!empty($_GET['search'])): ?>
            <div class="d-flex align-items-center">
                <span class="text-muted fw-medium fs-6">Có</span>
                <span class="badge bg-danger rounded-pill px-3 py-1 mx-2 fs-6 shadow-sm"><?= $totalProducts ?? count($products) ?></span>
                <span class="text-muted fw-medium fs-6">kết quả tìm kiếm phù hợp</span>
            </div>
        <?php else: ?>
            <span class="fw-bold">Hiển thị <?= $totalProducts ?? count($products) ?> sản phẩm</span>
        <?php endif; ?>
    </div>

    <div class="d-flex align-items-center gap-2">
        <label class="form-label mb-0 text-nowrap fs-7 fw-bold text-uppercase">Sắp xếp:</label>
        <select name="sort" class="form-select form-select-sm" form="filterForm" onchange="document.getElementById('filterForm').dispatchEvent(new Event('submit'))">
            <option value="newest" <?= ($_GET['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
            <option value="price_asc" <?= ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
            <option value="price_desc" <?= ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
        </select>
    </div>
</div>
<?php endif; ?>

<!-- PRODUCT GRID -->
<div class="row g-4">
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="col-6 col-md-4 <?= !empty($_GET['home']) ? 'col-lg-3' : '' ?>">
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
                            <?php $discountPercent = round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>
                            <div class="badge-ribbon badge-ribbon-red" <?= $isNew ? 'style="left: 43px;"' : '' ?>><small>-</small><?= $discountPercent ?>%</div>
                        <?php endif; ?>

                        <?php 
                            $fallbackImg = 'image/slide/slide' . (($product['product_id'] % 3) + 1) . '.avif';
                            $imgSrc = !empty($product['image_url']) ? $product['image_url'] : $fallbackImg;
                        ?>
                        <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $product['product_id'] ?>" class="d-block position-absolute w-100 h-100" style="top:0; left:0; z-index: 5;">
                            <img src="<?= htmlspecialchars(base_url($imgSrc)) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        </a>
                        <!-- Center search button for viewing detail -->
                        <div class="product-center-action">
                            <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $product['product_id'] ?>" class="search-icon-btn" title="Xem chi tiết">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </a>
                        </div>
                        
                        <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/cart/add/<?= $product['product_id'] ?>" class="btn-cart-hover <?= (($product['quantity'] ?? 50) <= 0) ? 'btn-out-of-stock' : '' ?>" title="<?= (($product['quantity'] ?? 50) <= 0) ? 'Tạm hết hàng' : 'Thêm vào giỏ' ?>">
                            <span class="cart-text"><?= (($product['quantity'] ?? 50) <= 0) ? 'Tạm hết hàng' : 'Thêm vào giỏ' ?></span>
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
                            
                            <?php if (($product['quantity'] ?? 50) <= 0): ?>
                                <span class="badge bg-danger rounded-1 ms-2 p-1 px-2 fw-medium" style="font-size: 0.75rem;">Hết hàng</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <i class="fa-solid fa-box-open fs-1 text-muted mb-3"></i>
            <h4 class="fw-bold">Không tìm thấy sản phẩm phù hợp</h4>
            <p class="text-muted">Vui lòng thử bỏ lọc hoặc tìm kiếm từ khóa khác.</p>
            <button type="button" onclick="window.location='<?= ($_ENV['APP_URL'] ?? '') ?>/products'" class="btn btn-red mt-2">Xem tất cả sản phẩm</button>
        </div>
    <?php endif; ?>
</div>

<?php if (empty($_GET['home'])): ?>
<!-- PHÂN TRANG -->
<?php if (isset($totalPages) && $totalPages > 1): ?>
    <nav class="mt-5" aria-label="Product listing pagination">
        <ul class="pagination justify-content-center ajax-pagination">
            <li class="page-item <?= ($currentPageNum ?? 1) <= 1 ? 'disabled' : '' ?>">
                <a class="page-link text-dark" href="#" data-page="<?= ($currentPageNum ?? 1) - 1 ?>"><i class="fa-solid fa-angle-left"></i> Trước</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($currentPageNum ?? 1) == $i ? 'active' : '' ?>">
                    <a class="page-link <?= ($currentPageNum ?? 1) == $i ? 'bg-red border-red text-white' : 'text-dark' ?>" href="#" data-page="<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($currentPageNum ?? 1) >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link text-dark" href="#" data-page="<?= ($currentPageNum ?? 1) + 1 ?>">Sau <i class="fa-solid fa-angle-right"></i></a>
            </li>
        </ul>
    </nav>
<?php endif; ?>
<?php endif; ?>
