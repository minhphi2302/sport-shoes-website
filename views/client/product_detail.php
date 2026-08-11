<?php 
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/layouts/header.php'; 
?>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products">Sản phẩm</a></li>
            <li class="breadcrumb-item"><a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products?category_id=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

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

        <!-- Product Info -->
        <div class="col-md-6 ps-md-5">
            <div class="d-flex gap-2 mb-2">
                <div class="badge bg-primary px-3 py-2 rounded-pill"><?= htmlspecialchars($product['brand_name']) ?></div>
                <div class="badge bg-secondary px-3 py-2 rounded-pill">
                    <?php
                        $g = $product['gender'] ?? 'unisex';
                        if ($g === 'male') echo 'Giày Nam';
                        elseif ($g === 'female') echo 'Giày Nữ';
                        else echo 'Unisex';
                    ?>
                </div>
            </div>
            
            <h1 class="fw-bold mb-3"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="text-muted mb-4">Mã sản phẩm: <span class="fw-semibold text-dark" id="productSku"><?= htmlspecialchars($product['sku']) ?></span></p>

            <div class="mb-4">
                <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                    <h2 class="price-sale mb-0 display-5 fw-bold" id="productPrice"><?= number_format($product['sale_price'], 0, ',', '.') ?>đ</h2>
                    <h4 class="price-original text-muted"><del id="productOriginalPrice"><?= number_format($product['price'], 0, ',', '.') ?>đ</del></h4>
                <?php else: ?>
                    <h2 class="price-regular mb-0 display-5 fw-bold text-primary" id="productPrice"><?= number_format($product['price'], 0, ',', '.') ?>đ</h2>
                    <h4 class="price-original text-muted" style="display:none;"><del id="productOriginalPrice"><?= number_format($product['price'], 0, ',', '.') ?>đ</del></h4>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <span class="d-inline-block bg-light rounded px-3 py-2 <?= $product['quantity'] > 0 ? 'text-success' : 'text-danger' ?> fw-semibold">
                    <i class="bi bi-box-seam"></i> 
                    <?= $product['quantity'] > 0 ? 'Còn hàng' : 'Hết hàng' ?>
                </span>
            </div>

            <?php if(!\App\Core\Auth::check() || \App\Core\Auth::user()['role'] !== 'admin'): ?>
            <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/cart/add" method="POST" class="mb-5">
                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                
                <div class="row g-3 mb-4">
                        <?php
                            $uniqueGenders = ['Nam', 'Nữ', 'Trẻ em'];
                            $uniqueModels = [];
                            $uniqueSizes = [];
                            $uniqueColors = [];
                            if (isset($variants) && !empty($variants)) {
                                foreach ($variants as &$v) {
                                    $m = $v['model'] ?? 'Mặc định';
                                    $uniqueModels[$m] = true;
                                    
                                    $sizeParts = explode(' - ', $v['size']);
                                    if (count($sizeParts) >= 2) {
                                        $g = trim($sizeParts[0]);
                                        $s = trim(implode(' - ', array_slice($sizeParts, 1)));
                                    } else {
                                        $g = 'Chung';
                                        $s = $v['size'];
                                    }
                                    
                                    $v['parsed_gender'] = $g;
                                    $v['parsed_size'] = $s;
                                    
                                    $uniqueSizes[$s] = true;
                                    $uniqueColors[$v['color']] = true;
                                }
                                unset($v);
                            }
                            $uniqueModels = array_keys($uniqueModels);
                            $uniqueColors = array_keys($uniqueColors);
                            $allSizes = [];
                            $dynamicSizeMapping = ['Nam' => [], 'Nữ' => [], 'Trẻ em' => []];
                            if (isset($sizes)) {
                                foreach ($sizes as $sz) {
                                    $allSizes[] = $sz['name'];
                                    if (isset($dynamicSizeMapping[$sz['gender']])) {
                                        $dynamicSizeMapping[$sz['gender']][] = $sz['name'];
                                    }
                                }
                                $allSizes = array_unique($allSizes);
                            }
                        ?>
                        <?php if (isset($variants) && !empty($variants)): ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">1. Mẫu giày:</label>
                                <div class="d-flex flex-wrap gap-2" id="group-model">
                                    <?php foreach ($uniqueModels as $m): ?>
                                        <button type="button" class="btn btn-outline-secondary opt-model" onclick="selectOption('model', this)" data-val="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">2. Đối tượng:</label>
                                <div class="d-flex flex-wrap gap-2" id="group-gender">
                                    <?php foreach ($uniqueGenders as $g): ?>
                                        <button type="button" class="btn btn-outline-secondary opt-gender" onclick="selectOption('gender', this)" data-val="<?= htmlspecialchars($g) ?>"><?= htmlspecialchars($g) ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">3. Màu sắc:</label>
                                <div class="d-flex flex-wrap gap-2" id="group-color">
                                    <?php foreach ($uniqueColors as $c): ?>
                                        <button type="button" class="btn btn-outline-secondary opt-color" onclick="selectOption('color', this)" data-val="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">4. Kích cỡ (Size):</label>
                                <div class="d-flex flex-wrap gap-2" id="group-size">
                                    <?php foreach ($allSizes as $s): ?>
                                        <button type="button" class="btn btn-outline-secondary opt-size" onclick="selectOption('size', this)" data-val="<?= htmlspecialchars($s) ?>" style="display:none;"><?= htmlspecialchars($s) ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Phân loại sản phẩm:</label>
                                <div>
                                    <button type="button" class="btn btn-outline-secondary variant-btn" 
                                            data-id="0" 
                                            data-qty="<?= $product['quantity'] ?>" 
                                            data-price="<?= $product['sale_price'] ?: $product['price'] ?>"
                                            data-sku="<?= htmlspecialchars($product['sku']) ?>"
                                            <?= $product['quantity'] <= 0 ? 'disabled' : '' ?>
                                            <?= $product['quantity'] <= 0 ? 'style="opacity: 0.5;"' : '' ?>>
                                        Mặc định
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                        <input type="hidden" name="variant_id" id="selectedVariantId" value="0">
                    </div>
                </div>

                <div class="row g-3 align-items-end">
                    <div class="col-auto">
                        <label for="quantity" class="form-label fw-bold">Số lượng:</label>
                        <input type="number" class="form-control text-center bg-light border-0" id="quantity" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>" style="width: 80px;" <?= $product['quantity'] <= 0 ? 'disabled' : '' ?>>
                    </div>
                    <div class="col">
                        <button type="submit" id="btnSubmit" class="btn btn-primary btn-lg w-100 rounded-3 shadow-sm" <?= $product['quantity'] <= 0 ? 'disabled' : '' ?>>
                            <i class="bi bi-cart-plus me-2"></i> Thêm vào giỏ hàng
                        </button>
                    </div>
                </div>
            </form>
            <?php else: ?>
            <div class="alert alert-info mb-5">
                Tài khoản admin không thể đặt hàng.
            </div>
            <?php endif; ?>

            <div>
                <h5 class="fw-bold mb-3">Mô tả sản phẩm</h5>
                <div class="text-muted" style="line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($product['description'] ?? 'Chưa có mô tả.')) ?>
                </div>
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

document.addEventListener('DOMContentLoaded', function() {
    if (variantsData.length > 0) {
        document.getElementById('quantity').disabled = true;
        document.getElementById('btnSubmit').disabled = true;
        document.getElementById('btnSubmit').innerHTML = 'Vui lòng chọn đủ Mẫu, Đối tượng, Màu, Size';

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
