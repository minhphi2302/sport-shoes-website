<?php 
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/layouts/header.php'; 
?>

<div class="container py-5">
    <h2 class="section-title mb-4">Thanh toán & Đặt hàng</h2>

    <?php if (isset($_SESSION['checkout_error'])): ?>
        <div class="alert alert-danger shadow-sm rounded-3">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <?= htmlspecialchars($_SESSION['checkout_error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form id="checkoutForm" action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/checkout" method="POST">
        <div class="row g-5">
            <!-- Thông tin giao hàng -->
            <div class="col-lg-7">
                <!-- Section 1: Thông tin người nhận -->
                <div class="card border shadow-sm p-4 mb-4 rounded-3">
                    <h5 class="fw-bold mb-4">Thông tin giao hàng</h5>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-floating custom-floating position-relative">
                                <input type="text" name="recipient_name" id="recipient_name" class="form-control custom-checkout-input pe-5" placeholder="Nhập họ và tên" required value="<?= htmlspecialchars($user['name'] ?? $user['full_name'] ?? '') ?>">
                                <label for="recipient_name" class="text-muted">Họ và tên</label>
                                <span class="position-absolute end-0 me-3 d-flex align-items-center" style="top: 29px; transform: translateY(-50%); z-index: 5;">
                                    <i class="fa-solid fa-circle-xmark text-secondary clear-btn" data-target="recipient_name" style="cursor: pointer; display: none; font-size: 0.95rem;"></i>
                                </span>
                                <div class="invalid-feedback" id="err-recipient_name"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating custom-floating position-relative">
                                <input type="tel" name="recipient_phone" id="recipient_phone" class="form-control custom-checkout-input" style="padding-right: 80px;" placeholder="Nhập số điện thoại" required value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                <label for="recipient_phone" class="text-muted">Số điện thoại</label>
                                <span class="position-absolute end-0 me-3 d-flex align-items-center" style="top: 29px; transform: translateY(-50%); z-index: 5;">
                                    <i class="fa-solid fa-circle-xmark text-secondary clear-btn" data-target="recipient_phone" style="cursor: pointer; display: none; font-size: 0.95rem;"></i>
                                    <div class="vr mx-2 text-muted" style="height: 18px; opacity: 0.3;"></div>
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/21/Flag_of_Vietnam.svg" alt="VN" width="24" class="rounded-1 border shadow-sm">
                                </span>
                                <div class="invalid-feedback" id="err-recipient_phone"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating custom-floating position-relative">
                                <input type="email" name="email" id="email" class="form-control custom-checkout-input pe-5" placeholder="Nhập Email" required value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                                <label for="email" class="text-muted">Email</label>
                                <span class="position-absolute end-0 me-3 d-flex align-items-center" style="top: 29px; transform: translateY(-50%); z-index: 5;">
                                    <i class="fa-solid fa-circle-xmark text-secondary clear-btn" data-target="email" style="cursor: pointer; display: none; font-size: 0.95rem;"></i>
                                </span>
                                <div class="invalid-feedback" id="err-email"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating custom-floating">
                                <input type="text" name="country" id="country" class="form-control custom-checkout-input" placeholder="Quốc gia" value="Vietnam" readonly>
                                <label for="country" class="text-muted">Quốc gia</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating custom-floating position-relative">
                                <input type="text" name="street_address" id="street_address" class="form-control custom-checkout-input pe-5" placeholder="Địa chỉ, tên đường" required value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                                <label for="street_address" class="text-muted">Địa chỉ, tên đường</label>
                                <span class="position-absolute end-0 me-3 d-flex align-items-center" style="top: 29px; transform: translateY(-50%); z-index: 5;">
                                    <i class="fa-solid fa-circle-xmark text-secondary clear-btn" data-target="street_address" style="cursor: pointer; display: none; font-size: 0.95rem;"></i>
                                </span>
                                <div class="invalid-feedback" id="err-street_address"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating custom-floating">
                                <textarea name="notes" id="notes" class="form-control custom-checkout-input" style="height: 80px" placeholder="Ghi chú đơn hàng (Tùy chọn)"></textarea>
                                <label for="notes" class="text-muted">Ghi chú đơn hàng (Tùy chọn)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Phương thức thanh toán</h4>
                        
                        <div class="form-check mb-3 p-3 border rounded-3 bg-light">
                            <input class="form-check-input ms-1" type="radio" name="payment_method" id="payment_cod" value="COD" checked>
                            <label class="form-check-label ms-2 fw-semibold d-flex align-items-center" for="payment_cod">
                                <i class="bi bi-cash-stack fs-4 text-success me-2"></i> Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        
                        <!-- Có thể thêm VNPAY ở đây sau này -->
                    </div>
                </div>

            </div>

            <!-- CỘT PHẢI: TÓM TẮT ĐƠN HÀNG -->
            <div class="col-lg-4">
                <div class="summary-card shadow-sm sticky-top" style="top: 100px; z-index: 1000;">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">ĐƠN HÀNG CỦA BẠN</h5>

                    <!-- Danh sách sản phẩm -->
                    <div class="checkout-items-list mb-4" style="max-height: 300px; overflow-y: auto;">
                        <?php if (!empty($cartItems)): ?>
                            <?php foreach ($cartItems as $item): ?>
                                <?php 
                                    $imgSrc = get_product_image_url($item['image_url'] ?? null, $item['product_id'] ?? 1);
                                ?>
                                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                    <img src="<?= htmlspecialchars($imgSrc) ?>" width="55" height="55" class="rounded me-3 object-fit-cover border" alt="<?= htmlspecialchars($item['name']) ?>" onerror="this.src='<?= base_url('image/slide/slide1.avif') ?>'">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fs-7 fw-bold"><?= htmlspecialchars($item['name']) ?></h6>
                                        <small class="text-muted">SKU: <?= htmlspecialchars($item['sku'] ?? 'N/A') ?> | Size: <?= $item['size'] ?? '41' ?> | SL: <?= $item['quantity'] ?></small>
                                    </div>
                                    <span class="fw-bold fs-7"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Không có sản phẩm nào trong giỏ hàng.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Tóm tắt số tiền -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Tạm tính:</span>
                        <span class="fw-bold"><?= number_format($totalAmount ?? 0, 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Phí vận chuyển:</span>
                        <?php if (isset($shippingFee) && $shippingFee > 0): ?>
                            <span class="fw-semibold text-dark"><?= number_format($shippingFee, 0, ',', '.') ?>đ</span>
                        <?php else: ?>
                            <span class="text-success fw-bold">MIỄN PHÍ</span>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-5">TỔNG CỘNG:</span>
                        <span class="fw-extrabold fs-3 text-red"><?= number_format($finalAmount ?? $totalAmount ?? 0, 0, ',', '.') ?>đ</span>
                    </div>

                    <button type="submit" class="btn btn-red w-100 py-3 fw-bold fs-6 shadow">
                        <i class="fa-solid fa-check-circle me-2"></i> ĐẶT HÀNG NGAY
                    </button>
                    <p class="text-muted text-center fs-7 mt-3 mb-0">
                        <i class="fa-solid fa-lock me-1"></i> Thông tin được mã hóa bảo mật.
                    </p>
                </div>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Tóm tắt đơn hàng</h4>
                        
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                            <span class="text-muted">Sản phẩm</span>
                            <span class="text-muted text-end">Tạm tính</span>
                        </div>
                        
                        <?php foreach ($cart as $item): ?>
                            <?php 
                                $priceToUse = (!empty($item['sale_price']) && $item['sale_price'] < $item['price']) ? $item['sale_price'] : $item['price'];
                                $subtotal = $priceToUse * $item['quantity'];
                            ?>
                            <div class="d-flex justify-content-between mb-3 align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3">
                                        <?php 
                                            $imgSrc = !empty($item['image_url']) ? '/uploads/' . $item['image_url'] : '/uploads/default-product.jpg';
                                            $baseUrl = htmlspecialchars($_ENV['APP_URL'] ?? '');
                                        ?>
                                        <img src="<?= $baseUrl . htmlspecialchars($imgSrc) ?>" alt="" class="rounded" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='<?= $baseUrl ?>/uploads/default-product.jpg'">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                            <?= $item['quantity'] ?>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="d-block fw-semibold" style="font-size: 0.9rem;"><?= htmlspecialchars($item['name']) ?></span>
                                        <?php if (!empty($item['size'])): ?>
                                            <small class="text-muted d-block" style="font-size: 0.8rem;">Size: <?= htmlspecialchars($item['size']) ?></small>
                                        <?php endif; ?>
                                        <?php if (!empty($item['color'])): ?>
                                            <small class="text-muted d-block" style="font-size: 0.8rem;">Màu: <?= htmlspecialchars($item['color']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="fw-semibold text-end" style="font-size: 0.9rem;"><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                            </div>
                        <?php endforeach; ?>
                        
                        <hr class="my-4">
                        
                        <?php 
                            $threshold = $freeThreshold ?? 1000000;
                            $currentSubtotal = $subtotal ?? 0;
                            $currentShippingFee = $shippingFee ?? 0;
                            $currentGrandTotal = $grandTotal ?? ($currentSubtotal + $currentShippingFee);
                        ?>

                        <?php if ($currentSubtotal < $threshold): ?>
                            <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-3 p-3 d-flex align-items-center" style="font-size: 0.85rem;">
                                <i class="bi bi-info-circle-fill text-warning me-2 fs-5"></i>
                                <div>
                                    Mua thêm <strong><?= number_format($threshold - $currentSubtotal, 0, ',', '.') ?>đ</strong> để được <strong class="text-success">Miễn phí vận chuyển</strong>!
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3 p-3 d-flex align-items-center" style="font-size: 0.85rem;">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                <div>
                                    Đơn hàng của bạn đã đủ điều kiện <strong class="text-success">Miễn phí vận chuyển (Freeship)</strong>!
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tạm tính</span>
                            <span class="fw-semibold"><?= number_format($currentSubtotal, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Phí vận chuyển & COD</span>
                            <?php if ($currentShippingFee == 0): ?>
                                <span class="fw-semibold text-success"><i class="bi bi-patch-check-fill me-1"></i>Miễn phí</span>
                            <?php else: ?>
                                <span class="fw-semibold text-dark"><?= number_format($currentShippingFee, 0, ',', '.') ?>đ</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <span class="fw-bold fs-5">Tổng cộng</span>
                            <span class="fw-bold fs-4 text-primary"><?= number_format($currentGrandTotal, 0, ',', '.') ?>đ</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 mt-4 rounded-pill fw-bold text-uppercase fs-5">
                            Đặt hàng ngay
                        </button>
                        <div class="text-center mt-3">
                            <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/cart" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Quay lại giỏ hàng</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>




<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form Validation on Submit & Blur
    const checkoutForm = document.getElementById('checkoutForm');
    const validateField = (input, errorDivId, emptyMsg, patternMsg, pattern) => {
        const val = input.value.trim();
        const errorDiv = document.getElementById(errorDivId);
        if (!val) {
            input.classList.add('is-invalid');
            errorDiv.innerText = emptyMsg;
            return false;
        } else if (pattern && !pattern.test(val)) {
            input.classList.add('is-invalid');
            errorDiv.innerText = patternMsg;
            return false;
        } else {
            input.classList.remove('is-invalid');
            return true;
        }
    };

    const inputsToValidate = [
        { el: document.getElementById('recipient_name'), err: 'err-recipient_name', empty: 'Vui lòng nhập họ và tên', patternMsg: null, pattern: null },
        { el: document.getElementById('recipient_phone'), err: 'err-recipient_phone', empty: 'Vui lòng nhập số điện thoại', patternMsg: 'Số điện thoại không hợp lệ', pattern: /^[0-9]{9,12}$/ },
        { el: document.getElementById('email'), err: 'err-email', empty: 'Vui lòng nhập email', patternMsg: 'Email không hợp lệ', pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
        { el: document.getElementById('street_address'), err: 'err-street_address', empty: 'Vui lòng nhập địa chỉ nhận hàng', patternMsg: null, pattern: null }
    ];

    inputsToValidate.forEach(item => {
        if (item.el) {
            item.el.addEventListener('blur', () => validateField(item.el, item.err, item.empty, item.patternMsg, item.pattern));
            item.el.addEventListener('input', () => {
                if (item.el.classList.contains('is-invalid')) {
                    validateField(item.el, item.err, item.empty, item.patternMsg, item.pattern);
                }
            });
        }
    });

    checkoutForm.addEventListener('submit', function(e) {
        let isValid = true;
        inputsToValidate.forEach(item => {
            if (item.el && !validateField(item.el, item.err, item.empty, item.patternMsg, item.pattern)) {
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            // scroll to first error
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    // Xử lý nút Clear cho các input
    const clearBtns = document.querySelectorAll('.clear-btn');
    clearBtns.forEach(btn => {
        const targetId = btn.getAttribute('data-target');
        const input = document.getElementById(targetId);
        if (input) {
            const toggleBtn = () => {
                // Chỉ hiện nút X khi ô có chữ VÀ đang được click/focus
                btn.style.display = (input.value.length > 0 && document.activeElement === input) ? 'block' : 'none';
            };
            
            input.addEventListener('input', toggleBtn);
            input.addEventListener('focus', toggleBtn);
            input.addEventListener('blur', () => {
                // Độ trễ nhỏ để sự kiện click vào nút X kịp xử lý trước khi ẩn
                setTimeout(toggleBtn, 150);
            });
            
            toggleBtn(); // Khởi tạo ban đầu
            
            // Ngăn input bị mất focus khi click vào nút X
            btn.addEventListener('mousedown', (e) => {
                e.preventDefault();
            });
            
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                input.value = '';
                input.focus();
                toggleBtn();
                input.dispatchEvent(new Event('input')); // Kích hoạt sự kiện input để tắt báo lỗi nếu cần
            });
        }
    });
});
</script>

<?php
require __DIR__ . '/layouts/footer.php';
?>
