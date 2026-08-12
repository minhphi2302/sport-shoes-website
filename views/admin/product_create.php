<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex align-items-center mb-4">
    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/products" class="text-decoration-none text-muted me-3 fs-5"><i class="bi bi-arrow-left"></i></a>
    <h1 class="h3 fw-bold m-0">Thêm sản phẩm</h1>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form id="product-form" action="" method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                <div class="col-md-8">
                    <!-- Thông tin cơ bản -->
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-semibold">Tên sản phẩm *</label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold">Mã sản phẩm gốc (SKU cha) *</label>
                            <input type="text" class="form-control text-uppercase" name="sku" value="<?= htmlspecialchars($product['sku'] ?? '') ?>" placeholder="VD: NIKE-AF1" style="text-transform: uppercase;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tổng số lượng kho (Tự động tính)</label>
                            <input type="number" class="form-control bg-light" name="quantity" id="total_quantity" value="<?= $product['quantity'] ?? 0 ?>" readonly>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold">Giá bán mặc định (VNĐ) *</label>
                            <input type="number" class="form-control" id="base_price" name="price" value="<?= $product['price'] ?? '' ?>" placeholder="VD: 2000000">
                        </div>
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold">Giá khuyến mãi (VNĐ - Tùy chọn)</label>
                            <input type="number" class="form-control" name="sale_price" value="<?= $product['sale_price'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả sản phẩm</label>
                        <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>

                    <?php require __DIR__ . '/components/variant_matrix_generator.php'; ?>

                        <!-- BẢNG HIỂN THỊ DANH SÁCH BIẾN THỂ -->
                        <div id="variant-global-error" class="text-danger fw-bold mb-2" style="display: none;"></div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Danh sách biến thể</h6>
                            <div>
                                <button type="button" class="btn btn-sm btn-success me-2" id="btn-add-single-variant">
                                    <i class="bi bi-plus-lg me-1"></i> Thêm 1 biến thể thủ công
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="btn-clear-all-variants">
                                    <i class="bi bi-trash me-1"></i> Xóa tất cả
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="variants-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="15%">Mã SKU</th>
                                        <th>Mẫu</th>
                                        <th>Đối tượng</th>
                                        <th>Size</th>
                                        <th>Màu sắc</th>
                                        <th width="15%">Giá riêng (VNĐ)</th>
                                        <th width="15%">Số lượng kho</th>
                                        <th width="5%" class="text-center">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($variants) && is_array($variants)): ?>
                                        <?php foreach ($variants as $v): 
                                            $modelVal = $v['model'] ?? 'Mặc định';
                                            $key = $modelVal . '-' . $v['size'] . '-' . $v['color'];
                                        ?>
                                            <tr data-key="<?= htmlspecialchars($key) ?>">
                                                <td><input type="text" name="variant_skus[]" class="form-control form-control-sm" value="<?= htmlspecialchars($v['sku'] ?? '') ?>" placeholder="SKU"></td>
                                                <td><input type="text" name="variant_models[]" class="form-control form-control-sm" value="<?= htmlspecialchars($modelVal) ?>"></td>
                                                <td>
                                                    <?php 
                                                        $parts = explode(' - ', $v['size']);
                                                        $genderVal = count($parts) > 1 ? trim($parts[0]) : 'Nam';
                                                        $sizeVal = count($parts) > 1 ? trim($parts[1]) : trim($parts[0]);
                                                    ?>
                                                    <select name="variant_genders[]" class="form-select form-select-sm">
                                                        <option value="Nam" <?= $genderVal === 'Nam' ? 'selected' : '' ?>>Nam</option>
                                                        <option value="Nữ" <?= $genderVal === 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                                                        <option value="Trẻ em" <?= $genderVal === 'Trẻ em' ? 'selected' : '' ?>>Trẻ em</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="variant_raw_sizes[]" class="form-control form-control-sm" value="<?= htmlspecialchars($sizeVal) ?>"></td>
                                                <td><input type="text" name="variant_colors[]" class="form-control form-control-sm" value="<?= htmlspecialchars($v['color']) ?>"></td>
                                                <td><input type="number" name="variant_prices[]" class="form-control form-control-sm variant-price" value="<?= $v['price'] ?? '' ?>" min="0"></td>
                                                <td><input type="number" name="variant_qtys[]" class="form-control form-control-sm variant-qty" value="<?= $v['quantity'] ?? 10 ?>" min="0"></td>
                                                <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove-variant"><i class="bi bi-trash"></i></button></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Cột thông tin phụ bên phải -->
                <div class="col-md-4">
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-semibold">Danh mục *</label>
                        <select class="form-select" name="category_id" id="category_select">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['category_id'] ?>" <?= (isset($product['category_id']) && $product['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label fw-semibold">Thương hiệu *</label>
                        <select class="form-select" name="brand_id">
                            <option value="">-- Chọn thương hiệu --</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand['brand_id'] ?>">
                                    <?= htmlspecialchars($brand['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Hình ảnh sản phẩm *</label>
                        <input class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm">Thêm sản phẩm</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>

<!-- JAVASCRIPT XỬ LÝ MA TRẬN BIẾN THỂ VÀ TÍNH GIÁ TỰ ĐỘNG -->
<script src="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/assets/js/admin_product.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
</script>

    // Lọc size theo category được chọn
    const categorySelect = document.getElementById('category_select');
    let allSizes = <?= json_encode($sizes) ?>;
    let filteredSizes = allSizes;

    categorySelect.addEventListener('change', async function() {
        const categoryId = this.value;
        
        if (!categoryId) {
            filteredSizes = allSizes;
            return;
        }

        try {
            const response = await fetch(`<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/api/sizes-by-category/${categoryId}`);
            const data = await response.json();
            
            if (data.success) {
                filteredSizes = data.sizes;
                console.log(`Loaded ${filteredSizes.length} sizes for gender: ${data.gender}`);
                
                // Cập nhật size options trong variant matrix generator
                if (typeof updateSizeOptions === 'function') {
                    updateSizeOptions(filteredSizes);
                }
            }
        } catch (error) {
            console.error('Error loading sizes:', error);
        }
    });

    // Trigger on page load if category already selected
    if (categorySelect.value) {
        categorySelect.dispatchEvent(new Event('change'));
    }
});
</script>
