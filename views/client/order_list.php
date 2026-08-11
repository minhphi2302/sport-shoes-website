<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group rounded-4 shadow-sm">
                <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/profile"
                    class="list-group-item list-group-item-action py-3">
                    <i class="bi bi-person me-2"></i> Tài khoản của tôi
                </a>
                <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/orders"
                    class="list-group-item list-group-item-action active py-3">
                    <i class="bi bi-card-list me-2"></i> Lịch sử đơn hàng
                </a>
            </div>
        </div>

        <div class="col-md-9">
            <h3 class="fw-bold mb-4">Đơn hàng của bạn</h3>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (empty($orders)): ?>
                <div class="alert alert-info text-center py-5 rounded-4 border-0 shadow-sm">
                    <i class="bi bi-receipt display-4 mb-3 d-block text-muted"></i>
                    Bạn chưa có đơn hàng nào. <br><br>
                    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products"
                        class="btn btn-primary rounded-pill px-4 mt-2">Bắt đầu mua sắm</a>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 py-3">Mã đơn</th>
                                        <th class="py-3">Ngày đặt</th>
                                        <th class="py-3 text-end">Tổng tiền</th>
                                        <th class="py-3 text-center">Trạng thái</th>
                                        <th class="pe-4 py-3 text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td class="ps-4 fw-semibold text-primary">
                                                #<?= str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                            <td class="text-end fw-bold">
                                                <?= number_format($order['total_amount'], 0, ',', '.') ?>đ</td>
                                            <td class="text-center">
                                                <?php
                                                $badgeClass = match ($order['status']) {
                                                    'pending' => 'bg-secondary',
                                                    'confirmed' => 'bg-info',
                                                    'completed' => 'bg-success',
                                                    'cancelled' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                                $label = match ($order['status']) {
                                                    'pending' => 'Chờ xử lý',
                                                    'confirmed' => 'Đã xác nhận',
                                                    'completed' => 'Hoàn thành',
                                                    'cancelled' => 'Đã hủy',
                                                    default => 'Không rõ'
                                                };
                                                ?>
                                                <span
                                                    class="badge <?= $badgeClass ?> rounded-pill px-3 py-2"><?= $label ?></span>
                                            </td>
                                            <td class="pe-4 text-center">
                                                <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/orders/<?= $order['order_id'] ?>"
                                                    class="btn btn-outline-primary btn-sm rounded-pill px-3 me-1">Xem</a>
                                                <?php if ($order['status'] === 'pending'): ?>
                                                    <form method="POST"
                                                        action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/orders/<?= $order['order_id'] ?>/cancel"
                                                        style="display:inline;"
                                                        onsubmit="return confirm('Bạn có chắc muốn hủy đơn #<?= str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) ?>?');">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">Hủy</button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                    <a class="page-link rounded mx-1" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
