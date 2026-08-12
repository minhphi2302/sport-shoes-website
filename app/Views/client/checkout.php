<?php
require __DIR__ . '/layouts/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/cart" class="text-decoration-none text-dark">Giỏ hàng</a></li>
                <li class="breadcrumb-item active text-red fw-semibold" aria-current="page">Thanh toán đơn hàng</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <h2 class="section-title mb-4">Thanh toán & Đặt hàng</h2>
    


    <?php if (isset($_SESSION['checkout_error'])): ?>
        <div class="alert alert-danger shadow-sm rounded-3">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <?= htmlspecialchars($_SESSION['checkout_error']) ?>
        </div>
        <?php unset($_SESSION['checkout_error']); ?>
    <?php endif; ?>

    <form action="<?= ($_ENV['APP_URL'] ?? '') ?>/checkout/process" method="POST" id="checkoutForm" novalidate>
        <div class="row g-5">
            
            <!-- CỘT TRÁI: THÔNG TIN GIAO HÀNG & THANH TOÁN -->
            <div class="col-lg-8">
                
                <?php if (empty($_SESSION['user'])): ?>
                <div class="d-flex justify-content-between align-items-center bg-white border shadow-sm p-3 mb-4 rounded-3">
                    <span class="text-dark">Đăng nhập để mua hàng tiện lợi và nhận nhiều ưu đãi hơn nữa</span>
                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/login?redirect=checkout" class="btn btn-light border fw-semibold px-4">Đăng nhập</a>
                </div>
                <?php else: ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold mb-0">Tài khoản</h5>
                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/logout" class="text-decoration-none text-muted fw-semibold">Đăng xuất</a>
                </div>
                <div class="bg-white border shadow-sm p-3 mb-4 rounded-3 d-flex align-items-center">
                    <?php 
                        $fullName = $_SESSION['user']['name'] ?? 'User';
                        $initials = mb_strtoupper(mb_substr($fullName, 0, 1));
                        $parts = explode(' ', $fullName);
                        if (count($parts) > 1) {
                            $initials = mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1));
                        }
                    ?>
                    <div class="rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px; background-color: #ffdddd; color: #e60012; font-weight: bold; font-size: 1.1rem;">
                        <?= $initials ?>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($fullName) ?></h6>
                        <span class="text-muted" style="font-size: 0.9rem;"><?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?></span>
                    </div>
                </div>
                <?php endif; ?>

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

                <!-- Section 2: Phương thức thanh toán -->
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-credit-card me-2 text-red"></i> PHƯƠNG THỨC THANH TOÁN</h5>
                    
                    <div class="d-flex flex-column gap-3">
                        <!-- Option 1: COD -->
                        <label class="payment-method-card active d-flex align-items-center border rounded p-3" style="cursor: pointer;">
                            <input type="radio" name="payment_method" value="COD" class="form-check-input me-3" checked>
                            <div>
                                <h6 class="fw-bold mb-1"><i class="fa-solid fa-hand-holding-dollar text-success me-2"></i> Thanh toán khi nhận hàng (COD)</h6>
                                <small class="text-muted">Thanh toán bằng tiền mặt trực tiếp cho shipper khi nhận được hàng.</small>
                            </div>
                        </label>
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
