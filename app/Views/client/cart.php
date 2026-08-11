<?php
require __DIR__ . '/layouts/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item active text-red fw-semibold" aria-current="page">Giỏ hàng của bạn</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <h2 class="section-title mb-4">Giỏ hàng (<span class="text-red"><?= count($cartItems ?? []) ?></span> sản phẩm)</h2>

    <?php if (!empty($cartItems)): ?>
        <?php 
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }
            $freeShipThreshold = 500000;
            $progress = min(100, ($totalAmount / $freeShipThreshold) * 100);
            $remaining = max(0, $freeShipThreshold - $totalAmount);
        ?>
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span></span>
                    <span class="text-success fw-bold"><?= round($progress) ?>%</span>
                </div>
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progress ?>%; border-radius: 5px;" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <?php if ($progress >= 100): ?>
                    <p class="mb-0 text-muted">Chúc mừng ! Đơn hàng của bạn đã đủ điều kiện được Freeship 🎉 Đến trang thanh toán để nhận giảm giá ngay nào!</p> 
                <?php else: ?>
                    <p class="mb-0 text-muted">Mua thêm <span class="fw-bold text-red"><?= number_format($remaining, 0, ',', '.') ?>đ</span> nữa để được miễn phí vận chuyển 🎉</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-4">
            <!-- CỘT TRÁI: BẢNG SẢN PHẨM IN CART -->
            <div class="col-lg-8">
                <div class="table-responsive border rounded-3 shadow-sm">
                    <form id="updateCartForm" action="<?= base_url('cart/update') ?>" method="POST">
                        <table class="table cart-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-center">Size</th>
                                <th class="text-center">Màu sắc</th>
                                <th class="text-center">Giới tính</th>
                                <th class="text-center">Đơn giá</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $totalAmount = 0; ?>
                            <?php foreach ($cartItems as $item): ?>
                                <?php 
                                    $itemSubtotal = $item['price'] * $item['quantity'];
                                    $totalAmount += $itemSubtotal;
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?= htmlspecialchars(base_url($item['image_url'])) ?>" width="70" height="70" class="rounded me-3 object-fit-cover border" alt="<?= htmlspecialchars($item['name']) ?>">
                                            <div>
                                                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $item['product_id'] ?>" class="fw-bold text-dark text-decoration-none d-block mb-1"><?= htmlspecialchars($item['name']) ?> <span class="text-muted fw-normal" style="font-size: 0.85rem;">(SKU: <?= htmlspecialchars($item['sku'] ?? 'N/A') ?>)</span></a> 
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="quantity-selector mx-auto" style="width: 48px;">
                                            <input type="number" name="quantity[<?= $item['cart_key'] ?>]" class="form-control text-center cart-update-btn shadow-none fw-semibold bg-light px-1" style="border-radius: 6px; border: 1px solid #e9ecef; height: 30px; font-size: 0.85rem;" value="<?= $item['quantity'] ?>" min="1">
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <select name="size[<?= $item['cart_key'] ?>]" class="form-select form-select-sm cart-update-btn shadow-none mx-auto fw-semibold bg-light" style="width: 44px; border-radius: 6px; border: 1px solid #e9ecef; height: 28px; font-size: 0.8rem; cursor: pointer; padding: 0 12px 0 4px; text-align: center; background-position: right 4px center; background-size: 6px 4px; margin: 0 auto;">
                                            <?php foreach($item['available_sizes'] as $s): ?>
                                                <option value="<?= htmlspecialchars((string)$s) ?>" <?= ($item['size'] ?? 41) == $s ? 'selected' : '' ?>><?= htmlspecialchars((string)$s) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="text-center align-middle">
                                        <select name="color[<?= $item['cart_key'] ?>]" class="form-select form-select-sm cart-update-btn shadow-none mx-auto fw-semibold bg-light" style="width: 66px; border-radius: 6px; border: 1px solid #e9ecef; height: 28px; font-size: 0.8rem; cursor: pointer; padding: 0 12px 0 4px; text-align: center; background-position: right 4px center; background-size: 6px 4px; margin: 0 auto;">
                                            <?php foreach($item['available_colors'] as $c): ?>
                                                <option value="<?= htmlspecialchars((string)$c) ?>" <?= ($item['color'] ?? 'Black') == $c ? 'selected' : '' ?>><?= htmlspecialchars((string)$c) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="text-center align-middle">
                                        <select name="gender[<?= $item['cart_key'] ?>]" class="form-select form-select-sm cart-update-btn shadow-none mx-auto fw-semibold bg-light" style="width: 62px; border-radius: 6px; border: 1px solid #e9ecef; height: 28px; font-size: 0.8rem; cursor: pointer; padding: 0 12px 0 4px; text-align: center; background-position: right 4px center; background-size: 6px 4px; margin: 0 auto;">
                                            <?php foreach($item['available_genders'] as $g): ?>
                                                <option value="<?= htmlspecialchars($g) ?>" <?= ($item['gender'] ?? 'male') == $g ? 'selected' : '' ?>><?= $g == 'male' ? 'Nam' : ($g == 'female' ? 'Nữ' : ucfirst($g)) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="fw-bold text-center"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                    <td class="text-center">
                                        <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/cart/remove/<?= $item['cart_key'] ?>" class="btn btn-sm btn-outline-danger" title="Xóa"><i class="fa-solid fa-trash-can"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        </table>
                    </form>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-outline-dark-custom btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Quay Lại</a>
                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/cart/clear" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-broom me-1"></i> Xóa tất cả</a>
                </div>
            </div>

            <!-- CỘT PHẢI: TỔNG TIỀN & MÃ GIẢM GIÁ -->
            <div class="col-lg-4">
                <div class="summary-card">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">TỔNG ĐƠN HÀNG</h5>
                    
                    <!-- Mã giảm giá -->
                    <div class="mb-4">
                        <label class="form-label fs-7 fw-bold">MÃ GIẢM GIÁ / VOUCHER</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Nhập mã ưu đãi">
                            <button class="btn btn-dark" type="button">Áp dụng</button>
                        </div>
                    </div>

                    <!-- Tóm tắt chi phí -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Tạm tính:</span>
                        <span class="fw-bold"><?= number_format($totalAmount, 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Giảm giá:</span>
                        <span class="text-success fw-bold">-0đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Phí vận chuyển:</span>
                        <?php if (isset($shippingFee) && $shippingFee > 0): ?>
                            <span class="fw-semibold text-dark"><?= number_format($shippingFee, 0, ',', '.') ?>đ</span>
                        <?php else: ?>
                            <span class="text-success fw-semibold">MIỄN PHÍ</span>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-5">TỔNG TIỀN:</span>
                        <span class="fw-extrabold fs-4 text-red"><?= number_format($finalAmount ?? $totalAmount, 0, ',', '.') ?>đ</span>
                    </div>

                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/checkout" class="btn btn-red w-100 py-3 fw-bold fs-6">
                        Mua Ngay <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- GIỎ HÀNG TRỐNG -->
        <div class="text-center py-5">
            <i class="fa-solid fa-cart-arrow-down fs-1 text-muted mb-3" style="font-size: 4rem !important;"></i>
            <h3 class="fw-bold mt-2">Giỏ hàng của bạn đang trống!</h3>
            <p class="text-secondary">Hãy chọn sản phẩm ưng ý và thêm vào giỏ hàng ngay nhé.</p>
            <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-red btn-lg px-4 py-2 mt-2"><i class="fa-solid fa-bag-shopping me-2"></i> KHÁM PHÁ SẢN PHẨM</a>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const attachListeners = () => {
        const updateBtns = document.querySelectorAll('.cart-update-btn');
        updateBtns.forEach(btn => {
            btn.addEventListener('change', function(e) {
                const form = document.getElementById('updateCartForm');
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Replace the cart container content
                    const newCartContent = doc.querySelector('.container.pb-5');
                    const oldCartContent = document.querySelector('.container.pb-5');
                    if (newCartContent && oldCartContent) {
                        oldCartContent.innerHTML = newCartContent.innerHTML;
                    }
                    
                    // Replace the badge count in the header
                    const newBadge = doc.querySelector('.badge-badge-count');
                    const oldBadge = document.querySelector('.badge-badge-count');
                    if (newBadge && oldBadge) {
                        oldBadge.innerHTML = newBadge.innerHTML;
                    }
                    
                    // Re-attach listeners to the newly rendered DOM elements
                    attachListeners();
                })
                .catch(err => {
                    console.error('Error updating cart:', err);
                });
            });
        });
    };

    attachListeners();
});
</script>

<?php
require __DIR__ . '/layouts/footer.php';
?>
