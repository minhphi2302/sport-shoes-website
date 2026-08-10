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
                            <input type="text" class="form-control" name="sku" value="<?= htmlspecialchars($product['sku'] ?? '') ?>" placeholder="VD: NIKE-AF1">
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

                    <!-- KHU VỰC TẠO MA TRẬN PHÂN LOẠI -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Tạo Biến Thể Sản Phẩm</h5>
                        
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <!-- 1. Nhập các Mẫu -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">1. Mẫu giày và Giá cộng thêm (VND)</label>
                                    <div id="models-container">
                                        <div class="input-group input-group-sm mb-2 model-row">
                                            <input type="text" class="form-control model-name" placeholder="Tên mẫu (VD: Bản Thường)" value="Mặc định">
                                            <span class="input-group-text">Δ Giá</span>
                                            <input type="number" class="form-control model-delta" placeholder="± Giá" value="0">
                                            <button class="btn btn-outline-danger btn-remove-row" type="button"><i class="bi bi-x"></i></button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-add-model">+ Thêm Mẫu Khác</button>
                                </div>

                                <!-- 2. Tích chọn Size phân theo Đối tượng -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">2. Chọn Size và Chênh lệch giá</label>
                                    
                                    <ul class="nav nav-pills nav-sm mb-2" id="sizeTab" role="tablist">
                                        <li class="nav-item"><button class="nav-link active py-1 px-3" data-bs-toggle="tab" data-bs-target="#size-men" type="button">Giày Nam</button></li>
                                        <li class="nav-item"><button class="nav-link py-1 px-3" data-bs-toggle="tab" data-bs-target="#size-women" type="button">Giày Nữ</button></li>
                                        <li class="nav-item"><button class="nav-link py-1 px-3" data-bs-toggle="tab" data-bs-target="#size-kids" type="button">Trẻ Em</button></li>
                                    </ul>

                                    <div class="tab-content border rounded p-3 bg-white">
                                        <!-- Size Nam -->
                                        <div class="tab-pane fade show active" id="size-men">
                                            <div class="row g-2">
                                                <?php foreach (['39','40','41','42','43','44','45'] as $s): ?>
                                                    <div class="col-md-3">
                                                        <div class="input-group input-group-sm">
                                                            <div class="input-group-text"><input class="form-check-input mt-0 chk-size" type="checkbox" value="Nam - <?= $s ?>"></div>
                                                            <span class="input-group-text bg-white" style="width:50px; justify-content:center"><?= $s ?></span>
                                                            <input type="number" class="form-control size-delta" placeholder="Δ Giá" value="0">
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <!-- Size Nữ -->
                                        <div class="tab-pane fade" id="size-women">
                                            <div class="row g-2">
                                                <?php foreach (['35','36','37','38','39'] as $s): ?>
                                                    <div class="col-md-3">
                                                        <div class="input-group input-group-sm">
                                                            <div class="input-group-text"><input class="form-check-input mt-0 chk-size" type="checkbox" value="Nữ - <?= $s ?>"></div>
                                                            <span class="input-group-text bg-white" style="width:50px; justify-content:center"><?= $s ?></span>
                                                            <input type="number" class="form-control size-delta" placeholder="Δ Giá" value="0">
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <!-- Size Trẻ em -->
                                        <div class="tab-pane fade" id="size-kids">
                                            <div class="row g-2">
                                                <?php foreach (['28','29','30','31','32','33','34'] as $s): ?>
                                                    <div class="col-md-3">
                                                        <div class="input-group input-group-sm">
                                                            <div class="input-group-text"><input class="form-check-input mt-0 chk-size" type="checkbox" value="Trẻ em - <?= $s ?>"></div>
                                                            <span class="input-group-text bg-white" style="width:50px; justify-content:center"><?= $s ?></span>
                                                            <input type="number" class="form-control size-delta" placeholder="Δ Giá" value="0">
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. Tích chọn nhiều Màu -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">3. Chọn các Màu có sẵn và Chênh lệch giá</label>
                                    <div class="row g-2">
                                        <?php foreach (['Trắng','Đen','Đỏ','Xanh dương','Xanh lá','Vàng','Xám'] as $c): ?>
                                            <div class="col-md-3">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-text"><input class="form-check-input mt-0 chk-color" type="checkbox" value="<?= $c ?>"></div>
                                                    <span class="input-group-text bg-white" style="width:80px; justify-content:center"><?= $c ?></span>
                                                    <input type="number" class="form-control color-delta" placeholder="Δ Giá" value="0">
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <!-- 4. Nhập Số Lượng & Nút Thêm -->
                                <div class="row align-items-end mb-2">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">4. Số lượng</label>
                                        <input type="number" class="form-control" id="common_qty" min="0">
                                    </div>
                                    <div class="col-md-9">
                                        <button type="button" class="btn btn-success w-100 fw-bold" id="btn-generate-matrix">
                                            <i class="bi bi-plus-circle me-1"></i> Thêm các biến thể vừa chọn vào danh sách
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
                </div>

                <!-- Cột thông tin phụ bên phải -->
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
    const basePriceInput = document.getElementById('base_price');

    // 1.5 Xử lý thêm Model
    document.getElementById('btn-add-model').addEventListener('click', function() {
        const container = document.getElementById('models-container');
        const row = document.createElement('div');
        row.className = 'input-group input-group-sm mb-2 model-row';
        row.innerHTML = `
            <input type="text" class="form-control model-name" placeholder="Tên mẫu (VD: Bản Thường)">
            <span class="input-group-text">Δ Giá</span>
            <input type="number" class="form-control model-delta" placeholder="± Giá" value="0">
            <button class="btn btn-outline-danger btn-remove-row" type="button"><i class="bi bi-x"></i></button>
        `;
        container.appendChild(row);
    });

    document.getElementById('models-container').addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-row')) {
            e.target.closest('.model-row').remove();
        }
    });

    function createSlug(str) {
        return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^\w\s-]/g, '').replace(/[\s_-]+/g, '-').replace(/^-+|-+$/g, '').toUpperCase();
    }

    // 2. Tạo ma trận biến thể động (Chênh lệch giá)
    document.getElementById('btn-generate-matrix').addEventListener('click', function() {
        const commonQty = document.getElementById('common_qty').value.trim();
        if (commonQty === '') {
            alert('Vui lòng nhập Số lượng ở Bước 4 trước khi thêm!');
            document.getElementById('common_qty').focus();
            return;
        }

        const skuCha = document.querySelector('input[name="sku"]').value.trim() || 'SKU';
        const basePrice = parseFloat(basePriceInput.value) || 0;

        const models = [];
        document.querySelectorAll('.model-row').forEach(row => {
            const name = row.querySelector('.model-name').value.trim();
            if (name) {
                models.push({
                    name: name,
                    delta: parseFloat(row.querySelector('.model-delta').value) || 0
                });
            }
        });
        if (models.length === 0) models.push({name: 'Mặc định', delta: 0});

        const selectedSizes = [];
        document.querySelectorAll('.chk-size:checked').forEach(cb => {
            const row = cb.closest('.input-group');
            selectedSizes.push({
                name: cb.value,
                delta: parseFloat(row.querySelector('.size-delta').value) || 0
            });
        });

        const selectedColors = [];
        document.querySelectorAll('.chk-color:checked').forEach(cb => {
            const row = cb.closest('.input-group');
            selectedColors.push({
                name: cb.value,
                delta: parseFloat(row.querySelector('.color-delta').value) || 0
            });
        });

        if (selectedSizes.length === 0 || selectedColors.length === 0) {
            alert('Vui lòng chọn ít nhất 1 Size và 1 Màu sắc!');
            return;
        }

        models.forEach(modelObj => {
            selectedSizes.forEach(sizeObj => {
                selectedColors.forEach(colorObj => {
                    let calculatedPrice = basePrice + modelObj.delta + sizeObj.delta + colorObj.delta;
                    if (calculatedPrice < 0) calculatedPrice = 0;

                    const key = `${modelObj.name}-${sizeObj.name}-${colorObj.name}`;
                    const variantSku = `${skuCha}-${createSlug(modelObj.name)}-${createSlug(colorObj.name)}-${createSlug(sizeObj.name)}`;
                    
                    if (!document.querySelector(`tr[data-key="${key}"]`)) {
                        const tr = document.createElement('tr');
                        tr.setAttribute('data-key', key);
                        
                        const genderPart = sizeObj.name.split(' - ')[0];
                        const sizePart = sizeObj.name.split(' - ')[1] || sizeObj.name;

                        tr.innerHTML = `
                            <td><input type="text" name="variant_skus[]" class="form-control form-control-sm" value="${variantSku}"></td>
                            <td><input type="text" name="variant_models[]" class="form-control form-control-sm" value="${modelObj.name}"></td>
                            <td>
                                <select name="variant_genders[]" class="form-select form-select-sm">
                                    <option value="Nam" ${genderPart === 'Nam' ? 'selected' : ''}>Nam</option>
                                    <option value="Nữ" ${genderPart === 'Nữ' ? 'selected' : ''}>Nữ</option>
                                    <option value="Trẻ em" ${genderPart === 'Trẻ em' ? 'selected' : ''}>Trẻ em</option>
                                </select>
                            </td>
                            <td><input type="text" name="variant_raw_sizes[]" class="form-control form-control-sm" value="${sizePart}"></td>
                            <td><input type="text" name="variant_colors[]" class="form-control form-control-sm" value="${colorObj.name}"></td>
                            <td><input type="number" name="variant_prices[]" class="form-control form-control-sm variant-price" value="${calculatedPrice}" min="0"></td>
                            <td><input type="number" name="variant_qtys[]" class="form-control form-control-sm variant-qty" value="${commonQty}" min="0" required></td>
                            <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove-variant"><i class="bi bi-trash"></i></button></td>
                        `;
                        const tableBody = document.querySelector('#variants-table tbody');
                        tableBody.appendChild(tr);
                    }
                });
            });
        });

        // Bỏ chọn tất cả sau khi đã ném xuống bảng
        document.querySelectorAll('.chk-size:checked, .chk-color:checked').forEach(cb => cb.checked = false);
        document.getElementById('common_qty').value = '';
        
        window.updateTotalQuantity();
    });
});
</script>