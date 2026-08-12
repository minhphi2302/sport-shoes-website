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

<div class="container pb-5">
    <div class="row g-5">
        
        <!-- BÊN TRÁI: GALLERY ÁNH -->
        <div class="col-lg-6">
            <div class="product-detail-gallery">
                <!-- Ảnh lớn chính -->
                <?php 
                    $imgSrc = get_product_image_url($product['image_url'] ?? null, $product['product_id'] ?? 1);
                ?>
                <img id="mainProductImg" src="<?= htmlspecialchars($imgSrc) ?>" class="main-img" alt="<?= htmlspecialchars($product['name'] ?? 'Product') ?>" onerror="this.src='<?= base_url('image/slide/slide1.avif') ?>'">
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

    <!-- PHÍA DƯỚI: TAB MÔ TẢ CHI TIẾT & ĐÁNH GIÁ -->
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
                    <?= !empty($product['description']) ? nl2br($product['description']) : '<p class="text-muted">Chưa có mô tả chi tiết cho sản phẩm này.</p>' ?>
                </div>
            </div>

            <!-- Tab 2: Thông số -->
            <div class="tab-pane fade" id="specs-tab-pane" role="tabpanel">
                <table class="table table-striped table-bordered mb-0">
                    <tbody>
                        <tr><th width="30%">Thương hiệu</th><td><?= htmlspecialchars($product['brand_name'] ?? 'Đang cập nhật') ?></td></tr>
                        <tr><th>Mã sản phẩm</th><td><?= htmlspecialchars($product['sku'] ?? 'Đang cập nhật') ?></td></tr>
                        <tr><th>Giới tính</th><td><?= htmlspecialchars($product['gender'] === 'male' ? 'Nam' : ($product['gender'] === 'female' ? 'Nữ' : 'Unisex')) ?></td></tr>
                        <tr><th>Trạng thái</th><td><?= htmlspecialchars($product['status'] === 'active' ? 'Đang bán' : 'Ngừng kinh doanh') ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SẢN PHẨM LIÊN QUAN -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="mt-5 pt-4">
            <h3 class="section-title">Sản phẩm liên quan</h3>
            <div class="row g-4">
                <?php foreach ($relatedProducts as $rel): ?>
                    <div class="col-6 col-md-3">
                        <div class="product-card">
                            <div class="product-img-wrapper">
                                <?php 
                                    $fallbackImgRel = 'image/slide/slide' . (($rel['product_id'] % 3) + 1) . '.avif';
                                    $imgSrcRel = !empty($rel['image_url']) ? $rel['image_url'] : $fallbackImgRel;
                                    
                                    $isNew = isset($rel['created_at']) && (strtotime($rel['created_at']) > strtotime('-7 days'));
                                    $hasSale = (($rel['sale_price'] ?? 0) > 0 && $rel['sale_price'] < $rel['price']);
                                ?>
                                <?php if ($isNew): ?>
                                    <div class="badge-ribbon badge-ribbon-black">NEW</div>
                                <?php endif; ?>
                                <?php if ($hasSale): ?>
                                    <?php $discountPercent = round((($rel['price'] - $rel['sale_price']) / $rel['price']) * 100); ?>
                                    <div class="badge-ribbon badge-ribbon-red" <?= $isNew ? 'style="left: 43px;"' : '' ?>><small>-</small><?= $discountPercent ?>%</div>
                                <?php endif; ?>

                                <img src="<?= htmlspecialchars(base_url($imgSrcRel)) ?>" alt="<?= htmlspecialchars($rel['name']) ?>">
                                <div class="product-center-action">
                                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products/<?= $rel['product_id'] ?>" class="search-icon-btn" title="Xem chi tiết">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="product-body">
                                <span class="product-brand-tag"><?= htmlspecialchars($rel['brand_name'] ?? 'Chính Hãng') ?></span>
                                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products/<?= $rel['product_id'] ?>" class="product-title mb-1"><?= htmlspecialchars($rel['name']) ?></a>
                                <?php if (!empty($rel['sku'])): ?>
                                    <div class="text-muted mb-2" style="font-size: 0.85rem;"><?= htmlspecialchars($rel['sku']) ?></div>
                                <?php endif; ?>
                                <div class="product-price-box">
                                    <?php if (($rel['sale_price'] ?? 0) > 0 && $rel['sale_price'] < $rel['price']): ?>
                                        <span class="product-price text-danger"><?= number_format($rel['sale_price'], 0, ',', '.') ?>đ</span>
                                        <span class="product-price-old text-decoration-line-through text-muted ms-2" style="font-size: 0.85rem;"><?= number_format($rel['price'], 0, ',', '.') ?>đ</span>
                                    <?php else: ?>
                                        <span class="product-price text-danger"><?= number_format($rel['price'], 0, ',', '.') ?>đ</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
function changeImage(src) {
    document.getElementById('mainProductImg').src = src;
    document.querySelectorAll('.thumbnail-img').forEach(img => img.classList.remove('active'));
    event.target.classList.add('active');
}

function incrementQuantity() {
    let input = document.getElementById('quantityInput');
    input.value = parseInt(input.value) + 1;
}

function decrementQuantity() {
    let input = document.getElementById('quantityInput');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
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
                
                // Create FormData và lấy giá trị cụ thể của các radio buttons
                const formData = new FormData();
                formData.append('product_id', document.querySelector('input[name="product_id"]').value);
                formData.append('variant_id', document.getElementById('variantIdInput').value);
                formData.append('quantity', document.getElementById('quantityInput').value);
                formData.append('action', 'add');
                
                // Lấy giá trị radio button đã chọn
                const selectedSize = document.querySelector('input[name="size"]:checked');
                if (selectedSize) {
                    formData.append('size', selectedSize.value);
                }
                
                const selectedColor = document.querySelector('input[name="color"]:checked');
                if (selectedColor) {
                    formData.append('color', selectedColor.value);
                }
                
                const selectedGender = document.querySelector('input[name="gender"]:checked') || document.querySelector('input[name="gender"]');
                if (selectedGender) {
                    formData.append('gender', selectedGender.value);
                }
                
                console.log('Sending FormData:');
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }
                
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
                    
                    // Nếu response không phải JSON, đọc text trước khi parse
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            console.log('Raw response:', text);
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('JSON parse error:', e);
                                throw new Error('Invalid JSON response: ' + text.substring(0, 100));
                            }
                        });
                    }
                })
                .then(data => {
                    console.log('Parsed data:', data);
                    if (data.success) {
                        showCartToast(data.message, 'success');
                        
                        // Update cart count
                        const cartCountEl = document.querySelector('.badge-badge-count');
                        if (cartCountEl && data.cartCount !== undefined) {
                            cartCountEl.textContent = data.cartCount;
                        }
                    } else {
                        showCartToast(data.error || data.message || 'Có lỗi xảy ra', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showCartToast('Không thể thêm sản phẩm vào giỏ hàng: ' + error.message, 'error');
                });
            }
        });
    }
});

</script>

<?php
require __DIR__ . '/layouts/footer.php';
?>
