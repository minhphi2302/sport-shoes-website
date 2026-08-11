document.addEventListener('DOMContentLoaded', function() {
    // Tự động in hoa tất cả các ô nhập mã SKU
    document.addEventListener('input', function(e) {
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
        productForm.addEventListener('submit', function(e) {
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
            
            // Bỏ kiểm tra trùng lặp ở client - để backend xử lý
            // Backend sẽ:
            // - Cộng dồn số lượng nếu trùng hoàn toàn (cả % giảm)
            // - Báo lỗi nếu trùng nhưng % giảm khác
            // Client chỉ validate format cơ bản

            variantRows.forEach(row => {
                if (!isValid) return; // Chỉ báo lỗi đầu tiên
                
                const vSku = row.querySelector('input[name="variant_skus[]"]');
                const vPrice = row.querySelector('input[name="variant_prices[]"]');
                const vQty = row.querySelector('input[name="variant_qtys[]"]');

                if (vSku && vSku.value.trim().length === 0) {
                    showError(vSku, 'Thiếu SKU');
                } else if (vPrice && (vPrice.value.trim() === '' || Number(vPrice.value) <= 0)) {
                    // Nếu giá trống, tự điền bằng giá bán mặc định thay vì báo lỗi
                    const fallback = basePriceVal > 0 ? basePriceVal : 0;
                    if (fallback <= 0) {
                        showError(vPrice, 'Giá phải > 0');
                    } else {
                        vPrice.value = fallback;
                    }
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
    
    window.updateTotalQuantity = function() {
        if (!totalQtyInput) return;
        let total = 0;
        document.querySelectorAll('.variant-qty').forEach(input => {
            total += parseInt(input.value) || 0;
        });
        totalQtyInput.value = total;
    };

    if (tableBody) {
        tableBody.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-variant')) {
                e.target.closest('tr').remove();
                window.updateTotalQuantity();
            }
        });

        tableBody.addEventListener('input', function(e) {
            if (e.target.classList.contains('variant-qty')) {
                window.updateTotalQuantity();
            }
        });
        
        window.updateTotalQuantity();
    }

    // 3. Logic Xóa tất cả biến thể
    const btnClearAll = document.getElementById('btn-clear-all-variants');
    if (btnClearAll) {
        btnClearAll.addEventListener('click', function() {
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
});
