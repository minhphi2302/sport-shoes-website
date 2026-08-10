<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold m-0">Quản lý Thương hiệu</h1>
    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/brands/create" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Thêm thương hiệu mới
    </a>
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

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Hình ảnh</th>
                    <th>Tên thương hiệu</th>
                    <th>Mô tả</th>
                    <th class="pe-4 text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($brands as $brand): ?>
                <tr>
                    <td class="ps-4"><?= $brand['brand_id'] ?></td>
                    <td>
                        <?php if (!empty($brand['image_url'])): ?>
                            <img src="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/uploads/<?= htmlspecialchars($brand['image_url']) ?>" alt="<?= htmlspecialchars($brand['name']) ?>" style="height: 40px; object-fit: contain;">
                        <?php else: ?>
                            <span class="text-muted small">Không có</span>
                        <?php endif; ?>
                    </td>
                    <td class="fw-semibold"><?= htmlspecialchars($brand['name']) ?></td>
                    <td class="text-muted text-truncate" style="max-width: 250px;"><?= htmlspecialchars($brand['description']) ?></td>
                    <td class="pe-4 text-end">
                        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/brands/<?= $brand['brand_id'] ?>/edit" class="btn btn-sm btn-outline-primary me-2">Sửa</a>
                        <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/brands/<?= $brand['brand_id'] ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa thương hiệu này?');">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
