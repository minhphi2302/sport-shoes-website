<div class="row g-4">
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="col-6 col-md-4 col-lg-3">
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
    <?php else: ?>
        <div class="col-12 text-center py-4">
            <p class="text-muted mb-0">Không tìm thấy sản phẩm nào.</p>
        </div>
    <?php endif; ?>
</div>
