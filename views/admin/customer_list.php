<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
    <h1 class="h3 fw-bold m-0">Quản lý Khách hàng</h1>
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

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/customers" method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên hoặc email..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Tìm kiếm</button>
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
                        <th class="ps-4">ID</th>
                        <th>Khách hàng</th>
                        <th>Email</th>
                        <th>Ngày đăng ký</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="pe-4 text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr><td colspan="6" class="text-center py-4">Không tìm thấy khách hàng nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td class="ps-4"><?= $customer['user_id'] ?></td>
                            <td class="fw-semibold">
                                <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/customers/<?= $customer['user_id'] ?>" class="text-dark text-decoration-none">
                                    <?= htmlspecialchars($customer['name']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($customer['email']) ?></td>
                            <td><?= date('d/m/Y', strtotime($customer['created_at'])) ?></td>
                            <td class="text-center">
                                <?php if ($customer['status'] === 'active'): ?>
                                    <span class="badge bg-success rounded-pill px-3">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-danger rounded-pill px-3">Đã khóa</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-end">
                                <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/customers/<?= $customer['user_id'] ?>/toggle-status" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn <?= $customer['status'] === 'active' ? 'khóa' : 'mở khóa' ?> tài khoản này?');">
                                    <?php if ($customer['status'] === 'active'): ?>
                                        <button type="submit" class="btn btn-sm btn-outline-warning">Khóa</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success">Mở khóa</button>
                                    <?php endif; ?>
                                </form>
                                <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/customers/<?= $customer['user_id'] ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn tài khoản khách hàng này (sẽ xóa luôn các đơn hàng liên quan)?');">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                </form>
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
