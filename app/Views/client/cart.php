<?php 
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/layouts/header.php'; 
$cart = $cart ?? $cartItems ?? $_SESSION['cart'] ?? [];
?>

<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-uppercase m-0">Giỏ hàng của bạn</h2>
            <small class="text-muted">Quản lý và kiểm tra các sản phẩm đã chọn</small>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($cart)): ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border-0">
            <i class="bi bi-cart-x display-1 text-muted mb-3 d-block"></i>
            <h4 class="fw-bold mb-2">Giỏ hàng của bạn đang trống</h4>
            <p class="text-muted mb-4">Hãy chọn cho mình những đôi giày thể thao ưng ý nhất nhé!</p>
            <a href="<?= base_url('products') ?>" class="btn btn-primary rounded-pill px-5 py-3 fw-bold"><i class="bi bi-shop me-2"></i> Khám phá sản phẩm ngay</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Danh sách sản phẩm -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="border-0 text-uppercase fw-bold text-muted ps-3">Sản phẩm</th>
                                        <th scope="col" class="border-0 text-uppercase fw-bold text-muted text-center">Số lượng</th>
                                        <th scope="col" class="border-0 text-uppercase fw-bold text-muted text-end">Thành tiền</th>
                                        <th scope="col" class="border-0 text-uppercase fw-bold text-muted text-end pe-3">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart as $item): ?>
                                        <?php 
                                            $price = $item['price'] ?? 0;
                                            $salePrice = $item['sale_price'] ?? null;
                                            $priceToUse = (!empty($salePrice) && $salePrice < $price) ? $salePrice : $price;
                                            $subtotal = $priceToUse * ($item['quantity'] ?? 1);
                                        ?>
                                        <tr>
                                            <td class="ps-3 py-3">
                                                <div class="d-flex align-items-center">
                                                    <?php 
                                                        $imgSrc = get_product_image_url($item['image_url'] ?? null, $item['product_id'] ?? 1);
                                                    ?>
                                                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="img-fluid rounded-3 me-3 border" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.src='<?= base_url('image/slide/slide1.avif') ?>'">
                                                    <div>
                                                        <a href="<?= base_url('product/' . $item['product_id']) ?>" class="text-dark fw-bold text-decoration-none d-block mb-1">
                                                            <?= htmlspecialchars($item['name']) ?>
                                                        </a>
                                                        <?php if (!empty($item['sku'])): ?>
                                                            <small class="text-muted d-block" style="font-size: 0.8rem;">Mã SKU: <?= htmlspecialchars($item['sku']) ?></small>
                                                        <?php endif; ?>
                                                        <?php if (!empty($item['size'])): ?>
                                                            <small class="text-muted d-block" style="font-size: 0.8rem;">Size: <span class="fw-semibold text-dark"><?= htmlspecialchars((string)$item['size']) ?></span></small>
                                                        <?php endif; ?>
                                                        <?php if (!empty($item['color'])): ?>
                                                            <small class="text-muted d-block" style="font-size: 0.8rem;">Màu sắc: <span class="fw-semibold text-dark"><?= htmlspecialchars((string)$item['color']) ?></span></small>
                                                        <?php endif; ?>
                                                        <div class="mt-1 text-primary fw-bold" style="font-size: 0.9rem;">
                                                            <?= number_format($priceToUse, 0, ',', '.') ?>đ
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <form action="<?= base_url('cart/update') ?>" method="POST" class="cart-update-form d-flex align-items-center justify-content-center">
                                                    <input type="hidden" name="cart_key" value="<?= $item['cart_key'] ?? $item['product_id'] ?>">
                                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control text-center rounded-3 mx-1 cart-quantity-input" style="width: 70px;" data-cart-key="<?= $item['cart_key'] ?? $item['product_id'] ?>" data-max="<?= $item['max_quantity'] ?? 999 ?>">
                                                </form>
                                            </td>
                                            <td class="text-end fw-bold text-danger">
                                                <?= number_format($subtotal, 0, ',', '.') ?>đ
                                            </td>
                                            <td class="text-end pe-3">
                                                <form action="<?= base_url('cart/remove') ?>" method="POST" class="d-inline cart-remove-form">
                                                    <input type="hidden" name="cart_key" value="<?= $item['cart_key'] ?? $item['product_id'] ?>">
                                                    <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none" title="Xóa">
                                                        <i class="bi bi-trash3 fs-5"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <a href="<?= base_url('products') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Tiếp tục chọn đồ
                    </a>
                    <form action="<?= base_url('cart/clear') ?>" method="POST">
                        <button type="submit" class="btn btn-outline-danger rounded-pill px-4" onclick="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')">
                            <i class="bi bi-trash me-1"></i> Xóa toàn bộ giỏ hàng
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="col-lg-4">
                <?php if(\App\Core\Auth::check()): ?>
                    <?php $currentUser = \App\Core\Auth::user(); ?>
                    <!-- Thông tin khách hàng -->
                    <div class="card border-0 shadow-sm rounded-4 mb-3">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-person-circle me-2 text-primary"></i>Thông tin khách hàng</h5>
                            <div class="mb-2">
                                <small class="text-muted d-block">Họ tên:</small>
                                <span class="fw-semibold"><?= htmlspecialchars($currentUser['name'] ?? 'Chưa cập nhật') ?></span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Email:</small>
                                <span class="fw-semibold"><?= htmlspecialchars($currentUser['email'] ?? '') ?></span>
                            </div>
                            <?php if (!empty($currentUser['phone'])): ?>
                            <div class="mb-2">
                                <small class="text-muted d-block">Số điện thoại:</small>
                                <span class="fw-semibold"><?= htmlspecialchars($currentUser['phone']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($currentUser['address'])): ?>
                            <div class="mb-2">
                                <small class="text-muted d-block">Địa chỉ:</small>
                                <span class="fw-semibold small"><?= htmlspecialchars($currentUser['address']) ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="mt-3">
                                <a href="<?= base_url('profile') ?>" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                    <i class="bi bi-pencil-square me-1"></i> Cập nhật thông tin
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Tóm tắt đơn hàng</h4>
                        
                        <?php 
                            $threshold = defined('FREE_COD_THRESHOLD') ? FREE_COD_THRESHOLD : 1000000;
                            $needed = $threshold - $total;
                        ?>
                        <?php if ($needed > 0): ?>
                            <div class="alert alert-warning border-0 rounded-3 mb-3 p-3" style="font-size: 0.85rem;">
                                <i class="bi bi-truck me-1"></i> Mua thêm <strong><?= number_format($needed, 0, ',', '.') ?>đ</strong> để được <strong class="text-success">Miễn phí vận chuyển (Freeship)</strong>!
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success border-0 rounded-3 mb-3 p-3" style="font-size: 0.85rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Đơn hàng của bạn đã đạt mốc <strong class="text-success">Miễn phí vận chuyển (Freeship)</strong>!
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Tạm tính tiền hàng</span>
                            <span class="fw-semibold"><?= number_format($total, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Dự kiến giao hàng</span>
                            <span class="fw-semibold text-dark">Toàn quốc (1-3 ngày)</span>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Tạm tính:</span>
                            <span class="fw-bold fs-4 text-red"><?= number_format($total, 0, ',', '.') ?>đ</span>
                        </div>

                        <a href="<?= base_url('checkout') ?>" class="btn btn-red w-100 py-3 rounded-pill fw-bold text-uppercase fs-5">
                            Tiến hành đặt hàng <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
/* Cart Page JavaScript - Version 2.0 - Fixed double confirm issue */
document.addEventListener('DOMContentLoaded', function() {
    // AJAX Update Quantity - trigger on change
    const quantityInputs = document.querySelectorAll('.cart-quantity-input');
    
    quantityInputs.forEach(input => {
        let debounceTimer;
        
        input.addEventListener('change', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => updateCartQuantity(this), 500);
        });
        
        // Optional: Update while typing (after user stops for 1 second)
        input.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => updateCartQuantity(this), 1000);
        });
    });
    
    function updateCartQuantity(input) {
        const cartKey = input.dataset.cartKey;
        const quantity = parseInt(input.value);
        const form = input.closest('.cart-update-form');
        
        if (quantity < 1) {
            if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                // Submit form normally for deletion
                form.submit();
            } else {
                input.value = 1; // Reset to minimum
            }
            return;
        }
        
        // Show loading state
        input.disabled = true;
        input.style.opacity = '0.5';
        
        // Prepare form data
        const formData = new FormData();
        formData.append('cart_key', cartKey);
        formData.append('quantity', quantity);
        
        // Send AJAX request
        fetch('<?= base_url("cart/update") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(data => {
            // Reload page to update totals
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật giỏ hàng. Vui lòng thử lại.');
            input.disabled = false;
            input.style.opacity = '1';
        });
    }
    
    // AJAX Remove Item - prevent default form submit and use AJAX
    const removeForms = document.querySelectorAll('.cart-remove-form');
    
    removeForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                return;
            }
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            
            // Show loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
            }
            
            fetch('<?= base_url("cart/remove") ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(data => {
                // Reload page to update cart
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa sản phẩm. Vui lòng thử lại.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                }
            });
        });
    });
});
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
