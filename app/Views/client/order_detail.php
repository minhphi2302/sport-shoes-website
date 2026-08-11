<?php 
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/layouts/header.php'; 
?>

<div class="container py-5">
    <div class="mb-4 d-flex align-items-center">
        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/orders" class="text-decoration-none text-muted me-3">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
        <h3 class="fw-bold m-0">Chi tiết đơn hàng #<?= str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) ?></h3>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-pill">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-pill">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold">Sản phẩm</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 border-0 rounded-start">Sản phẩm</th>
                                    <th class="border-0 text-end">Đơn giá</th>
                                    <th class="border-0 text-center">SL</th>
                                    <th class="border-0 text-end rounded-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderDetails as $item): ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <?php 
                                                $imgSrc = !empty($item['image_url']) ? '/uploads/' . $item['image_url'] : '/uploads/default-product.jpg';
                                                $baseUrl = htmlspecialchars($_ENV['APP_URL'] ?? '');
                                            ?>
                                            <img src="<?= $baseUrl . htmlspecialchars($imgSrc) ?>" alt="" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.src='<?= $baseUrl ?>/uploads/default-product.jpg'">
                                            <div>
                                                <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products/<?= $item['product_id'] ?>" class="text-dark fw-bold text-decoration-none d-block">
                                                    <?= htmlspecialchars($item['product_name']) ?>
                                                </a>
                                                <small class="text-muted">Mã: <?= htmlspecialchars($item['sku']) ?></small>
                                                <?php if (!empty($item['size']) && !empty($item['color'])): ?>
                                                    <br><small class="text-muted">Size: <?= htmlspecialchars($item['size']) ?> - Màu: <?= htmlspecialchars($item['color']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end text-muted"><?= number_format($item['unit_price'], 0, ',', '.') ?>đ</td>
                                    <td class="text-center fw-semibold"><?= $item['quantity'] ?></td>
                                    <td class="text-end fw-bold text-primary"><?= number_format($item['subtotal'], 0, ',', '.') ?>đ</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php 
                $itemsSubtotal = array_sum(array_column($orderDetails, 'subtotal'));
                $shipFee = (float)($order['shipping_fee'] ?? 0);
            ?>
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tạm tính:</span>
                        <span class="fw-semibold"><?= number_format($itemsSubtotal, 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">Phí vận chuyển & COD:</span>
                        <?php if ($shipFee == 0): ?>
                            <span class="text-success fw-semibold"><i class="bi bi-patch-check-fill me-1"></i>Miễn phí (Freeship)</span>
                        <?php else: ?>
                            <span class="fw-semibold text-dark"><?= number_format($shipFee, 0, ',', '.') ?>đ</span>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0">Tổng thanh toán:</h5>
                        <span class="fs-4 fw-bold text-primary"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Thông tin đơn hàng</h5>
                    <div class="mb-3 d-flex justify-content-between border-bottom pb-2">
                        <span class="text-muted">Ngày đặt:</span>
                        <span class="fw-semibold"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between border-bottom pb-2 align-items-center">
                        <span class="text-muted">Trạng thái:</span>
                        <?php
                            $badgeClass = match($order['status']) {
                                'pending' => 'bg-secondary',
                                'confirmed' => 'bg-info',
                                'completed' => 'bg-success',
                                'cancelled' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            $label = match($order['status']) {
                                'pending' => 'Chờ xử lý',
                                'confirmed' => 'Đã xác nhận',
                                'completed' => 'Hoàn thành',
                                'cancelled' => 'Đã hủy',
                                default => 'Không rõ'
                            };
                        ?>
                        <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-2"><?= $label ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Thanh toán:</span>
                        <span class="fw-semibold"><?= htmlspecialchars($order['payment_method']) ?></span>
                    </div>
                    <?php if ($order['status'] === 'pending'): ?>
                    <hr>
                    <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/orders/<?= $order['order_id'] ?>/cancel" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này? Không thể hoàn tác sau khi hủy.');">
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill">Hủy đơn hàng</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Thông tin giao hàng</h5>
                    <div class="mb-3">
                        <label class="text-muted small">Người nhận:</label>
                        <div class="fw-semibold"><?= htmlspecialchars($order['recipient_name']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Số điện thoại:</label>
                        <div class="fw-semibold"><?= htmlspecialchars($order['recipient_phone']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Địa chỉ:</label>
                        <div class="fw-semibold"><?= htmlspecialchars($order['shipping_address']) ?></div>
                    </div>
                    <?php if (!empty($order['notes'])): ?>
                    <div>
                        <label class="text-muted small">Ghi chú:</label>
                        <div class="fw-semibold text-warning"><?= nl2br(htmlspecialchars($order['notes'])) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
