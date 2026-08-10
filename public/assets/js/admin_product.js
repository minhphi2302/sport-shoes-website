document.addEventListener('DOMContentLoaded', function() {
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
            if (isValid && sku.value.trim().length === 0) {
                showError(sku, 'Vui lòng không để trống mã SKU.');
            } else if (isValid && sku.value.trim().length > 50) {
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
                if (variantGlobalError) {
                    variantGlobalError.innerText = 'Vui lòng tạo ít nhất 1 biến thể sản phẩm (Mẫu, Size, Màu sắc).';
                    variantGlobalError.style.display = 'block';
                }
                isValid = false;
                firstInvalid = document.getElementById('variants-table');
            }

            variantRows.forEach(row => {
                if (!isValid) return; // Chỉ báo lỗi đầu tiên
                
                const vSku = row.querySelector('input[name="variant_skus[]"]');
                const vPrice = row.querySelector('input[name="variant_prices[]"]');
                const vQty = row.querySelector('input[name="variant_qtys[]"]');

                if (vSku && vSku.value.trim().length === 0) {
                    showError(vSku, 'Thiếu SKU');
                } else if (vPrice && (vPrice.value.trim() === '' || Number(vPrice.value) < 0)) {
                    showError(vPrice, 'Sai giá');
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
});
