<!-- KHU VỰC TẠO MA TRẬN PHÂN LOẠI -->
<div class="mb-4">
    <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Tạo Biến Thể Sản Phẩm (Matrix)</h5>
    
    <div class="card bg-light border-0 mb-3">
        <div class="card-body">
            <!-- 1. Nhập các Mẫu -->
            <div class="mb-3">
                <label class="form-label fw-semibold">1. Mẫu giày (Tùy chọn) và Giá cộng thêm (VND)</label>
                <div id="models-container">
                    <!-- Không có mẫu mặc định -->
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="btn-add-model">+ Thêm Mẫu Khác</button>
            </div>

            <!-- 2. Tích chọn Size phân theo Đối tượng -->
            <div class="mb-3">
                <label class="form-label fw-semibold">2. Chọn Size và Giảm giá (%)</label>
                
                <ul class="nav nav-pills nav-sm mb-2" id="sizeTab" role="tablist">
                    <li class="nav-item"><button class="nav-link active py-1 px-3" data-bs-toggle="tab" data-bs-target="#size-men" type="button">Giày Nam</button></li>
                    <li class="nav-item"><button class="nav-link py-1 px-3" data-bs-toggle="tab" data-bs-target="#size-women" type="button">Giày Nữ</button></li>
                    <li class="nav-item"><button class="nav-link py-1 px-3" data-bs-toggle="tab" data-bs-target="#size-kids" type="button">Trẻ Em</button></li>
                </ul>

                <div class="tab-content border rounded p-3 bg-white">
                    <!-- Size Nam -->
                    <div class="tab-pane fade show active" id="size-men">
                        <div class="row g-2">
                            <?php if (isset($sizes)) foreach ($sizes as $s): if ($s['gender'] !== 'Nam') continue; ?>
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text"><input class="form-check-input mt-0 chk-size" type="checkbox" value="Nam - <?= htmlspecialchars($s['name']) ?>"></div>
                                        <span class="input-group-text bg-white" style="width:50px; justify-content:center"><?= htmlspecialchars($s['name']) ?></span>
                                        <input type="number" class="form-control size-percent" placeholder="0" value="0" min="0" max="100" title="Phần trăm giảm giá (0-100)">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Size Nữ -->
                    <div class="tab-pane fade" id="size-women">
                        <div class="row g-2">
                            <?php if (isset($sizes)) foreach ($sizes as $s): if ($s['gender'] !== 'Nữ') continue; ?>
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text"><input class="form-check-input mt-0 chk-size" type="checkbox" value="Nữ - <?= htmlspecialchars($s['name']) ?>"></div>
                                        <span class="input-group-text bg-white" style="width:50px; justify-content:center"><?= htmlspecialchars($s['name']) ?></span>
                                        <input type="number" class="form-control size-percent" placeholder="0" value="0" min="0" max="100" title="Phần trăm giảm giá (0-100)">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Size Trẻ em -->
                    <div class="tab-pane fade" id="size-kids">
                        <div class="row g-2">
                            <?php if (isset($sizes)) foreach ($sizes as $s): if ($s['gender'] !== 'Trẻ em') continue; ?>
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text"><input class="form-check-input mt-0 chk-size" type="checkbox" value="Trẻ em - <?= htmlspecialchars($s['name']) ?>"></div>
                                        <span class="input-group-text bg-white" style="width:50px; justify-content:center"><?= htmlspecialchars($s['name']) ?></span>
                                        <input type="number" class="form-control size-percent" placeholder="0" value="0" min="0" max="100" title="Phần trăm giảm giá (0-100)">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Tích chọn nhiều Màu -->
            <div class="mb-3">
                <label class="form-label fw-semibold">3. Chọn các Màu có sẵn và Giảm giá (%)</label>
                <div class="row g-2">
                    <?php if (isset($colors)) foreach ($colors as $c): ?>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <div class="input-group-text"><input class="form-check-input mt-0 chk-color" type="checkbox" value="<?= htmlspecialchars($c['name']) ?>"></div>
                                <span class="input-group-text bg-white" style="width:80px; justify-content:center"><?= htmlspecialchars($c['name']) ?></span>
                                <input type="number" class="form-control color-percent" placeholder="0" value="0" min="0" max="100" title="Phần trăm giảm giá (0-100)">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- 4. Nhập Số Lượng & Nút Thêm -->
            <div class="row align-items-end mb-2">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">4. Số lượng mặc định</label>
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
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const basePriceInput = document.getElementById('base_price');

    // 1.5 Xử lý thêm Model
    const btnAddModel = document.getElementById('btn-add-model');
    if (btnAddModel) {
        btnAddModel.addEventListener('click', function() {
            const container = document.getElementById('models-container');
            const row = document.createElement('div');
            row.className = 'input-group input-group-sm mb-2 model-row';
            row.innerHTML = `
                <input type="text" class="form-control model-name" placeholder="Tên mẫu (VD: Bản Thường)">
                <span class="input-group-text">Giá (VNĐ)</span>
                <input type="number" class="form-control model-delta" placeholder="Để trống = Giá gốc" value="">
                <button class="btn btn-outline-danger btn-remove-row" type="button"><i class="bi bi-x"></i></button>
            `;
            container.appendChild(row);
        });
    }

    const modelsContainer = document.getElementById('models-container');
    if (modelsContainer) {
        modelsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-row')) {
                e.target.closest('.model-row').remove();
            }
        });
    }

    function createSlug(str) {
        return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^\w\s-]/g, '').replace(/[\s_-]+/g, '-').replace(/^-+|-+$/g, '').toUpperCase();
    }

    // 2. Tạo ma trận biến thể động (Chênh lệch giá)
    const btnGenerate = document.getElementById('btn-generate-matrix');
    if (btnGenerate) {
        btnGenerate.addEventListener('click', function() {
            const commonQty = document.getElementById('common_qty').value.trim();
            if (commonQty === '') {
                alert('Vui lòng nhập Số lượng ở Bước 4 trước khi thêm!');
                document.getElementById('common_qty').focus();
                return;
            }

            const skuInput = document.querySelector('input[name="sku"]');
            const skuCha = (skuInput ? skuInput.value.trim() : '') || 'SKU';
            const basePrice = basePriceInput ? (parseFloat(basePriceInput.value) || 0) : 0;
            if (isNaN(basePrice) || basePrice <= 0) {
                alert('Vui lòng nhập Giá bán mặc định hợp lệ ở phần thông tin cơ bản trước khi tạo biến thể!');
                if (basePriceInput) basePriceInput.focus();
                return;
            }

            let hasError = false;

            // Lấy Models và validate
            const models = [];
            document.querySelectorAll('.model-row').forEach(row => {
                if (hasError) return;
                const name = row.querySelector('.model-name').value.trim();
                if (name) {
                    const priceInput = row.querySelector('.model-delta');
                    let mPrice = basePrice; // Nếu để trống thì giữ nguyên giá gốc
                    if (priceInput.value.trim() !== '') {
                        mPrice = parseFloat(priceInput.value);
                    }
                    
                    // Kiểm tra giá mẫu không được lớn hơn giá bán mặc định và không được âm
                    if (mPrice > basePrice) {
                        alert('Lỗi: Giá của biến thể mẫu không được phép lớn hơn Giá bán mặc định!');
                        priceInput.focus();
                        hasError = true;
                        return;
                    } else if (mPrice < 0) {
                        alert('Lỗi: Giá mẫu không được là số âm!');
                        priceInput.focus();
                        hasError = true;
                        return;
                    }

                    models.push({
                        name: name,
                        price: mPrice
                    });
                }
            });
            if (hasError) return;
            
            if (models.length === 0) models.push({name: '', price: basePrice});

            // Lấy Sizes
            const selectedSizes = [];
            document.querySelectorAll('.chk-size:checked').forEach(cb => {
                if (hasError) return;
                const row = cb.closest('.input-group');
                const pctInput = row.querySelector('.size-percent');
                let pct = parseFloat(pctInput.value) || 0;
                
                selectedSizes.push({
                    name: cb.value,
                    percent: pct
                });
            });
            if (hasError) return;

            // Lấy Colors
            const selectedColors = [];
            document.querySelectorAll('.chk-color:checked').forEach(cb => {
                if (hasError) return;
                const row = cb.closest('.input-group');
                const pctInput = row.querySelector('.color-percent');
                let pct = parseFloat(pctInput.value) || 0;
                
                selectedColors.push({
                    name: cb.value,
                    percent: pct
                });
            });
            if (hasError) return;

            if (selectedSizes.length === 0 || selectedColors.length === 0) {
                alert('Vui lòng chọn ít nhất 1 Size và 1 Màu sắc!');
                return;
            }

            // Sinh dữ liệu
            models.forEach(modelObj => {
                selectedSizes.forEach(sizeObj => {
                    selectedColors.forEach(colorObj => {
                        if (hasError) return;

                        // Tính giá sau khi giảm giá size
                        let priceAfterSize = modelObj.price * (1 - (sizeObj.percent / 100));
                        // Lấy giá sau khi giảm size tính tiếp giảm màu
                        let calculatedPrice = priceAfterSize * (1 - (colorObj.percent / 100));
                        
                        // Ràng buộc giá biến thể không vượt quá giá bán gốc
                        if (calculatedPrice > basePrice) {
                            calculatedPrice = basePrice;
                        }
                        
                        if (calculatedPrice <= 0) {
                            alert(`Lỗi: Giá bán cuối cùng của biến thể (Mẫu: ${modelObj.name || 'Mặc định'}, Size: ${sizeObj.name}, Màu: ${colorObj.name}) phải lớn hơn 0. Vui lòng giảm bớt % giảm giá!`);
                            hasError = true;
                            return;
                        }
                        
                        const key = `${modelObj.name}-${sizeObj.name}-${colorObj.name}`;
                        
                        // Rút gọn SKU nếu không có Mẫu
                        let variantSku = '';
                        if (modelObj.name) {
                            variantSku = `${skuCha}-${createSlug(modelObj.name)}-${createSlug(colorObj.name)}-${createSlug(sizeObj.name)}`;
                        } else {
                            variantSku = `${skuCha}-${createSlug(colorObj.name)}-${createSlug(sizeObj.name)}`;
                        }
                        
                        if (!document.querySelector(`tr[data-key="${key}"]`)) {
                            const tr = document.createElement('tr');
                            tr.setAttribute('data-key', key);
                            
                            const genderPart = sizeObj.name.split(' - ')[0];
                            const sizePart = sizeObj.name.split(' - ')[1] || sizeObj.name;
                            const displayModel = modelObj.name ? modelObj.name : 'Mặc định';

                            tr.innerHTML = `
                                <td><input type="text" name="variant_skus[]" class="form-control form-control-sm" value="${variantSku}"></td>
                                <td><input type="text" name="variant_models[]" class="form-control form-control-sm" value="${displayModel}"></td>
                                <td>
                                    <select name="variant_genders[]" class="form-select form-select-sm">
                                        <option value="Nam" ${genderPart === 'Nam' ? 'selected' : ''}>Nam</option>
                                        <option value="Nữ" ${genderPart === 'Nữ' ? 'selected' : ''}>Nữ</option>
                                        <option value="Trẻ em" ${genderPart === 'Trẻ em' ? 'selected' : ''}>Trẻ em</option>
                                    </select>
                                </td>
                                <td><input type="text" name="variant_raw_sizes[]" class="form-control form-control-sm" value="${sizePart}"></td>
                                <td><input type="text" name="variant_colors[]" class="form-control form-control-sm" value="${colorObj.name}"></td>
                                <td><input type="number" name="variant_prices[]" class="form-control form-control-sm variant-price" value="${Math.round(calculatedPrice)}" min="0"></td>
                                <td><input type="number" name="variant_qtys[]" class="form-control form-control-sm variant-qty" value="${commonQty}" min="0" required></td>
                                <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove-variant"><i class="bi bi-trash"></i></button></td>
                            `;
                            const tableBody = document.querySelector('#variants-table tbody');
                            if (tableBody) tableBody.appendChild(tr);
                        }
                    });
                });
            });

            if (hasError) return;

            // Bỏ chọn tất cả sau khi đã ném xuống bảng
            document.querySelectorAll('.chk-size:checked, .chk-color:checked').forEach(cb => cb.checked = false);
            document.getElementById('common_qty').value = '';
            
            if (typeof window.updateTotalQuantity === 'function') {
                window.updateTotalQuantity();
            }
        });
    }
});
</script>
