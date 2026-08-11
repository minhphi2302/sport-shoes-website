<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">Thanh toán & Đặt hàng</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/checkout" method="POST">
        <div class="row g-5">
            <!-- Thông tin giao hàng -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Thông tin giao hàng</h4>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Họ và tên người nhận</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label fw-semibold">Địa chỉ giao hàng (Số nhà, đường, xã/phường, quận/huyện, tỉnh/TP)</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold">Ghi chú đơn hàng (Tùy chọn)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Ví dụ: Giao hàng giờ hành chính..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Phương thức thanh toán</h4>
                        
                        <div class="form-check mb-3 p-3 border rounded-3 bg-light">
                            <input class="form-check-input ms-1" type="radio" name="payment_method" id="payment_cod" value="COD" checked>
                            <label class="form-check-label ms-2 fw-semibold d-flex align-items-center" for="payment_cod">
                                <i class="bi bi-cash-stack fs-4 text-success me-2"></i> Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        
                        <!-- Có thể thêm VNPAY ở đây sau này -->
                    </div>
                </div>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Tóm tắt đơn hàng</h4>
                        
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                            <span class="text-muted">Sản phẩm</span>
                            <span class="text-muted text-end">Tạm tính</span>
                        </div>
                        
                        <?php foreach ($cart as $item): ?>
                            <?php 
                                $priceToUse = (!empty($item['sale_price']) && $item['sale_price'] < $item['price']) ? $item['sale_price'] : $item['price'];
                                $subtotal = $priceToUse * $item['quantity'];
                            ?>
                            <div class="d-flex justify-content-between mb-3 align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3">
                                        <?php 
                                            $imgSrc = !empty($item['image_url']) ? '/uploads/' . $item['image_url'] : '/uploads/default-product.jpg';
                                            $baseUrl = htmlspecialchars($_ENV['APP_URL'] ?? '');
                                        ?>
                                        <img src="<?= $baseUrl . htmlspecialchars($imgSrc) ?>" alt="" class="rounded" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='<?= $baseUrl ?>/uploads/default-product.jpg'">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                            <?= $item['quantity'] ?>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="d-block fw-semibold" style="font-size: 0.9rem;"><?= htmlspecialchars($item['name']) ?></span>
                                        <?php if (!empty($item['size'])): ?>
                                            <small class="text-muted d-block" style="font-size: 0.8rem;">Size: <?= htmlspecialchars($item['size']) ?></small>
                                        <?php endif; ?>
                                        <?php if (!empty($item['color'])): ?>
                                            <small class="text-muted d-block" style="font-size: 0.8rem;">Màu: <?= htmlspecialchars($item['color']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="fw-semibold text-end" style="font-size: 0.9rem;"><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                            </div>
                        <?php endforeach; ?>
                        
                        <hr class="my-4">
                        
                        <?php 
                            $threshold = $freeThreshold ?? 1000000;
                            $currentSubtotal = $subtotal ?? 0;
                            $currentShippingFee = $shippingFee ?? 0;
                            $currentGrandTotal = $grandTotal ?? ($currentSubtotal + $currentShippingFee);
                        ?>

                        <?php if ($currentSubtotal < $threshold): ?>
                            <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-3 p-3 d-flex align-items-center" style="font-size: 0.85rem;">
                                <i class="bi bi-info-circle-fill text-warning me-2 fs-5"></i>
                                <div>
                                    Mua thêm <strong><?= number_format($threshold - $currentSubtotal, 0, ',', '.') ?>đ</strong> để được <strong class="text-success">Miễn phí vận chuyển</strong>!
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3 p-3 d-flex align-items-center" style="font-size: 0.85rem;">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                <div>
                                    Đơn hàng của bạn đã đủ điều kiện <strong class="text-success">Miễn phí vận chuyển (Freeship)</strong>!
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tạm tính</span>
                            <span class="fw-semibold"><?= number_format($currentSubtotal, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Phí vận chuyển & COD</span>
                            <?php if ($currentShippingFee == 0): ?>
                                <span class="fw-semibold text-success"><i class="bi bi-patch-check-fill me-1"></i>Miễn phí</span>
                            <?php else: ?>
                                <span class="fw-semibold text-dark"><?= number_format($currentShippingFee, 0, ',', '.') ?>đ</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <span class="fw-bold fs-5">Tổng cộng</span>
                            <span class="fw-bold fs-4 text-primary"><?= number_format($currentGrandTotal, 0, ',', '.') ?>đ</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 mt-4 rounded-pill fw-bold text-uppercase fs-5">
                            Đặt hàng ngay
                        </button>
                        <div class="text-center mt-3">
                            <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/cart" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Quay lại giỏ hàng</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
