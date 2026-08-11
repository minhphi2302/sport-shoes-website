<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
    <h1 class="h3 fw-bold">Quản lý Đơn hàng</h1>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/orders" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Mã đơn hàng, Tên khách..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="pending" <?= (isset($filters['status']) && $filters['status'] === 'pending') ? 'selected' : '' ?>>Chờ xử lý</option>
                    <option value="confirmed" <?= (isset($filters['status']) && $filters['status'] === 'confirmed') ? 'selected' : '' ?>>Đã xác nhận</option>
                    <option value="completed" <?= (isset($filters['status']) && $filters['status'] === 'completed') ? 'selected' : '' ?>>Hoàn thành</option>
                    <option value="cancelled" <?= (isset($filters['status']) && $filters['status'] === 'cancelled') ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Lọc</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th class="text-end">Tổng tiền</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="pe-4 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="6" class="text-center py-4">Không tìm thấy đơn hàng nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="ps-4 fw-semibold">#<?= str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <div><?= htmlspecialchars($order['customer_name']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($order['customer_email']) ?></small>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</span>
                                <?php if (isset($order['shipping_fee']) && $order['shipping_fee'] == 0): ?>
                                    <small class="d-block text-success font-monospace" style="font-size: 0.75rem;"><i class="bi bi-truck me-1"></i>Freeship</small>
                                <?php elseif (isset($order['shipping_fee']) && $order['shipping_fee'] > 0): ?>
                                    <small class="d-block text-muted font-monospace" style="font-size: 0.75rem;">(Phí ship: <?= number_format($order['shipping_fee'], 0, ',', '.') ?>đ)</small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
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
                                <span class="badge <?= $badgeClass ?>"><?= $label ?></span>
                            </td>
                            <td class="pe-4 text-center">
                                <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/orders/<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($totalPages > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php 
                $pageQuery = $_GET; 
                $pageQuery['page'] = $i;
            ?>
            <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query($pageQuery) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
