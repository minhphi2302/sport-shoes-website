<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
    <h1 class="h3 fw-bold">Tổng quan Dashboard</h1>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-uppercase mb-1 opacity-75">Doanh thu tháng này</h6>
                        <h3 class="fw-bold mb-0"><?= number_format($monthlyRevenue, 0, ',', '.') ?>đ</h3>
                    </div>
                    <div class="p-2 bg-white bg-opacity-25 rounded-3">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-success text-white h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-uppercase mb-1 opacity-75">Đơn hàng hôm nay</h6>
                        <h3 class="fw-bold mb-0"><?= number_format($todayOrdersCount) ?></h3>
                    </div>
                    <div class="p-2 bg-white bg-opacity-25 rounded-3">
                        <i class="bi bi-cart-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-info text-white h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-uppercase mb-1 opacity-75">Tổng khách hàng</h6>
                        <h3 class="fw-bold mb-0"><?= number_format($totalCustomersCount) ?></h3>
                    </div>
                    <div class="p-2 bg-white bg-opacity-25 rounded-3">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-warning text-white h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-uppercase mb-1 opacity-75">Tổng sản phẩm</h6>
                        <h3 class="fw-bold mb-0"><?= number_format($totalProductsCount) ?></h3>
                    </div>
                    <div class="p-2 bg-white bg-opacity-25 rounded-3">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0">Đơn hàng chờ xử lý mới nhất</h5>
                <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/orders?status=pending" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Ngày đặt</th>
                                <th class="text-end">Tổng tiền</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($latestOrders)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">Không có đơn hàng chờ xử lý nào.</td></tr>
                            <?php else: ?>
                                <?php foreach ($latestOrders as $order): ?>
                                <tr>
                                    <td class="fw-semibold">#<?= str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td class="text-end fw-bold text-primary"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</td>
                                    <td class="text-end">
                                        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/orders/<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-secondary">Chi tiết</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
