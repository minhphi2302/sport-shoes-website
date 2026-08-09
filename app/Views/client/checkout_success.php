<?php require __DIR__ . '/layouts/header.php'; ?>

<div class="container my-5 py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-5">
                <div class="mb-4">
                    <i class="fa-solid fa-circle-check text-success" style="font-size: 5rem;"></i>
                </div>
                <h2 class="fw-bold mb-3">ĐẶT HÀNG THÀNH CÔNG!</h2>
                <p class="text-muted fs-5 mb-4">
                    Cảm ơn bạn đã mua sắm tại ANTA. Đơn hàng của bạn đang được xử lý.
                </p>
                
                <?php if (!empty($orderId)): ?>
                <div class="bg-light p-3 rounded-3 mb-4 d-inline-block">
                    <span class="text-muted me-2">Mã đơn hàng:</span>
                    <span class="fw-bold fs-5">#<?= htmlspecialchars($orderId) ?></span>
                </div>
                <?php endif; ?>

                <p class="mb-5">
                    Chúng tôi sẽ sớm liên hệ với bạn để xác nhận đơn hàng và thông báo thời gian giao hàng dự kiến. Bạn có thể theo dõi trạng thái đơn hàng trong phần quản lý tài khoản.
                </p>

                <div class="d-flex justify-content-center gap-3">
                    <a href="<?= base_url('account') ?>" class="btn btn-outline-dark px-4 py-2 rounded-pill fw-semibold">
                        Quản lý đơn hàng
                    </a>
                    <a href="<?= base_url('products') ?>" class="btn btn-dark px-4 py-2 rounded-pill fw-semibold">
                        Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/layouts/footer.php'; ?>
