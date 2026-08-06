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

<!-- PRODUCT GRID -->
<div class="row g-4">
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="col-6 col-md-4">
                <div class="product-card">
                    <?php if (($product['quantity'] ?? 50) <= 0): ?>
                        <span class="product-badge-sale bg-secondary text-white border-0" style="left: 10px; right: auto;">HẾT HÀNG</span>
                    <?php elseif (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                        <?php $discountPercent = round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>
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
                            <?php if (($product['quantity'] ?? 50) > 0): ?>
                                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/cart/add/<?= $product['product_id'] ?>" class="btn-action-icon" title="Thêm vào giỏ"><i class="fa-solid fa-bag-shopping"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-body">
                        <span class="product-brand-tag"><?= htmlspecialchars($product['brand_name'] ?? 'Chính Hãng') ?></span>
                        <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $product['product_id'] ?>" class="product-title"><?= htmlspecialchars($product['name']) ?></a>
                        <div class="product-rating">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
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
        <div class="col-12 text-center py-5">
            <i class="fa-solid fa-box-open fs-1 text-muted mb-3"></i>
            <h4 class="fw-bold">Không tìm thấy sản phẩm phù hợp</h4>
            <p class="text-muted">Vui lòng thử bỏ lọc hoặc tìm kiếm từ khóa khác.</p>
            <button type="button" onclick="window.location='<?= ($_ENV['APP_URL'] ?? '') ?>/products'" class="btn btn-red mt-2">Xem tất cả sản phẩm</button>
        </div>
    <?php endif; ?>
</div>

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
