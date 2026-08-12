document.addEventListener('DOMContentLoaded', function () {
    // Tự động in hoa tất cả các ô nhập mã SKU
    document.addEventListener('input', function (e) {
        if (e.target.matches('input[name="sku"], input[name="variant_skus[]"]')) {
            const start = e.target.selectionStart;
            const end = e.target.selectionEnd;
            e.target.value = e.target.value.toUpperCase();
            if (start !== null && end !== null) {
                e.target.setSelectionRange(start, end);
            }
        }
    });

    // 1. Form Validation cho cả trang Thêm và Sửa Sản Phẩm
    const productForm = document.getElementById('product-form');
    if (productForm) {
        productForm.addEventListener('submit', function (e) {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            let isValid = true;
            let firstInvalid = null;

            const showError = (input, message) => {
                if (!isValid) return;
                isValid = false;
                firstInvalid = input;
                input.classList.add('is-invalid');
                const err = document.createElement('div');
                err.className = 'invalid-feedback fw-bold';
                err.innerText = message;
                input.parentElement.appendChild(err);

                // Tự động ẩn lỗi ô nhập sau 5 giây (5000ms)
                setTimeout(() => {
                    input.classList.remove('is-invalid');
                    err.remove();
                }, 5000);
            };

            const name = document.querySelector('input[name="name"]');
            if (name.value.trim().length === 0) {
                showError(name, 'Vui lòng không để trống tên sản phẩm.');
            } else if (name.value.trim().length > 255) {
                showError(name, 'Tên sản phẩm không được vượt quá 255 ký tự.');
            }

            const sku = document.querySelector('input[name="sku"]');
            if (sku) sku.value = sku.value.trim().toUpperCase();
            if (isValid && sku && sku.value.length === 0) {
                showError(sku, 'Vui lòng không để trống mã SKU.');
            } else if (isValid && sku && sku.value.length > 50) {
                showError(sku, 'Mã SKU không được vượt quá 50 ký tự.');
            }

            const price = document.querySelector('input[name="price"]');
            if (isValid && price.value.trim() === '') {
                showError(price, 'Vui lòng nhập giá bán mặc định.');
            } else if (isValid && Number(price.value) <= 0) {
                showError(price, 'Giá bán phải lớn hơn 0.');
            }

            const salePrice = document.querySelector('input[name="sale_price"]');
            if (isValid && salePrice.value.trim() !== '') {
                if (Number(salePrice.value) < 0) {
                    showError(salePrice, 'Giá khuyến mãi không được âm.');
                } else if (Number(salePrice.value) > Number(price.value)) {
                    showError(salePrice, 'Giá khuyến mãi không được lớn hơn giá bán mặc định.');
                }
            }

            const categoryId = document.querySelector('select[name="category_id"]');
            if (isValid && categoryId.value === '') {
                showError(categoryId, 'Vui lòng chọn danh mục cho sản phẩm.');
            }

            const brandId = document.querySelector('select[name="brand_id"]');
            if (isValid && brandId.value === '') {
                showError(brandId, 'Vui lòng chọn thương hiệu.');
            }

            // Require image for Create page
            if (window.location.href.includes('create')) {
                const image = document.querySelector('input[name="image"]');
                if (isValid && image && image.files.length === 0) {
                    showError(image, 'Vui lòng tải lên hình ảnh sản phẩm mới.');
                }
            }

            // Validate Biến thể
            const variantRows = document.querySelectorAll('#variants-table tbody tr');
            const variantGlobalError = document.getElementById('variant-global-error');
            if (variantGlobalError) variantGlobalError.style.display = 'none';

            if (isValid && variantRows.length === 0) {
                const msg = 'Vui lòng tạo ít nhất 1 biến thể sản phẩm (Mẫu, Size, Màu sắc).';
                if (typeof window.showMatrixNotice === 'function') {
                    window.showMatrixNotice(msg);
                } else if (variantGlobalError) {
                    variantGlobalError.innerText = msg;
                    variantGlobalError.style.display = 'block';
                }
                isValid = false;
                firstInvalid = document.getElementById('variants-table');
            }

            const basePriceInput = document.querySelector('input[name="price"]');
            const basePriceVal = basePriceInput ? Number(basePriceInput.value) : 0;
            
            const seenVariants = new Set();

            variantRows.forEach(row => {
                if (!isValid) return; // Chỉ báo lỗi đầu tiên
                
                // Validate duplicate variants
                const vModelInput = row.querySelector('input[name="variant_models[]"]');
                const vGenderInput = row.querySelector('select[name="variant_genders[]"]');
                const vRawSizeInput = row.querySelector('input[name="variant_raw_sizes[]"]');
                const vColorInput = row.querySelector('input[name="variant_colors[]"]');

                const vModel = vModelInput ? vModelInput.value.trim() : 'Mặc định';
                const vGender = vGenderInput ? vGenderInput.value.trim() : '';
                const vRawSize = vRawSizeInput ? vRawSizeInput.value.trim() : '';
                const vColor = vColorInput ? vColorInput.value.trim() : '';

                const uniqueKey = `${vModel.toLowerCase()}-${vGender.toLowerCase()}-${vRawSize.toLowerCase()}-${vColor.toLowerCase()}`;

                if (seenVariants.has(uniqueKey)) {
                    const msg = `Lỗi: Có biến thể bị trùng lặp (Mẫu: ${vModel}, Đối tượng: ${vGender}, Size: ${vRawSize}, Màu: ${vColor}). Không thể lưu!`;
                    if (typeof window.showMatrixNotice === 'function') {
                        window.showMatrixNotice(msg);
                    } else if (variantGlobalError) {
                        variantGlobalError.innerText = msg;
                        variantGlobalError.style.display = 'block';
                    }
                    isValid = false;
                    firstInvalid = vModelInput;
                    row.style.transition = "background-color 0.5s";
                    row.style.backgroundColor = "#f8d7da"; // highlight red
                    setTimeout(() => { row.style.backgroundColor = ""; }, 5000);
                    return;
                }
                seenVariants.add(uniqueKey);
                
                const vSku = row.querySelector('input[name="variant_skus[]"]');
                const vPrice = row.querySelector('input[name="variant_prices[]"]');
                const vQty = row.querySelector('input[name="variant_qtys[]"]');

                if (vSku && vSku.value.trim().length === 0) {
                    showError(vSku, 'Thiếu SKU');
                } else if (vPrice && (vPrice.value.trim() === '' || Number(vPrice.value) <= 0)) {
                    showError(vPrice, 'Giá phải > 0');
                } else if (vQty && (vQty.value.trim() === '' || Number(vQty.value) < 0 || !Number.isInteger(Number(vQty.value)))) {
                    showError(vQty, 'Sai SL');
                }
            });

            if (!isValid) {
                e.preventDefault();
                if (firstInvalid) firstInvalid.focus();
            }
        });
    }

    // 2. Logic cập nhật Bảng biến thể chung
    const tableBody = document.querySelector('#variants-table tbody');
    const totalQtyInput = document.getElementById('total_quantity');

    window.updateTotalQuantity = function () {
        if (!totalQtyInput) return;
        let total = 0;
        document.querySelectorAll('.variant-qty').forEach(input => {
            total += parseInt(input.value) || 0;
        });
        totalQtyInput.value = total;
    };

    if (tableBody) {
        tableBody.addEventListener('click', function (e) {
            if (e.target.closest('.btn-remove-variant')) {
                e.target.closest('tr').remove();
                window.updateTotalQuantity();
            }
        });

        tableBody.addEventListener('input', function (e) {
            if (e.target.classList.contains('variant-qty')) {
                window.updateTotalQuantity();
            }
        });

        window.updateTotalQuantity();
    }

    // 3. Logic Xóa tất cả biến thể
    const btnClearAll = document.getElementById('btn-clear-all-variants');
    if (btnClearAll) {
        btnClearAll.addEventListener('click', function () {
            const tbody = document.querySelector('#variants-table tbody');
            if (!tbody || tbody.querySelectorAll('tr').length === 0) {
                if (typeof window.showMatrixNotice === 'function') {
                    window.showMatrixNotice('Danh sách biến thể đang trống!');
                }
                return;
            }
            if (confirm('Bạn có chắc chắn muốn xóa toàn bộ biến thể có trong danh sách?')) {
                tbody.innerHTML = '';
                if (typeof window.hideMatrixNotice === 'function') {
                    window.hideMatrixNotice();
                }
                if (typeof window.updateTotalQuantity === 'function') {
                    window.updateTotalQuantity();
                }
            }
        });
    }

    // 3.5 Logic Thêm 1 biến thể thủ công
    const btnAddSingle = document.getElementById('btn-add-single-variant');
    if (btnAddSingle) {
        btnAddSingle.addEventListener('click', function() {
            const skuInput = document.querySelector('input[name="sku"]');
            const baseSku = (skuInput ? skuInput.value.trim().toUpperCase() : 'SKU') || 'SKU';
            const basePriceInput = document.querySelector('input[name="price"]');
            const basePrice = basePriceInput ? (parseFloat(basePriceInput.value) || 0) : 0;

            const tbody = document.querySelector('#variants-table tbody');
            if (!tbody) return;

            const rowCount = tbody.querySelectorAll('tr').length + 1;
            const defaultSku = `${baseSku}-VAR-${rowCount}`;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" name="variant_skus[]" class="form-control form-control-sm" value="${defaultSku}" placeholder="SKU"></td>
                <td><input type="text" name="variant_models[]" class="form-control form-control-sm" value="Mặc định"></td>
                <td>
                    <select name="variant_genders[]" class="form-select form-select-sm">
                        <option value="Nam">Nam</option>
                        <option value="Nữ" selected>Nữ</option>
                        <option value="Trẻ em">Trẻ em</option>
                    </select>
                </td>
                <td><input type="text" name="variant_raw_sizes[]" class="form-control form-control-sm" value="40" placeholder="Size"></td>
                <td><input type="text" name="variant_colors[]" class="form-control form-control-sm" value="Đen" placeholder="Màu"></td>
                <td><input type="number" name="variant_prices[]" class="form-control form-control-sm variant-price" value="${basePrice}" min="0"></td>
                <td><input type="number" name="variant_qtys[]" class="form-control form-control-sm variant-qty" value="10" min="0" required></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove-variant"><i class="bi bi-trash"></i></button></td>
            `;
            tbody.appendChild(tr);

            if (typeof window.updateTotalQuantity === 'function') {
                window.updateTotalQuantity();
            }

            // Highlight row vừa thêm
            tr.style.transition = "background-color 0.5s";
            tr.style.backgroundColor = "#d1ecf1";
            setTimeout(() => { tr.style.backgroundColor = ""; }, 1000);
        });
    }

    // 4. Tự động ẩn các thông báo alert trên trang sau 5 giây
    document.querySelectorAll('.alert:not(#matrix-error-alert)').forEach(alertEl => {
        setTimeout(() => {
            alertEl.style.transition = 'opacity 0.5s ease-out';
            alertEl.style.opacity = '0';
            setTimeout(() => {
                alertEl.style.display = 'none';
            }, 500);
        }, 5000);
    });
});
