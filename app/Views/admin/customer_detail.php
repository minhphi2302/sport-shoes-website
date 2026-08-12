<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex align-items-center mb-4">
    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/customers" class="text-decoration-none text-muted me-3 fs-5"><i class="bi bi-arrow-left"></i></a>
    <h1 class="h3 fw-bold m-0">Chi tiết Khách hàng</h1>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 text-center p-4">
            <div class="mb-3">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px; font-size: 2.5rem;">
                    <?= strtoupper(substr(htmlspecialchars($customer['name']), 0, 1)) ?>
                </div>
            </div>
            <h4 class="fw-bold mb-1"><?= htmlspecialchars($customer['name']) ?></h4>
            <p class="text-muted mb-3"><?= htmlspecialchars($customer['email']) ?></p>
            
            <div class="mb-4">
                <?php if ($customer['status'] === 'active'): ?>
                    <span class="badge bg-success rounded-pill px-4 py-2 fs-6">Hoạt động</span>
                <?php else: ?>
                    <span class="badge bg-danger rounded-pill px-4 py-2 fs-6">Đã khóa</span>
                <?php endif; ?>
            </div>

            <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/customers/<?= $customer['user_id'] ?>/toggle-status" method="POST" onsubmit="return confirm('Bạn có chắc muốn <?= $customer['status'] === 'active' ? 'khóa' : 'mở khóa' ?> tài khoản này?');" class="mb-2">
                <?php if ($customer['status'] === 'active'): ?>
                    <button type="submit" class="btn btn-outline-warning w-100"><i class="bi bi-lock me-2"></i>Khóa tài khoản</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-unlock me-2"></i>Mở khóa tài khoản</button>
                <?php endif; ?>
            </form>
            
            <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/customers/<?= $customer['user_id'] ?>/delete" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn tài khoản khách hàng này (sẽ xóa luôn các đơn hàng liên quan)?');">
                <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-trash me-2"></i>Xóa tài khoản</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold">Thông tin cá nhân</h5>
            </div>
            <div class="card-body p-4">
                <dl class="row mb-0">
                    <dt class="col-sm-3 text-muted fw-normal">ID</dt>
                    <dd class="col-sm-9 fw-semibold"><?= $customer['user_id'] ?></dd>
                    
                    <dt class="col-sm-3 text-muted fw-normal mt-3">Họ và tên</dt>
                    <dd class="col-sm-9 fw-semibold mt-3"><?= htmlspecialchars($customer['name']) ?></dd>
                    
                    <dt class="col-sm-3 text-muted fw-normal mt-3">Email</dt>
                    <dd class="col-sm-9 fw-semibold mt-3"><?= htmlspecialchars($customer['email']) ?></dd>
                    
                    <dt class="col-sm-3 text-muted fw-normal mt-3">Giới tính</dt>
                    <dd class="col-sm-9 fw-semibold mt-3">
                        <?php 
                            if (isset($customer['gender'])) {
                                if ($customer['gender'] === 'male') echo 'Nam';
                                elseif ($customer['gender'] === 'female') echo 'Nữ';
                                else echo 'Khác';
                            } else {
                                echo '<span class="text-muted fst-italic">Chưa cập nhật</span>';
                            }
                        ?>
                    </dd>
                    
                    <dt class="col-sm-3 text-muted fw-normal mt-3">Số điện thoại</dt>
                    <dd class="col-sm-9 fw-semibold mt-3"><?= !empty($customer['phone']) ? htmlspecialchars($customer['phone']) : '<span class="text-muted fst-italic">Chưa cập nhật</span>' ?></dd>
                    
                    <dt class="col-sm-3 text-muted fw-normal mt-3">Địa chỉ</dt>
                    <dd class="col-sm-9 fw-semibold mt-3"><?= !empty($customer['address']) ? htmlspecialchars($customer['address']) : '<span class="text-muted fst-italic">Chưa cập nhật</span>' ?></dd>
                    
                    <dt class="col-sm-3 text-muted fw-normal mt-3">Ngày đăng ký</dt>
                    <dd class="col-sm-9 fw-semibold mt-3"><?= date('d/m/Y H:i', strtotime($customer['created_at'])) ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
