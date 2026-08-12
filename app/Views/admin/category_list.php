<?php 
require_once __DIR__ . '/layouts/header.php'; 
$categories = $categories ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold m-0">Quản lý Danh mục</h1>
    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/categories/create" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Thêm danh mục mới
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
                    <th>Tên danh mục</th>
                    <th>Mô tả</th>
                    <th class="pe-4 text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                <tr>
                    <td class="ps-4"><?= $category['category_id'] ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($category['name']) ?></td>
                    <td class="text-muted text-truncate" style="max-width: 250px;"><?= htmlspecialchars($category['description']) ?></td>
                    <td class="pe-4 text-end">
                        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/categories/<?= $category['category_id'] ?>/edit" class="btn btn-sm btn-outline-primary me-2">Sửa</a>
                        <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/categories/<?= $category['category_id'] ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?');">
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
