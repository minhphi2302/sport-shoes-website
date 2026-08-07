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
                            <input type="text" class="form-control" name="sku" value="<?= htmlspecialchars($product['sku'] ?? '') ?>" placeholder="VD: NIKE-AF1">
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

                        <!-- BULK UPDATE VARIANTS (Nâng cấp với Checkbox đa chọn) -->
                        <div class="card border-info mb-3">
                            <div class="card-header bg-info text-white fw-bold py-2">
                                <i class="bi bi-magic me-1"></i> Cập nhật nhanh hàng loạt (Gom nhóm)
                            </div>
                            <div class="card-body bg-light p-3">
                                <!-- Hàng 1: Điều kiện lọc (Checkbox) -->
                                <div class="fw-bold text-secondary mb-2 small">1. BỘ LỌC (Tích chọn để lọc nhiều giá trị, không tích = Lọc tất cả)</div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold text-primary mb-1">Lọc theo Mẫu:</label>
                                        <div id="filter-models" class="border rounded p-2 bg-white" style="max-height: 150px; overflow-y: auto; font-size: 0.85rem;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-primary mb-1">Lọc Đối tượng & Size (Tham chiếu bảng dưới):</label>
                                        <div id="filter-gender-sizes" class="border rounded p-2 bg-white" style="max-height: 150px; overflow-y: auto; font-size: 0.85rem;">
                                            <!-- Dynamically generated -->
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold text-primary mb-1">Lọc Màu sắc:</label>
                                        <div id="filter-colors" class="border rounded p-2 bg-white" style="max-height: 150px; overflow-y: auto; font-size: 0.85rem;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Hàng 2: Giá trị gán mới -->
                                <div class="fw-bold text-secondary mb-2 small mt-3">2. GÁN GIÁ TRỊ MỚI (Bỏ trống = Giữ nguyên)</div>
                                <div class="row g-2 align-items-end mb-2">
                                    <div class="col-md-3">
                                        <label class="form-label small mb-1">Mẫu mới & Đ/c Giá</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" id="bulk-model-input" list="datalist-models" placeholder="Tên mẫu mới">
                                            <input type="number" class="form-control" id="bulk-model-delta" placeholder="± Giá" style="max-width: 80px;">
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
                                        <label class="form-label small mb-1">Size mới & Đ/c Giá</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" id="bulk-size-input" list="datalist-sizes" placeholder="VD: 40">
                                            <input type="number" class="form-control" id="bulk-size-delta" placeholder="± Giá" style="max-width: 80px;">
                                        </div>
                                        <datalist id="datalist-sizes"></datalist>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small mb-1">Màu sắc mới & Đ/c Giá</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" id="bulk-color-input" list="datalist-colors" placeholder="Tên màu">
                                            <input type="number" class="form-control" id="bulk-color-delta" placeholder="± Giá" style="max-width: 80px;">
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
document.addEventListener('DOMContentLoaded', function() {
    // Bulk Edit Variants Logic (Upgraded with Checkboxes & Dynamic Sizes)
    const uniqueModels = new Set();
    const uniqueGenders = new Set();
    const uniqueSizes = new Set();
    const uniqueColors = new Set();
    const existingGenderSizes = [];

    // Scan table to find existing unique values
    document.querySelectorAll('#variants-table tbody tr').forEach(row => {
        const m = row.querySelector('input[name="variant_models[]"]');
        const g = row.querySelector('select[name="variant_genders[]"]');
        const s = row.querySelector('input[name="variant_raw_sizes[]"]');
        const c = row.querySelector('input[name="variant_colors[]"]');
        
        if (m && m.value.trim() !== '') uniqueModels.add(m.value.trim());
        if (g && g.value.trim() !== '') uniqueGenders.add(g.value.trim());
        if (c && c.value.trim() !== '') uniqueColors.add(c.value.trim());
        
        if (g && s && s.value.trim() !== '') {
            const genderVal = g.value.trim();
            const sizeVal = s.value.trim();
            const combinedSize = genderVal + ' - ' + sizeVal;
            uniqueSizes.add(combinedSize);
            existingGenderSizes.push({ gender: genderVal, size: sizeVal, combined: combinedSize });
        }
    });

    const createCheckboxes = (containerId, items, groupName) => {
        const container = document.getElementById(containerId);
        container.innerHTML = '';
        if (items.length === 0) {
            container.innerHTML = '<span class="text-muted small">Không có dữ liệu</span>';
            return;
        }
        items.forEach(val => {
            const div = document.createElement('div');
            div.className = 'form-check mb-1 filter-checkbox-wrapper';
            const safeVal = val.replace(/"/g, '&quot;');
            div.innerHTML = `
                <input class="form-check-input filter-checkbox" type="checkbox" data-group="${groupName}" value="${safeVal}" id="chk_${groupName}_${safeVal}">
                <label class="form-check-label text-truncate w-100" for="chk_${groupName}_${safeVal}" title="${safeVal}" style="cursor: pointer;">
                    ${safeVal}
                </label>
            `;
            container.appendChild(div);
        });
    };

    createCheckboxes('filter-models', Array.from(uniqueModels), 'model');
    createCheckboxes('filter-colors', Array.from(uniqueColors), 'color');

    // Dynamically build Gender and Size group
    const genderSizeMap = {};
    existingGenderSizes.forEach(item => {
        if (!genderSizeMap[item.gender]) genderSizeMap[item.gender] = new Set();
        genderSizeMap[item.gender].add(item.size);
    });

    const gsContainer = document.getElementById('filter-gender-sizes');
    gsContainer.innerHTML = '';
    
    if (Object.keys(genderSizeMap).length === 0) {
        gsContainer.innerHTML = '<span class="text-muted small">Không có dữ liệu</span>';
    } else {
        Object.keys(genderSizeMap).forEach(gen => {
            const sizes = Array.from(genderSizeMap[gen]).sort((a, b) => {
                let na = parseInt(a), nb = parseInt(b);
                if (!isNaN(na) && !isNaN(nb)) return na - nb;
                return a.localeCompare(b);
            });
            const safeGen = gen.replace(/"/g, '&quot;');
            
            let sizeHtml = '';
            sizes.forEach(sz => {
                const safeSz = sz.replace(/"/g, '&quot;');
                sizeHtml += `
                    <div class="form-check form-check-inline me-2 mb-1">
                        <input class="form-check-input filter-size-cb" type="checkbox" data-gender="${safeGen}" value="${safeSz}" id="chk_s_${safeGen}_${safeSz}">
                        <label class="form-check-label" for="chk_s_${safeGen}_${safeSz}" style="cursor:pointer;">${safeSz}</label>
                    </div>
                `;
            });

            const row = document.createElement('div');
            row.className = 'd-flex align-items-start mb-2 pb-2 border-bottom filter-gender-row';
            row.innerHTML = `
                <div class="form-check me-3" style="min-width: 90px;">
                    <input class="form-check-input filter-gender-cb" type="checkbox" value="${safeGen}" id="chk_g_${safeGen}">
                    <label class="form-check-label fw-bold text-dark" for="chk_g_${safeGen}" style="cursor:pointer;">${safeGen}</label>
                </div>
                <div class="size-group-container" id="sizes_for_${safeGen}" style="display: none; flex: 1; flex-wrap: wrap;">
                    ${sizeHtml}
                </div>
            `;
            gsContainer.appendChild(row);
        });

        // Add event listeners to show/hide sizes when gender is checked
        gsContainer.querySelectorAll('.filter-gender-cb').forEach(cb => {
            cb.addEventListener('change', function() {
                const sizeContainer = document.getElementById(`sizes_for_${this.value.replace(/"/g, '&quot;')}`);
                if (this.checked) {
                    sizeContainer.style.display = 'flex';
                } else {
                    sizeContainer.style.display = 'none';
                    // Uncheck all sizes inside
                    sizeContainer.querySelectorAll('input').forEach(scb => scb.checked = false);
                }
            });
        });
    }

    // Populate datalists for "GÁN GIÁ TRỊ MỚI"
    const dModels = document.getElementById('datalist-models');
    uniqueModels.forEach(val => { dModels.appendChild(new Option(val, val)); });

    const dColors = document.getElementById('datalist-colors');
    uniqueColors.forEach(val => { dColors.appendChild(new Option(val, val)); });

    // Dynamic standard sizes for "Gán giá trị mới"
    const standardSizes = {
        'Nam': ['38','39','40','41','42','43','44','45'],
        'Nữ': ['35','36','37','38','39'],
        'Trẻ em': ['28','29','30','31','32','33','34']
    };
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
            const getCheckedValues = (selector) => {
                const checkboxes = document.querySelectorAll(selector);
                return Array.from(checkboxes).map(cb => cb.value);
            };

            const fModels = getCheckedValues('.filter-checkbox[data-group="model"]:checked');
            const fColors = getCheckedValues('.filter-checkbox[data-group="color"]:checked');
            
            const checkedGenderCbs = document.querySelectorAll('.filter-gender-cb:checked');
            const fGenderLogic = {}; 
            checkedGenderCbs.forEach(gCb => {
                const gen = gCb.value;
                const sizeCbs = document.querySelectorAll(`.filter-size-cb[data-gender="${gen}"]:checked`);
                fGenderLogic[gen] = Array.from(sizeCbs).map(sCb => sCb.value);
            });
            const isGenderFilterActive = checkedGenderCbs.length > 0;

            const newModel = document.getElementById('bulk-model-input').value.trim();
            const deltaModel = parseFloat(document.getElementById('bulk-model-delta').value) || 0;
            
            const newGender = document.getElementById('bulk-gender-input').value;
            
            const newSize = document.getElementById('bulk-size-input').value.trim();
            const deltaSize = parseFloat(document.getElementById('bulk-size-delta').value) || 0;
            
            const newColor = document.getElementById('bulk-color-input').value.trim();
            const deltaColor = parseFloat(document.getElementById('bulk-color-delta').value) || 0;
            
            const newPrice = document.getElementById('bulk-price-input').value;
            const newQty = document.getElementById('bulk-qty-input').value;

            if (newModel === '' && newGender === '' && newSize === '' && newColor === '' && newPrice === '' && newQty === '' && deltaModel === 0 && deltaSize === 0 && deltaColor === 0) {
                alert('Vui lòng nhập ít nhất 1 giá trị mới hoặc nhập điều chỉnh giá để áp dụng!');
                return;
            }

            let count = 0;
            document.querySelectorAll('#variants-table tbody tr').forEach(row => {
                const model = row.querySelector('input[name="variant_models[]"]').value.trim();
                const gender = row.querySelector('select[name="variant_genders[]"]').value.trim();
                const size = row.querySelector('input[name="variant_raw_sizes[]"]').value.trim();
                const color = row.querySelector('input[name="variant_colors[]"]').value.trim();

                const matchModel = fModels.length === 0 || fModels.includes(model);
                const matchColor = fColors.length === 0 || fColors.includes(color);

                let matchGenderSize = true;
                if (isGenderFilterActive) {
                    if (fGenderLogic.hasOwnProperty(gender)) {
                        const allowedSizes = fGenderLogic[gender];
                        if (allowedSizes.length > 0 && !allowedSizes.includes(size)) {
                            matchGenderSize = false;
                        }
                    } else {
                        matchGenderSize = false;
                    }
                }

                if (matchModel && matchGenderSize && matchColor) {
                    if (newModel !== '') row.querySelector('input[name="variant_models[]"]').value = newModel;
                    if (newGender !== '') row.querySelector('select[name="variant_genders[]"]').value = newGender;
                    if (newSize !== '') row.querySelector('input[name="variant_raw_sizes[]"]').value = newSize;
                    if (newColor !== '') row.querySelector('input[name="variant_colors[]"]').value = newColor;
                    if (newQty !== '') row.querySelector('input[name="variant_qtys[]"]').value = newQty;
                    
                    const priceInput = row.querySelector('input[name="variant_prices[]"]');
                    if (newPrice !== '') {
                        priceInput.value = newPrice;
                    } else {
                        let currentPrice = parseFloat(priceInput.value) || 0;
                        currentPrice = currentPrice + deltaModel + deltaSize + deltaColor;
                        if (currentPrice < 0) currentPrice = 0;
                        if (deltaModel !== 0 || deltaSize !== 0 || deltaColor !== 0) {
                            priceInput.value = currentPrice;
                        }
                    }
                    
                    row.style.transition = "background-color 0.5s";
                    row.style.backgroundColor = "#d1ecf1";
                    setTimeout(() => { row.style.backgroundColor = ""; }, 1000);
                    count++;
                }
            });
            
            window.updateTotalQuantity();
            alert('Đã cập nhật hàng loạt thành công cho ' + count + ' biến thể khớp điều kiện!');
            
            // Clear inputs
            document.getElementById('bulk-model-input').value = '';
            document.getElementById('bulk-model-delta').value = '';
            document.getElementById('bulk-gender-input').value = '';
            document.getElementById('bulk-size-input').value = '';
            document.getElementById('bulk-size-delta').value = '';
            document.getElementById('bulk-color-input').value = '';
            document.getElementById('bulk-color-delta').value = '';
            document.getElementById('bulk-price-input').value = '';
            document.getElementById('bulk-qty-input').value = '';
        });
    }
});
</script>