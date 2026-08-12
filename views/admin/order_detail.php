<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex align-items-center mb-4">
    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/orders" class="text-decoration-none text-muted me-3 fs-5"><i class="bi bi-arrow-left"></i></a>
    <h1 class="h3 fw-bold m-0">Đơn hàng #<?= str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) ?></h1>
</div>

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

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white pt-4 pb-0 px-4 border-0"><h5 class="fw-bold">Danh sách sản phẩm</h5></div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-center">SL</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderDetails as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php 
                                            $imgSrc = !empty($item['image_url']) ? '/uploads/' . $item['image_url'] : '/uploads/default-product.jpg';
                                            $baseUrl = htmlspecialchars($_ENV['APP_URL'] ?? '');
                                        ?>
                                        <img src="<?= $baseUrl . htmlspecialchars($imgSrc) ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded me-3" onerror="this.src='<?= $baseUrl ?>/uploads/default-product.jpg'">
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars($item['product_name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($item['sku']) ?></small>
                                            <?php if (!empty($item['size']) && !empty($item['color'])): ?>
                                                <br><small class="text-muted">Size: <?= htmlspecialchars($item['size']) ?> - Màu: <?= htmlspecialchars($item['color']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end"><?= number_format($item['unit_price'], 0, ',', '.') ?>đ</td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td class="text-end fw-bold"><?= number_format($item['subtotal'], 0, ',', '.') ?>đ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <?php 
                            $adminItemsSubtotal = array_sum(array_column($orderDetails, 'subtotal'));
                            // Nếu có cột shipping_fee trong DB thì dùng, ngược lại suy ngược từ total_amount - items_subtotal
                            if (array_key_exists('shipping_fee', $order)) {
                                $adminShipFee = (float)$order['shipping_fee'];
                            } else {
                                $adminShipFee = max(0, (float)$order['total_amount'] - $adminItemsSubtotal);
                            }
                        ?>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end text-muted pt-3">Tạm tính tiền hàng:</td>
                                <td class="text-end fw-semibold pt-3"><?= number_format($adminItemsSubtotal, 0, ',', '.') ?>đ</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end text-muted">Phí vận chuyển & COD:</td>
                                <td class="text-end">
                                    <?php if ($adminShipFee == 0): ?>
                                        <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                            <i class="bi bi-truck me-1"></i> Miễn phí (Freeship)
                                        </span>
                                    <?php else: ?>
                                        <span class="fw-bold text-dark me-1"><?= number_format($adminShipFee, 0, ',', '.') ?>đ</span>
                                        <small class="badge bg-warning-subtle text-warning-emphasis border border-warning">Chưa đủ mốc Freeship</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-bold fs-5 text-dark pt-2">Tổng thanh toán:</td>
                                <td class="text-end fw-bold fs-4 text-primary pt-2"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white pt-4 pb-0 px-4 border-0"><h5 class="fw-bold">Trạng thái đơn hàng</h5></div>
            <div class="card-body p-4">
                <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/orders/<?= $order['order_id'] ?>/status" method="POST">
                    <div class="mb-3">
                        <select name="status" class="form-select form-select-lg" <?= in_array($order['status'], ['completed', 'cancelled']) ? 'disabled' : '' ?>>
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                            <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                        </select>
                    </div>
                    <?php if (!in_array($order['status'], ['completed', 'cancelled'])): ?>
                        <button type="submit" class="btn btn-primary w-100">Cập nhật trạng thái</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white pt-4 pb-0 px-4 border-0"><h5 class="fw-bold">Thông tin giao hàng</h5></div>
            <div class="card-body p-4">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted fw-normal">Người nhận</dt>
                    <dd class="col-sm-8 fw-semibold"><?= htmlspecialchars($order['recipient_name']) ?></dd>
                    
                    <dt class="col-sm-4 text-muted fw-normal">SĐT</dt>
                    <dd class="col-sm-8 fw-semibold"><?= htmlspecialchars($order['recipient_phone']) ?></dd>
                    
                    <dt class="col-sm-4 text-muted fw-normal">Địa chỉ</dt>
                    <dd class="col-sm-8 fw-semibold"><?= htmlspecialchars($order['shipping_address']) ?></dd>
                    
                    <dt class="col-sm-4 text-muted fw-normal">Phương thức</dt>
                    <dd class="col-sm-8 fw-semibold"><?= htmlspecialchars($order['payment_method']) ?></dd>

                    <dt class="col-sm-4 text-muted fw-normal">Vận chuyển</dt>
                    <dd class="col-sm-8">
                        <?php if ($adminShipFee == 0): ?>
                            <span class="badge bg-success"><i class="bi bi-patch-check-fill me-1"></i> Freeship (0đ)</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark"><i class="bi bi-truck me-1"></i> Phí ship: <?= number_format($adminShipFee, 0, ',', '.') ?>đ</span>
                        <?php endif; ?>
                    </dd>

                    <?php if (!empty($order['notes'])): ?>
                    <dt class="col-sm-4 text-muted fw-normal">Ghi chú</dt>
                    <dd class="col-sm-8 text-warning"><?= nl2br(htmlspecialchars($order['notes'])) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
