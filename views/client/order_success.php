<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card border-0 shadow-sm rounded-4 p-5">
                <i class="bi bi-check-circle-fill text-success display-1 mb-4"></i>
                <h1 class="fw-bold mb-3">Đặt hàng thành công!</h1>
                <p class="lead text-muted mb-4">Cảm ơn bạn đã mua sắm tại Shop Giày. Đơn hàng của bạn đã được ghi nhận và đang chờ xử lý.</p>
                
                <div class="bg-light rounded-3 p-4 mb-5 d-inline-block">
                    <h5 class="mb-2">Mã đơn hàng của bạn</h5>
                    <div class="display-6 fw-bold text-primary">#<?= str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) ?></div>
                </div>
                
                <div>
                    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/orders/<?= $order['order_id'] ?>" class="btn btn-outline-primary btn-lg rounded-pill px-4 me-3">Xem chi tiết đơn hàng</a>
                    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-primary btn-lg rounded-pill px-5">Tiếp tục mua sắm</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
