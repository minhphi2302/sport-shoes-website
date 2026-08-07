<?php 
require_once __DIR__ . '/layouts/header.php'; 
$products = $products ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$filters = $filters ?? [];
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
    <h1 class="h3 fw-bold m-0">Quản lý Sản phẩm</h1>
    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/products/create" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Thêm sản phẩm
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

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/products" method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên sản phẩm hoặc mã SKU..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
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
                        <th class="ps-4">Sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Thương hiệu</th>
                        <th>Giá bán</th>
                        <th>Size</th>
                        <th>Màu</th>
                        <th class="text-center">Tồn kho</th>
                        <th class="pe-4 text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="8" class="text-center py-4">Không tìm thấy sản phẩm nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                        <tr style="cursor: pointer;" onclick="if(!event.target.closest('a') && !event.target.closest('button')) window.location.href='<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/products/<?= $product['product_id'] ?>'">
                            <td class="ps-4">
                                <div class="d-flex align-items-center"> 
                                    
                                    <?php 
                                        $imgSrc = !empty($product['image_url']) ? '/uploads/' . $product['image_url'] : '/uploads/default-product.jpg';
                                        $baseUrl = htmlspecialchars($_ENV['APP_URL'] ?? '');
                                    ?>
                                    <img src="<?= $baseUrl . htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 250px; height: 300px; object-fit: cover;" class="rounded me-3 product-img-fixed" onerror="this.src='<?= $baseUrl ?>/uploads/default-product.jpg'">
                                    <div>
                                        <div class="fw-semibold text-truncate" style="max-width: 250px;">
                                            <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/products/<?= $product['product_id'] ?>" class="text-decoration-none text-dark">
                                                <?= htmlspecialchars($product['name']) ?>
                                            </a>
                                        </div>
                                        <small class="text-muted">SKU: <?= htmlspecialchars($product['sku']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($product['category_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($product['brand_name'] ?? '') ?></td>
                            <td>
                                <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                    <div class="fw-bold text-danger"><?= number_format($product['sale_price'], 0, ',', '.') ?>đ</div>
                                    <small class="text-muted text-decoration-line-through"><?= number_format($product['price'], 0, ',', '.') ?>đ</small>
                                <?php else: ?>
                                    <div class="fw-bold"><?= number_format($product['price'], 0, ',', '.') ?>đ</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($product['variant_sizes'])): ?>
                                    <span class="text-muted small"><?= htmlspecialchars($product['variant_sizes']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted fst-italic small">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($product['variant_colors'])): ?>
                                    <span class="text-muted small"><?= htmlspecialchars($product['variant_colors']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted fst-italic small">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $product['quantity'] > 0 ? 'bg-success' : 'bg-danger' ?> rounded-pill px-3">
                                    <?= $product['quantity'] ?>
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/products/<?= $product['product_id'] ?>/edit" class="btn btn-sm btn-outline-primary me-1">Sửa</a>
                                <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/products/<?= $product['product_id'] ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
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
