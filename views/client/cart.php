<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-uppercase m-0">Giỏ hàng của bạn</h2>
            <small class="text-muted">Quản lý và kiểm tra các sản phẩm đã chọn</small>
        </div>
    </div>

    <?php if (empty($cart)): ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border-0">
            <i class="bi bi-cart-x display-1 text-muted mb-3 d-block"></i>
            <h4 class="fw-bold mb-2">Giỏ hàng của bạn đang trống</h4>
            <p class="text-muted mb-4">Hãy chọn cho mình những đôi giày thể thao ưng ý nhất nhé!</p>
            <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-primary rounded-pill px-5 py-3 fw-bold"><i class="bi bi-shop me-2"></i> Khám phá sản phẩm ngay</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Danh sách sản phẩm -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="border-0 text-uppercase fw-bold text-muted ps-3">Sản phẩm</th>
                                        <th scope="col" class="border-0 text-uppercase fw-bold text-muted text-center">Số lượng</th>
                                        <th scope="col" class="border-0 text-uppercase fw-bold text-muted text-end">Thành tiền</th>
                                        <th scope="col" class="border-0 text-uppercase fw-bold text-muted text-end pe-3">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart as $item): ?>
                                        <?php 
                                            $priceToUse = (!empty($item['sale_price']) && $item['sale_price'] < $item['price']) ? $item['sale_price'] : $item['price'];
                                            $subtotal = $priceToUse * $item['quantity'];
                                        ?>
                                        <tr>
                                            <td class="ps-3 py-3">
                                                <div class="d-flex align-items-center">
                                                    <?php 
                                                        $imgSrc = !empty($item['image_url']) ? '/uploads/' . $item['image_url'] : '/uploads/default-product.jpg';
                                                        $baseUrl = htmlspecialchars($_ENV['APP_URL'] ?? '');
                                                    ?>
                                                    <img src="<?= $baseUrl . htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="img-fluid rounded-3 me-3 border" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.src='<?= $baseUrl ?>/uploads/default-product.jpg'">
                                                    <div>
                                                        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products/<?= $item['product_id'] ?>" class="text-dark fw-bold text-decoration-none d-block mb-1">
                                                            <?= htmlspecialchars($item['name']) ?>
                                                        </a>
                                                        <small class="text-muted d-block" style="font-size: 0.8rem;">Mã SKU: <?= htmlspecialchars($item['sku']) ?></small>
                                                        <?php if (!empty($item['size'])): ?>
                                                            <small class="text-muted d-block" style="font-size: 0.8rem;">Size: <span class="fw-semibold text-dark"><?= htmlspecialchars($item['size']) ?></span></small>
                                                        <?php endif; ?>
                                                        <?php if (!empty($item['color'])): ?>
                                                            <small class="text-muted d-block" style="font-size: 0.8rem;">Màu sắc: <span class="fw-semibold text-dark"><?= htmlspecialchars($item['color']) ?></span></small>
                                                        <?php endif; ?>
                                                        <div class="mt-1 text-primary fw-bold" style="font-size: 0.9rem;">
                                                            <?= number_format($priceToUse, 0, ',', '.') ?>đ
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/cart/update" method="POST" class="d-flex align-items-center justify-content-center">
                                                    <input type="hidden" name="cart_key" value="<?= $item['cart_key'] ?? $item['product_id'] ?>">
                                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control text-center rounded-3 mx-1" style="width: 70px;" onchange="this.form.submit()">
                                                </form>
                                            </td>
                                            <td class="text-end fw-bold text-danger">
                                                <?= number_format($subtotal, 0, ',', '.') ?>đ
                                            </td>
                                            <td class="text-end pe-3">
                                                <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/cart/remove" method="POST" class="d-inline">
                                                    <input type="hidden" name="cart_key" value="<?= $item['cart_key'] ?? $item['product_id'] ?>">
                                                    <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')" title="Xóa">
                                                        <i class="bi bi-trash3 fs-5"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Tiếp tục chọn đồ
                    </a>
                    <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/cart/clear" method="POST">
                        <button type="submit" class="btn btn-outline-danger rounded-pill px-4" onclick="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')">
                            <i class="bi bi-trash me-1"></i> Xóa toàn bộ giỏ hàng
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Tóm tắt đơn hàng</h4>
                        
                        <?php 
                            $threshold = defined('FREE_COD_THRESHOLD') ? FREE_COD_THRESHOLD : 1000000;
                            $needed = $threshold - $total;
                        ?>
                        <?php if ($needed > 0): ?>
                            <div class="alert alert-warning border-0 rounded-3 mb-3 p-3" style="font-size: 0.85rem;">
                                <i class="bi bi-truck me-1"></i> Mua thêm <strong><?= number_format($needed, 0, ',', '.') ?>đ</strong> để được <strong class="text-success">Miễn phí vận chuyển (Freeship)</strong>!
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success border-0 rounded-3 mb-3 p-3" style="font-size: 0.85rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Đơn hàng của bạn đã đạt mốc <strong class="text-success">Miễn phí vận chuyển (Freeship)</strong>!
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Tạm tính tiền hàng</span>
                            <span class="fw-semibold"><?= number_format($total, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Dự kiến giao hàng</span>
                            <span class="fw-semibold text-dark">Toàn quốc (1-3 ngày)</span>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Tạm tính:</span>
                            <span class="fw-bold fs-4 text-primary"><?= number_format($total, 0, ',', '.') ?>đ</span>
                        </div>

                        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/checkout" class="btn btn-primary w-100 py-3 rounded-pill fw-bold text-uppercase fs-5">
                            Tiến hành đặt hàng <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
