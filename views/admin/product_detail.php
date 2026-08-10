<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex align-items-center mb-4">
    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/products" class="text-decoration-none text-muted me-3 fs-5"><i class="bi bi-arrow-left"></i></a>
    <h1 class="h3 fw-bold m-0">Chi tiết sản phẩm</h1>
    <div class="ms-auto">
        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/products/<?= $product['product_id'] ?>/edit" class="btn btn-primary">
            <i class="bi bi-pencil-square"></i> Sửa sản phẩm
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 text-center">
                <?php 
                    $imgSrc = !empty($product['image_url']) ? '/uploads/' . $product['image_url'] : '/uploads/default-product.jpg';
                    $baseUrl = htmlspecialchars($_ENV['APP_URL'] ?? '');
                ?>
                <img src="<?= $baseUrl . htmlspecialchars($imgSrc) ?>" class="img-fluid rounded mb-3" style="max-height: 300px; object-fit: cover;" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='<?= $baseUrl ?>/uploads/default-product.jpg'">
                <h4 class="fw-bold"><?= htmlspecialchars($product['name']) ?></h4>
                <p class="text-muted mb-0">SKU: <?= htmlspecialchars($product['sku']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-0">Thông tin cơ bản</h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Danh mục:</div>
                    <div class="col-sm-8 fw-semibold"><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Thương hiệu:</div>
                    <div class="col-sm-8 fw-semibold"><?= htmlspecialchars($product['brand_name'] ?? 'N/A') ?></div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Giá bán:</div>
                    <div class="col-sm-8">
                        <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                            <span class="fw-bold text-danger me-2"><?= number_format($product['sale_price'], 0, ',', '.') ?>đ</span>
                            <span class="text-muted text-decoration-line-through"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                        <?php else: ?>
                            <span class="fw-bold"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Tổng số lượng tồn kho:</div>
                    <div class="col-sm-8">
                        <span class="badge <?= $product['quantity'] > 0 ? 'bg-success' : 'bg-danger' ?> rounded-pill px-3">
                            <?= $product['quantity'] ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Mô tả:</div>
                    <div class="col-sm-8">
                        <?= nl2br(htmlspecialchars($product['description'] ?? 'Chưa có mô tả.')) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-0">Phân loại hàng (Mẫu, Đối tượng, Size & Màu)</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mã SKU</th>
                                <th>Mẫu</th>
                                <th>Đối tượng</th>
                                <th>Size</th>
                                <th>Màu sắc</th>
                                <th class="text-end">Giá riêng (VNĐ)</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (isset($variants) && !empty($variants)) {
                                usort($variants, function($a, $b) {
                                    $gA = explode(' - ', $a['size'])[0] ?? 'Chung';
                                    $gB = explode(' - ', $b['size'])[0] ?? 'Chung';
                                    
                                    // Sắp xếp ưu tiên: Mẫu -> Đối tượng -> Size -> Màu
                                    $cmpModel = strcmp($a['model'] ?? '', $b['model'] ?? '');
                                    if ($cmpModel !== 0) return $cmpModel;

                                    $cmpGender = strcmp($gA, $gB);
                                    if ($cmpGender !== 0) return $cmpGender;
                                    
                                    $cmpSize = strnatcmp($a['size'], $b['size']);
                                    if ($cmpSize !== 0) return $cmpSize;
                                    
                                    return strcmp($a['color'] ?? '', $b['color'] ?? '');
                                });
                            }
                            ?>
                            <?php if (isset($variants) && !empty($variants)): ?>
                                <?php foreach ($variants as $v): 
                                    $sizeParts = explode(' - ', $v['size']);
                                    if (count($sizeParts) >= 2) {
                                        $g = trim($sizeParts[0]);
                                        $s = trim(implode(' - ', array_slice($sizeParts, 1)));
                                    } else {
                                        $g = 'Chung';
                                        $s = $v['size'];
                                    }
                                ?>
                                    <tr>
                                        <td><code><?= htmlspecialchars($v['sku'] ?? '') ?></code></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($v['model'] ?? 'Mặc định') ?></td>
                                        <td class="fw-semibold text-primary"><?= htmlspecialchars($g) ?></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($s) ?></td>
                                        <td><?= htmlspecialchars($v['color']) ?></td>
                                        <td class="text-end text-primary fw-bold"><?= isset($v['price']) ? number_format($v['price'], 0, ',', '.') . 'đ' : 'Theo giá gốc' ?></td>
                                        <td class="text-center"><?= $v['quantity'] ?></td>
                                        <td class="text-center">
                                            <?php if ($v['quantity'] > 0): ?>
                                                <span class="badge bg-success">Còn hàng</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Hết hàng</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-3">Sản phẩm này chưa có phân loại nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
