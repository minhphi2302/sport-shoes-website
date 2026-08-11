<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">Giỏ hàng của bạn</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (empty($cart)): ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <i class="bi bi-cart-x display-1 text-muted mb-3 d-block"></i>
            <h4 class="mb-3">Giỏ hàng của bạn đang trống</h4>
            <p class="text-muted mb-4">Hãy quay lại trang sản phẩm để chọn cho mình những đôi giày ưng ý nhất nhé!</p>
            <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-primary px-4 py-2">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Danh sách sản phẩm -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col" class="border-0 text-uppercase fw-bold text-muted">Sản phẩm</th>
                                        <th scope="col" class="border-0 text-uppercase fw-bold text-muted text-center">Số lượng</th>
                                        <th scope="col" class="border-0 text-uppercase fw-bold text-muted text-end">Thành tiền</th>
                                        <th scope="col" class="border-0 text-uppercase fw-bold text-muted text-end"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart as $item): ?>
                                        <?php 
                                            $priceToUse = (!empty($item['sale_price']) && $item['sale_price'] < $item['price']) ? $item['sale_price'] : $item['price'];
                                            $subtotal = $priceToUse * $item['quantity'];
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php 
                                                        $imgSrc = !empty($item['image_url']) ? '/uploads/' . $item['image_url'] : '/uploads/default-product.jpg';
                                                        $baseUrl = htmlspecialchars($_ENV['APP_URL'] ?? '');
                                                    ?>
                                                    <img src="<?= $baseUrl . htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="img-fluid rounded me-3" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.src='<?= $baseUrl ?>/uploads/default-product.jpg'">
                                                    <div>
                                                        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products/<?= $item['product_id'] ?>" class="text-dark fw-bold text-decoration-none d-block mb-1">
                                                            <?= htmlspecialchars($item['name']) ?>
                                                        </a>
                                                        <small class="text-muted d-block">Mã: <?= htmlspecialchars($item['sku']) ?></small>
                                                        <?php if (!empty($item['size'])): ?>
                                                            <small class="text-muted d-block">Size: <span class="fw-semibold"><?= htmlspecialchars($item['size']) ?></span></small>
                                                        <?php endif; ?>
                                                        <?php if (!empty($item['color'])): ?>
                                                            <small class="text-muted d-block">Màu: <span class="fw-semibold"><?= htmlspecialchars($item['color']) ?></span></small>
                                                        <?php endif; ?>
                                                        <div class="mt-1 text-primary fw-semibold">
                                                            <?= number_format($priceToUse, 0, ',', '.') ?>đ
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/cart/update" method="POST" class="d-flex align-items-center justify-content-center">
                                                    <input type="hidden" name="cart_key" value="<?= $item['cart_key'] ?? $item['product_id'] ?>">
                                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control text-center mx-2" style="width: 70px;" onchange="this.form.submit()">
                                                </form>
                                            </td>
                                            <td class="text-end fw-bold">
                                                <?= number_format($subtotal, 0, ',', '.') ?>đ
                                            </td>
                                            <td class="text-end">
                                                <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/cart/remove" method="POST" class="d-inline">
                                                    <input type="hidden" name="cart_key" value="<?= $item['cart_key'] ?? $item['product_id'] ?>">
                                                    <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
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
                
                <div class="mt-4 d-flex justify-content-between">
                    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
                    </a>
                    <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/cart/clear" method="POST">
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')">
                            <i class="bi bi-trash"></i> Xóa toàn bộ
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tổng kết -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Tổng đơn hàng</h4>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Tạm tính</span>
                            <span class="fw-semibold"><?= number_format($total, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Phí vận chuyển</span>
                            <span class="fw-semibold text-success">Miễn phí</span>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Tổng cộng</span>
                            <span class="fw-bold fs-4 text-primary"><?= number_format($total, 0, ',', '.') ?>đ</span>
                        </div>

                        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/checkout" class="btn btn-primary w-100 py-3 rounded-pill fw-bold text-uppercase">
                            Tiến hành đặt hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
