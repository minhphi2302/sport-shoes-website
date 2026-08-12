<?php 
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/layouts/header.php'; 
?>
<!-- Breadcrumb -->
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products" class="text-decoration-none text-dark">Sản phẩm</a></li>
                <li class="breadcrumb-item active text-red fw-semibold" aria-current="page"><?= htmlspecialchars($product['name'] ?? 'Chi tiết sản phẩm') ?></li>
            </ol>
        </nav>
    </div>
</div>

    <div class="row bg-white p-4 rounded-4 shadow-sm">
        <!-- Product Image -->
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="bg-light rounded-4 p-4 text-center h-100 d-flex align-items-center justify-content-center">
                <?php 
                    $imgSrc = !empty($product['image_url']) ? '/uploads/' . $product['image_url'] : '/uploads/default-product.jpg';
                    $baseUrl = htmlspecialchars($_ENV['APP_URL'] ?? '');
                ?>
                <img src="<?= $baseUrl . htmlspecialchars($imgSrc) ?>" class="img-fluid rounded product-img-fixed" style="width: 250px; height: 300px; object-fit: cover;" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='<?= $baseUrl ?>/uploads/default-product.jpg'">
            </div>
        </div>

        <!-- BÊN PHẢI: THÔNG TIN SẢN PHẨM & NÚT MUA -->
        <div class="col-lg-6">
            <div class="ps-lg-3">
                <span class="badge bg-dark text-white px-3 py-2 text-uppercase mb-2">Thương hiệu: <?= htmlspecialchars($product['brand_name'] ?? 'Chính Hãng') ?></span>
                <h1 class="product-detail-title mb-3"><?= htmlspecialchars($product['name'] ?? 'Nike Air Zoom Pegasus 39') ?></h1>

                <!-- Đánh giá sao & Tình trạng kho -->
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning me-3">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                        <span class="text-dark fw-bold ms-1">(4.9/5 - 128 đánh giá)</span>
                    </div>
                    <div class="border-start ps-3 <?= ($product['quantity'] ?? 50) > 0 ? 'text-success' : 'text-danger' ?> fw-semibold">
                        <?php if (($product['quantity'] ?? 50) > 0): ?>
                            <i class="fa-solid fa-check-circle me-1"></i> Còn hàng (<?= htmlspecialchars($product['quantity'] ?? 50) ?> sản phẩm)
                        <?php else: ?>
                            <i class="fa-solid fa-circle-xmark me-1"></i> Hết hàng
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Giá bán -->
                <div class="bg-light p-3 rounded-3 mb-4 d-flex align-items-baseline gap-3">
                    <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                        <span class="fs-2 fw-bold text-red"><?= number_format($product['sale_price'], 0, ',', '.') ?>đ</span>
                        <span class="fs-5 text-muted text-decoration-line-through"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                        <span class="badge bg-red fs-6 align-middle" style="transform: translateY(-2px);">Tiết kiệm <?= number_format($product['price'] - $product['sale_price'], 0, ',', '.') ?>đ</span>
                    <?php else: ?>
                        <span class="fs-2 fw-bold text-red"><?= number_format($product['price'] ?? 3000000, 0, ',', '.') ?>đ</span>
                    <?php endif; ?>
                </div>

                <!-- Mô tả ngắn -->
                <p class="text-secondary mb-4">
                    <?= htmlspecialchars($product['description'] ?? 'Mẫu giày thể thao với thiết kế hiện đại, thoáng khí tối đa, đế cao su chống trượt tuyệt đối. Mang lại cảm giác êm ái cho mọi chuyển động hàng ngày.') ?>
                </p>

                <!-- Form chọn Size, Màu & Số lượng -->
                <form id="addToCartForm" action="<?= base_url('cart/add') ?>" method="POST">
                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?? 1 ?>">
                    <input type="hidden" name="variant_id" id="variantIdInput" value="">

                    <script>
                        // Pass variants to JavaScript
                        const productVariants = <?= json_encode($variants ?? []) ?>;
                        const basePrice = <?= (float)($product['price'] ?? 0) ?>;
                        const baseSalePrice = <?= !empty($product['sale_price']) ? (float)$product['sale_price'] : 'null' ?>;
                        
                        console.log('Product Variants:', productVariants);
                        console.log('Base Price:', basePrice);
                    </script>

                    <!-- Chọn Size -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-uppercase fs-7 d-block">Chọn Kích thước (Size EU):</label>
                        <div class="size-btn-group flex-wrap gap-2 d-flex">
                            <?php 
                                $availableSizes = !empty($variants) ? array_unique(array_column($variants, 'size')) : [39, 40, 41, 42, 43, 44];
                                sort($availableSizes);
                            ?>
                            <?php foreach (array_values($availableSizes) as $idx => $s): ?>
                                <input type="radio" class="btn-check" name="size" id="detail_size_<?= $s ?>" value="<?= htmlspecialchars($s) ?>" autocomplete="off" <?= $idx === 0 ? 'checked' : '' ?>>
                                <label class="btn btn-outline-dark" for="detail_size_<?= $s ?>"><?= htmlspecialchars($s) ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Chọn Màu Sắc -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-uppercase fs-7 d-block">Chọn Phối Màu:</label>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <?php 
                                $availableColors = !empty($variants) ? array_unique(array_column($variants, 'color')) : ['Đen', 'Đỏ'];
                            ?>
                            <?php foreach (array_values($availableColors) as $idx => $c): ?>
                                <?php $colorId = 'color_' . md5((string)$c); ?>
                                <input type="radio" class="btn-check" name="color" id="<?= $colorId ?>" value="<?= htmlspecialchars((string)$c) ?>" <?= $idx === 0 ? 'checked' : '' ?>>
                                <label class="btn btn-outline-dark btn-sm rounded-pill" for="<?= $colorId ?>"><?= htmlspecialchars((string)$c) ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Chọn Giới tính -->
                    <?php 
                        // Lấy danh sách giới tính từ variants (model column)
                        $availableGenders = !empty($variants) ? array_unique(array_column($variants, 'model')) : ['Nam'];
                    ?>
                    <?php if (count($availableGenders) > 1): ?>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-uppercase fs-7 d-block">Chọn Giới tính:</label>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <?php foreach (array_values($availableGenders) as $idx => $model): ?>
                                <?php $modelId = 'model_' . md5((string)$model); ?>
                                <input type="radio" class="btn-check" name="gender" id="<?= $modelId ?>" value="<?= htmlspecialchars((string)$model) ?>" <?= $idx === 0 ? 'checked' : '' ?>>
                                <label class="btn btn-outline-dark btn-sm rounded-pill" for="<?= $modelId ?>"><?= htmlspecialchars((string)$model) ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="gender" value="<?= htmlspecialchars($availableGenders[0] ?? 'Nam') ?>">
                    <?php endif; ?>

                    <!-- Chọn Số lượng -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-uppercase fs-7 d-block">Số lượng:</label>
                        <div class="quantity-selector input-group" style="width: 150px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="decrementQuantity()" <?= (($product['quantity'] ?? 50) <= 0) ? 'disabled' : '' ?>><i class="fa-solid fa-minus"></i></button>
                            <input type="number" id="quantityInput" name="quantity" class="form-control text-center" value="1" min="1" max="<?= $product['quantity'] ?? 50 ?>" <?= (($product['quantity'] ?? 50) <= 0) ? 'disabled' : '' ?>>
                            <button class="btn btn-outline-secondary" type="button" onclick="incrementQuantity()" <?= (($product['quantity'] ?? 50) <= 0) ? 'disabled' : '' ?>><i class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>

                    <!-- Nút Thêm Giỏ & Mua Ngay -->
                    <div class="d-grid gap-2 d-md-flex mt-4">
                        <button type="submit" name="action" value="add" class="btn btn-outline-dark-custom btn-lg flex-fill py-3" <?= (($product['quantity'] ?? 50) <= 0) ? 'disabled' : '' ?>>
                            <i class="fa-solid fa-cart-plus me-2"></i> THÊM VÀO GIỎ
                        </button>
                        <button type="submit" name="action" value="buy_now" class="btn btn-red btn-lg flex-fill py-3" <?= (($product['quantity'] ?? 50) <= 0) ? 'disabled' : '' ?>>
                            <i class="fa-solid fa-bolt me-2"></i> MUA NGAY
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PHÍA DƯỚI: TAB MÔ TẢ CHI TIẾT -->
    <div class="mt-5 pt-4">
        <ul class="nav nav-tabs nav-tabs-custom mb-4" id="productTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-uppercase" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc-tab-pane" type="button" role="tab">Mô tả chi tiết</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-uppercase" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs-tab-pane" type="button" role="tab">Thông số kỹ thuật</button>
            </li>
        </ul>
        <div class="tab-content border rounded-3 p-4 bg-white" id="productTabContent">
            <!-- Tab 1: Mô tả -->
            <div class="tab-pane fade show active" id="desc-tab-pane" role="tabpanel">
                <h4>Đặc điểm nổi bật của <?= htmlspecialchars($product['name'] ?? 'sản phẩm') ?></h4>
                <div class="mt-3">
                    <?= !empty($product['description']) ? nl2br(htmlspecialchars($product['description'])) : '<p class="text-muted">Chưa có mô tả chi tiết cho sản phẩm này.</p>' ?>
                </div>
            </div>
            <!-- Tab 2: Thông số kỹ thuật -->
            <div class="tab-pane fade" id="specs-tab-pane" role="tabpanel">
                <table class="table table-bordered table-sm">
                    <tbody>
                        <tr><th>Thương hiệu</th><td><?= htmlspecialchars($product['brand_name'] ?? 'N/A') ?></td></tr>
                        <tr><th>Mã sản phẩm (SKU)</th><td><?= htmlspecialchars($product['sku'] ?? 'N/A') ?></td></tr>
                        <tr><th>Danh mục</th><td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td></tr>
                        <tr><th>Tồn kho</th><td><?= htmlspecialchars($product['quantity'] ?? 0) ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>

<script>
var variantsData = <?= json_encode($variants ?? []) ?>;
var selectedState = {
    model: '',
    gender: '',
    color: '',
    size: ''
};

function updateSizeButtons() {
    var gender = selectedState.gender;
    var sizeMapping = <?= json_encode($dynamicSizeMapping ?? ['Nam' => [], 'Nữ' => [], 'Trẻ em' => []]) ?>;
    var validSizes = sizeMapping[gender] || [];
    
    document.querySelectorAll('.opt-size').forEach(function(b) {
        if (validSizes.includes(b.getAttribute('data-val'))) {
            b.style.display = 'inline-block';
        } else {
            b.style.display = 'none';
        }
    });
}

function updateAvailableOptions() {
    // 1. Model: chỉ kiểm tra xem model này có bất kỳ biến thể nào còn hàng không (không bị ràng buộc bởi các lựa chọn bên dưới)
    document.querySelectorAll('.opt-model').forEach(function(btn) {
        var val = btn.getAttribute('data-val');
        var isAvailable = variantsData.some(v => 
            parseInt(v.quantity) > 0 &&
            (v.model || 'Mặc định') === val
        );
        btn.disabled = !isAvailable;
        btn.style.opacity = isAvailable ? '1' : '0.4';
    });
    
    // 2. Gender: kiểm tra dựa trên Model đã chọn
    document.querySelectorAll('.opt-gender').forEach(function(btn) {
        var val = btn.getAttribute('data-val');
        var isAvailable = variantsData.some(v => 
            parseInt(v.quantity) > 0 &&
            v.parsed_gender === val &&
            (!selectedState.model || (v.model || 'Mặc định') === selectedState.model)
        );
        btn.disabled = !isAvailable;
        btn.style.opacity = isAvailable ? '1' : '0.4';
    });
    
    // 3. Color: kiểm tra dựa trên Model và Gender đã chọn
    document.querySelectorAll('.opt-color').forEach(function(btn) {
        var val = btn.getAttribute('data-val');
        var isAvailable = variantsData.some(v => 
            parseInt(v.quantity) > 0 &&
            v.color === val &&
            (!selectedState.model || (v.model || 'Mặc định') === selectedState.model) &&
            (!selectedState.gender || v.parsed_gender === selectedState.gender)
        );
        btn.disabled = !isAvailable;
        btn.style.opacity = isAvailable ? '1' : '0.4';
    });
    
    // 4. Size: kiểm tra dựa trên tất cả Model, Gender, và Color đã chọn
    document.querySelectorAll('.opt-size').forEach(function(btn) {
        if (btn.style.display !== 'none') {
            var val = btn.getAttribute('data-val');
            var isAvailable = variantsData.some(v => 
                parseInt(v.quantity) > 0 &&
                v.parsed_size === val &&
                (!selectedState.model || (v.model || 'Mặc định') === selectedState.model) &&
                (!selectedState.gender || v.parsed_gender === selectedState.gender) &&
                (!selectedState.color || v.color === selectedState.color)
            );
            btn.disabled = !isAvailable;
            btn.style.opacity = isAvailable ? '1' : '0.4';
        }
    });
}

function selectOption(type, btn) {
    if (btn.disabled) return;
    
    if (btn.classList.contains('btn-primary')) {
        btn.classList.remove('btn-primary', 'text-white');
        btn.classList.add('btn-outline-secondary');
        selectedState[type] = '';
    } else {
        document.querySelectorAll('.opt-' + type).forEach(function(b) {
            b.classList.remove('btn-primary', 'text-white');
            b.classList.add('btn-outline-secondary');
        });
        
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-primary', 'text-white');
        selectedState[type] = btn.getAttribute('data-val');
    }
    
    if (type === 'gender') {
        selectedState.size = '';
        document.querySelectorAll('.opt-size').forEach(function(b) {
            b.classList.remove('btn-primary', 'text-white');
            b.classList.add('btn-outline-secondary');
            if (!selectedState.gender) b.style.display = 'none';
        });
        if (selectedState.gender) {
            updateSizeButtons();
        }
    }
    
    updateAvailableOptions();
    checkVariantCombination();
}

function checkVariantCombination() {
    var matchedVariant = null;
    
    if (selectedState.model && selectedState.gender && selectedState.color && selectedState.size) {
        matchedVariant = variantsData.find(function(v) {
            var m = v.model || 'Mặc định';
            return m === selectedState.model && v.parsed_gender === selectedState.gender && v.color === selectedState.color && v.parsed_size === selectedState.size;
        });
    }

    var qtyInput = document.getElementById('quantity');
    var btnSubmit = document.getElementById('btnSubmit');
    
    if (matchedVariant) {
        document.getElementById('selectedVariantId').value = matchedVariant.id;
        
        var price = parseFloat(matchedVariant.price) || parseFloat(<?= $product['price'] ?>);
        document.getElementById('productPrice').innerText = price.toLocaleString('vi-VN') + 'đ';
        var origEl = document.getElementById('productOriginalPrice');
        if (origEl) origEl.parentElement.style.display = 'none';
        
        if (matchedVariant.sku) {
            document.getElementById('productSku').innerText = matchedVariant.sku;
        }
        
        var maxQty = parseInt(matchedVariant.quantity) || 0;
        if (maxQty > 0) {
            qtyInput.max = maxQty;
            qtyInput.disabled = false;
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-cart-plus me-2"></i> Thêm vào giỏ hàng';
            if (parseInt(qtyInput.value) > maxQty) {
                qtyInput.value = maxQty;
            }
        } else {
            qtyInput.disabled = true;
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = 'Hết hàng';
            qtyInput.value = 0;
        }
    } else {
        document.getElementById('selectedVariantId').value = 0;
        qtyInput.disabled = true;
        btnSubmit.disabled = true;
        
        if (selectedState.model && selectedState.gender && selectedState.color && selectedState.size) {
            btnSubmit.innerHTML = 'Hết hàng';
        } else {
            btnSubmit.innerHTML = 'Vui lòng chọn đủ Mẫu, Đối tượng, Màu, Size';
        }
    }
}

// ========== VARIANT SELECTION & PRICE UPDATE ==========
function updateVariantPrice() {
    const selectedSize = document.querySelector('input[name="size"]:checked')?.value;
    const selectedColor = document.querySelector('input[name="color"]:checked')?.value;
    const selectedGender = document.querySelector('input[name="gender"]:checked')?.value || document.querySelector('input[name="gender"]')?.value;
    
    console.log('Selected:', { size: selectedSize, color: selectedColor, gender: selectedGender });
    
    if (!selectedSize || !selectedColor) {
        console.warn('Size or color not selected');
        return;
    }
    
    // Find matching variant (check model/gender if exists)
    const variant = productVariants.find(v => {
        const sizeMatch = String(v.size).trim() === String(selectedSize).trim();
        const colorMatch = String(v.color).trim() === String(selectedColor).trim();
        const genderMatch = !selectedGender || String(v.model).trim() === String(selectedGender).trim();
        return sizeMatch && colorMatch && genderMatch;
    });
    
    console.log('Found variant:', variant);
    
    if (variant) {
        // Update variant_id hidden input
        document.getElementById('variantIdInput').value = variant.variant_id;
        
        // Update price display
        const variantPrice = parseFloat(variant.price) || basePrice;
        const priceContainer = document.querySelector('.bg-light.p-3.rounded-3');
        
        let priceHTML;
        if (baseSalePrice && variantPrice < basePrice) {
            const savings = basePrice - variantPrice;
            priceHTML = `
                <span class="fs-2 fw-bold text-red">${formatPrice(variantPrice)}đ</span>
                <span class="fs-5 text-muted text-decoration-line-through">${formatPrice(basePrice)}đ</span>
                <span class="badge bg-red fs-6 align-middle" style="transform: translateY(-2px);">Tiết kiệm ${formatPrice(savings)}đ</span>
            `;
        } else {
            priceHTML = `<span class="fs-2 fw-bold text-red">${formatPrice(variantPrice)}đ</span>`;
        }
        
        if (priceContainer) {
            priceContainer.innerHTML = priceHTML;
        }
        
        // Update stock info
        const stockQty = parseInt(variant.quantity) || 0;
        const quantityInput = document.getElementById('quantityInput');
        if (quantityInput) {
            quantityInput.max = stockQty;
            quantityInput.disabled = stockQty <= 0;
            if (quantityInput.value > stockQty) {
                quantityInput.value = Math.max(1, stockQty);
            }
        }
        
        // Update stock display
        const stockDisplay = document.querySelector('.border-start.ps-3');
        if (stockDisplay) {
            if (stockQty > 0) {
                stockDisplay.className = 'border-start ps-3 text-success fw-semibold';
                stockDisplay.innerHTML = `<i class="fa-solid fa-check-circle me-1"></i> Còn hàng (${stockQty} sản phẩm)`;
            } else {
                stockDisplay.className = 'border-start ps-3 text-danger fw-semibold';
                stockDisplay.innerHTML = `<i class="fa-solid fa-circle-xmark me-1"></i> Hết hàng`;
            }
        }
        
        // Enable/disable buttons
        const addToCartBtn = document.querySelector('button[value="add"]');
        const buyNowBtn = document.querySelector('button[value="buy_now"]');
        if (addToCartBtn) addToCartBtn.disabled = stockQty <= 0;
        if (buyNowBtn) buyNowBtn.disabled = stockQty <= 0;
        
        console.log('Price updated to:', variantPrice, 'Stock:', stockQty);
    } else {
        console.warn('No variant found for selected combination');
        document.getElementById('variantIdInput').value = '';
    }
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price);
}

// Attach event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Initial update
    updateVariantPrice();
    
    // Listen to size/color/gender changes
    document.querySelectorAll('input[name="size"], input[name="color"], input[name="gender"]').forEach(input => {
        input.addEventListener('change', updateVariantPrice);
    });
    
    // Handle form submit
    const form = document.getElementById('addToCartForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const action = e.submitter?.value;
            
            if (action === 'buy_now') {
                // For buy now, let form submit normally to redirect to checkout
                return true;
            }
            
            if (action === 'add') {
                // For add to cart, use AJAX
                e.preventDefault();
                
                const formData = new FormData(form);
                formData.append('action', 'add');
                
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    
                    // Clone response để có thể đọc text trước khi parse JSON
                    return response.text().then(text => {
                        console.log('Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            throw new Error('Invalid JSON response: ' + text.substring(0, 100));
                        }
                    });
                })
                .then(data => {
                    console.log('Parsed data:', data);
                    if (data.success) {
                        // Show success toast - will auto-update badge via footer.php's showCartToast
                        if (typeof showCartToast === 'function') {
                            showCartToast(data.message, 'success');
                        }
                    } else {
                        // Show error toast
                        if (typeof showCartToast === 'function') {
                            showCartToast(data.message || 'Có lỗi xảy ra', 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('AJAX Error:', error);
                    // Show error toast only, don't use alert
                    if (typeof showCartToast === 'function') {
                        showCartToast('Không thể thêm sản phẩm vào giỏ hàng', 'error');
                    }
                });
            }
        });
    }
});

        var firstValid = variantsData.find(v => parseInt(v.quantity) > 0) || variantsData[0];
        
        var mBtn = document.querySelector('.opt-model[data-val="'+(firstValid.model || 'Mặc định')+'"]');
        var gBtn = document.querySelector('.opt-gender[data-val="'+firstValid.parsed_gender+'"]');
        var cBtn = document.querySelector('.opt-color[data-val="'+firstValid.color+'"]');
        var sBtn = document.querySelector('.opt-size[data-val="'+firstValid.parsed_size+'"]');
        
        if (mBtn) mBtn.click();
        if (gBtn) gBtn.click();
        if (cBtn) cBtn.click();
        if (sBtn) {
            sBtn.style.display = 'inline-block';
            sBtn.click();
        }
        
        updateAvailableOptions();
    } else {
        var baseQty = <?= $product['quantity'] ?>;
        if (baseQty <= 0) {
            document.getElementById('quantity').disabled = true;
            document.getElementById('btnSubmit').disabled = true;
            document.getElementById('btnSubmit').innerHTML = 'Hết hàng';
        }
    }
});
</script>
