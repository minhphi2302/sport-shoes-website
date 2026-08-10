<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex align-items-center mb-4">
    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/products" class="text-decoration-none text-muted me-3 fs-5"><i class="bi bi-arrow-left"></i></a>
    <h1 class="h3 fw-bold m-0">Sửa sản phẩm</h1>
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
                            <label class="form-label fw-semibold">Mã sản phẩm (SKU) *</label>
                            <input type="text" class="form-control text-uppercase" name="sku" value="<?= htmlspecialchars($product['sku'] ?? '') ?>" placeholder="VD: NIKE-AF1" style="text-transform: uppercase;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tổng số lượng</label>
                            <input type="number" class="form-control bg-light" name="quantity" id="total_quantity" value="<?= $product['quantity'] ?? 0 ?>" readonly>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold">Giá bán (VNĐ) </label>
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

                        <!-- BULK UPDATE VARIANTS (Lọc theo SKU) -->
                        <div class="card border-info mb-3">
                            <div class="card-header bg-info text-white fw-bold py-2">
                                <i class="bi bi-magic me-1"></i> Cập nhật nhanh hàng loạt (Gom nhóm)
                            </div>
                            <div class="card-body bg-light p-3">
                                <!-- Hàng 1: Điều kiện lọc (SKU) -->
                                <div class="fw-bold text-secondary mb-2 small">1. BỘ LỌC (Nhập mã SKU hoặc một phần SKU để lọc, bỏ trống = Lọc tất cả)</div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label small fw-semibold text-primary mb-1">Lọc theo Mã SKU (Ví dụ: -40, hoặc Đỏ, hoặc 1 phần mã bất kỳ):</label>
                                        <input type="text" class="form-control form-control-sm" id="filter-sku" placeholder="Nhập mã SKU hoặc một phần SKU cần lọc...">
                                    </div>
                                </div>

                                <!-- Hàng 2: Giá trị gán mới -->
                                <div class="fw-bold text-secondary mb-2 small mt-3">2. GÁN GIÁ TRỊ MỚI (Bỏ trống = Giữ nguyên)</div>
                                <div class="row g-2 align-items-end mb-2">
                                    <div class="col-md-3">
                                        <label class="form-label small mb-1">Mẫu mới & Giá Mẫu (VNĐ)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" id="bulk-model-input" list="datalist-models" placeholder="Tên mẫu mới">
                                            <input type="number" class="form-control" id="bulk-model-price" placeholder="Giá mẫu" style="max-width: 80px;">
                                        </div>
                                        <datalist id="datalist-models"></datalist>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small mb-1">Đối tượng mới</label>
                                        <select class="form-select form-select-sm" id="bulk-gender-input">
                                            <option value="">-- Bỏ qua --</option>
                                            <option value="Nam">Nam</option>
                                            <option value="Nữ">Nữ</option>
                                            <option value="Trẻ em">Trẻ em</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small mb-1">Size mới & Giảm giá (%)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" id="bulk-size-input" list="datalist-sizes" placeholder="VD: 40">
                                            <input type="number" class="form-control" id="bulk-size-percent" placeholder="0" min="0" max="100" style="max-width: 60px;">
                                        </div>
                                        <datalist id="datalist-sizes"></datalist>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small mb-1">Màu sắc mới & Giảm giá (%)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" id="bulk-color-input" list="datalist-colors" placeholder="Tên màu">
                                            <input type="number" class="form-control" id="bulk-color-percent" placeholder="0" min="0" max="100" style="max-width: 60px;">
                                        </div>
                                        <datalist id="datalist-colors"></datalist>
                                    </div>
                                </div>
                                <div class="row g-2 align-items-end mt-2">
                                    <div class="col-md-4">
                                        <label class="form-label small mb-1">Giá mới (Ghi đè giá trị tuyệt đối)</label>
                                        <input type="number" class="form-control form-control-sm" id="bulk-price-input" placeholder="Nhập giá mới (Bỏ trống = Dùng Đ/c Giá ở trên)">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small mb-1">Số lượng mới (Ghi đè)</label>
                                        <input type="number" class="form-control form-control-sm" id="bulk-qty-input" placeholder="Nhập SL mới">
                                    </div>
                                    <div class="col-md-5 text-end">
                                        <button type="button" class="btn btn-sm btn-info text-white fw-bold px-4" id="btn-bulk-apply">
                                            <i class="bi bi-check2-circle"></i> Áp dụng cho các dòng thỏa mãn bộ lọc
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BẢNG HIỂN THỊ DANH SÁCH BIẾN THỂ -->
                        <div id="variant-global-error" class="text-danger fw-bold mb-2" style="display: none;"></div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Danh sách biến thể</h6>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="btn-clear-all-variants">
                                <i class="bi bi-trash me-1"></i> Xóa tất cả
                            </button>
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
                                    <?php 
                                    if (isset($variants) && is_array($variants)) {
                                        usort($variants, function($a, $b) {
                                            $gA = explode(' - ', $a['size'])[0] ?? 'Chung';
                                            $gB = explode(' - ', $b['size'])[0] ?? 'Chung';
                                            
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

                <div class="col-md-4">
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-semibold">Danh mục *</label>
                        <select class="form-select" name="category_id">
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
                                <option value="<?= $brand['brand_id'] ?>" <?= (isset($product['brand_id']) && $product['brand_id'] == $brand['brand_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($brand['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4 position-relative">
                        <label class="form-label fw-semibold">Hình ảnh sản phẩm</label>
                        <?php if (isset($product) && !empty($product['image_url'])): ?>
                            <div class="mb-2">
                                <img src="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/uploads/<?= htmlspecialchars($product['image_url']) ?>" alt="Current image" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                        <input class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>

<!-- JAVASCRIPT XỬ LÝ MA TRẬN BIẾN THỂ VÀ TÍNH GIÁ TỰ ĐỘNG -->
<script src="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/assets/js/admin_product.js"></script>
<script>
<?php
$dynamicSizeMapping = ['Nam' => [], 'Nữ' => [], 'Trẻ em' => []];
if (isset($sizes)) {
    foreach ($sizes as $sz) {
        if (isset($dynamicSizeMapping[$sz['gender']])) {
            $dynamicSizeMapping[$sz['gender']][] = $sz['name'];
        }
    }
}
?>
document.addEventListener('DOMContentLoaded', function() {
    // Bulk Edit Variants Logic (Upgraded with SKU Filter)
    const uniqueModels = new Set();
    const uniqueColors = new Set();

    // Scan table to find existing unique values for datalists
    document.querySelectorAll('#variants-table tbody tr').forEach(row => {
        const m = row.querySelector('input[name="variant_models[]"]');
        const c = row.querySelector('input[name="variant_colors[]"]');
        
        if (m && m.value.trim() !== '') uniqueModels.add(m.value.trim());
        if (c && c.value.trim() !== '') uniqueColors.add(c.value.trim());
    });

    // Populate datalists for "GÁN GIÁ TRỊ MỚI"
    const dModels = document.getElementById('datalist-models');
    uniqueModels.forEach(val => { dModels.appendChild(new Option(val, val)); });

    const dColors = document.getElementById('datalist-colors');
    uniqueColors.forEach(val => { dColors.appendChild(new Option(val, val)); });

    // Dynamic standard sizes for "Gán giá trị mới"
    const standardSizes = <?= json_encode($dynamicSizeMapping ?? ['Nam' => [], 'Nữ' => [], 'Trẻ em' => []]) ?>;
    const bulkGenderInput = document.getElementById('bulk-gender-input');
    const dSizes = document.getElementById('datalist-sizes');
    
    function updateNewSizeDatalist() {
        dSizes.innerHTML = '';
        const g = bulkGenderInput.value;
        let sizesToShow = new Set();
        if (g && standardSizes[g]) {
            standardSizes[g].forEach(s => sizesToShow.add(s));
        } else {
            // Show unique raw sizes if no specific gender selected
            document.querySelectorAll('#variants-table tbody tr').forEach(row => {
                const s = row.querySelector('input[name="variant_raw_sizes[]"]');
                if (s && s.value.trim() !== '') sizesToShow.add(s.value.trim());
            });
        }
        sizesToShow.forEach(val => { dSizes.appendChild(new Option(val, val)); });
    }
    bulkGenderInput.addEventListener('change', updateNewSizeDatalist);
    updateNewSizeDatalist();

    // Apply Bulk Update
    const btnBulkApply = document.getElementById('btn-bulk-apply');
    if (btnBulkApply) {
        btnBulkApply.addEventListener('click', function() {
            const filterSku = document.getElementById('filter-sku').value.trim().toLowerCase();

            const newModel = document.getElementById('bulk-model-input').value.trim();
            const newModelPriceInput = document.getElementById('bulk-model-price').value;
            const newModelPrice = newModelPriceInput !== '' ? parseFloat(newModelPriceInput) : null;
            
            const newGender = document.getElementById('bulk-gender-input').value;
            
            const newSize = document.getElementById('bulk-size-input').value.trim();
            const pctSize = parseFloat(document.getElementById('bulk-size-percent').value) || 0;
            
            const newColor = document.getElementById('bulk-color-input').value.trim();
            const pctColor = parseFloat(document.getElementById('bulk-color-percent').value) || 0;
            
            const newPrice = document.getElementById('bulk-price-input').value;
            const newQty = document.getElementById('bulk-qty-input').value;

            if (newModel === '' && newGender === '' && newSize === '' && newColor === '' && newPrice === '' && newQty === '' && newModelPrice === null && pctSize === 0 && pctColor === 0) {
                if (typeof window.showMatrixNotice === 'function') {
                    window.showMatrixNotice('Vui lòng nhập ít nhất 1 giá trị mới hoặc nhập điều chỉnh giá để áp dụng!');
                }
                return;
            }

            const basePrice = parseFloat(document.getElementById('base_price').value) || 0;
            if (newModelPrice !== null) {
                if (newModelPrice > basePrice) {
                    if (typeof window.showMatrixNotice === 'function') {
                        window.showMatrixNotice('Lỗi: Giá của biến thể mẫu không được phép lớn hơn Giá bán mặc định!');
                    }
                    return;
                }
                if (newModelPrice < 0) {
                    if (typeof window.showMatrixNotice === 'function') {
                        window.showMatrixNotice('Lỗi: Giá mẫu không được là số âm!');
                    }
                    return;
                }
            }

            let count = 0;
            document.querySelectorAll('#variants-table tbody tr').forEach(row => {
                const skuInput = row.querySelector('input[name="variant_skus[]"]');
                const rowSku = skuInput ? skuInput.value.trim().toLowerCase() : '';

                const matchSku = filterSku === '' || rowSku.includes(filterSku);

                if (matchSku) {
                    if (newModel !== '') row.querySelector('input[name="variant_models[]"]').value = newModel;
                    if (newGender !== '') row.querySelector('select[name="variant_genders[]"]').value = newGender;
                    if (newSize !== '') row.querySelector('input[name="variant_raw_sizes[]"]').value = newSize;
                    if (newColor !== '') row.querySelector('input[name="variant_colors[]"]').value = newColor;
                    if (newQty !== '') row.querySelector('input[name="variant_qtys[]"]').value = newQty;
                    
                    const priceInput = row.querySelector('input[name="variant_prices[]"]');
                    if (newPrice !== '') {
                        if (parseFloat(newPrice) <= 0) {
                            if (typeof window.showMatrixNotice === 'function') {
                                window.showMatrixNotice('Lỗi: Giá mới không được <= 0!');
                            }
                            return;
                        }
                        priceInput.value = newPrice;
                    } else {
                        let currentPrice = parseFloat(priceInput.value) || 0;
                        
                        // Nếu có thay đổi giá mẫu, dùng giá mẫu mới làm gốc, ngược lại dùng giá hiện tại
                        let baseForPct = newModelPrice !== null ? newModelPrice : currentPrice;
                        // Tính giá sau khi giảm giá size
                        let priceAfterSize = baseForPct * (1 - (pctSize / 100));
                        // Lấy giá sau khi giảm size tính tiếp giảm màu
                        currentPrice = priceAfterSize * (1 - (pctColor / 100));
                        
                        if (currentPrice > basePrice) {
                            currentPrice = basePrice;
                        }
                        
                        if (currentPrice <= 0) {
                            if (typeof window.showMatrixNotice === 'function') {
                                window.showMatrixNotice('Lỗi: Có biến thể bị tính giá <= 0 sau khi áp dụng Giảm giá. Vui lòng kiểm tra lại!');
                            }
                            return;
                        }
                        
                        if (newModelPrice !== null || pctSize !== 0 || pctColor !== 0) {
                            priceInput.value = Math.round(currentPrice);
                        }
                    }
                    
                    row.style.transition = "background-color 0.5s";
                    row.style.backgroundColor = "#d1ecf1";
                    setTimeout(() => { row.style.backgroundColor = ""; }, 1000);
                    count++;
                }
            });
            
            window.updateTotalQuantity();
            if (typeof window.showMatrixNotice === 'function') {
                window.showMatrixNotice('Đã cập nhật hàng loạt thành công cho ' + count + ' biến thể khớp điều kiện!', 'success');
            }
            
            // Clear inputs
            document.getElementById('bulk-model-input').value = '';
            document.getElementById('bulk-model-price').value = '';
            document.getElementById('bulk-gender-input').value = '';
            document.getElementById('bulk-size-input').value = '';
            document.getElementById('bulk-size-percent').value = '';
            document.getElementById('bulk-color-input').value = '';
            document.getElementById('bulk-color-percent').value = '';
            document.getElementById('bulk-price-input').value = '';
            document.getElementById('bulk-qty-input').value = '';
        });
    }
});
</script>