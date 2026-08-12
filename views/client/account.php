<?php
require __DIR__ . '/layouts/header.php';

$user = $_SESSION['user'] ?? null;
$name = $user['name'] ?? 'User';
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? 'Chưa có';
$address = $user['address'] ?? 'Chưa có';
?>

<!-- Breadcrumb -->
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item active text-secondary" aria-current="page">Tài khoản</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <h5 class="fw-bold mb-3">TRANG TÀI KHOẢN</h5>
            <p class="mb-4">Xin chào, <span class="fw-bold"><?= htmlspecialchars($name) ?> !</span></p>
            
            <ul class="nav flex-column list-unstyled mb-4" id="accountTabs" role="tablist">
                <li class="mb-3 nav-item" role="presentation">
                    <a href="#info" class="nav-link text-dark fw-bold px-0 active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab" aria-controls="info-pane" aria-selected="true" onclick="changeActiveTab(this)"><i class="fa-solid fa-user me-2"></i> Thông tin tài khoản</a>
                </li>
                <li class="mb-3 nav-item" role="presentation">
                    <a href="#orders" class="nav-link text-dark px-0" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders-pane" type="button" role="tab" aria-controls="orders-pane" aria-selected="false" onclick="changeActiveTab(this)"><i class="fa-solid fa-shopping-bag me-2"></i> Đơn hàng của tôi</a>
                </li>
                <li class="mb-3 nav-item" role="presentation">
                    <a href="#address" class="nav-link text-dark px-0" id="address-tab" data-bs-toggle="tab" data-bs-target="#address-pane" type="button" role="tab" aria-controls="address-pane" aria-selected="false" onclick="changeActiveTab(this)"><i class="fa-solid fa-location-dot me-2"></i> Địa chỉ</a>
                </li>
                <li class="mb-3 nav-item" role="presentation">
                    <a href="#password" class="nav-link text-dark px-0" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-pane" type="button" role="tab" aria-controls="password-pane" aria-selected="false" onclick="changeActiveTab(this)"><i class="fa-solid fa-key me-2"></i> Đổi mật khẩu</a>
                </li>
                <li class="nav-item">
                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/logout" class="nav-link text-danger px-0"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất</a>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9 tab-content" id="accountTabsContent">
            
            <!-- Tab: Thông tin tài khoản -->
            <div class="tab-pane fade show active" id="info-pane" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
                <h5 class="fw-bold mb-4">THÔNG TIN CÁ NHÂN</h5>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-3 fw-semibold text-secondary">Họ và tên:</div>
                            <div class="col-md-9" id="infoAddrName"><?= htmlspecialchars($name) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 fw-semibold text-secondary">Email:</div>
                            <div class="col-md-9"><?= htmlspecialchars($email) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 fw-semibold text-secondary">Điện thoại:</div>
                            <div class="col-md-9" id="infoAddrPhone"><?= htmlspecialchars($phone) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 fw-semibold text-secondary">Địa chỉ:</div>
                            <div class="col-md-9" id="infoAddrDetail"><?= htmlspecialchars($address) ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 fw-semibold text-secondary">Vai trò:</div>
                            <div class="col-md-9">
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="badge bg-danger">Quản trị viên</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Khách hàng</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab: Đơn hàng của tôi -->
            <div class="tab-pane fade" id="orders-pane" role="tabpanel" aria-labelledby="orders-tab" tabindex="0">
                <h5 class="fw-bold mb-4">ĐƠN HÀNG CỦA BẠN</h5>
                <div class="table-responsive">
                    <table class="table table-hover border">
                        <thead class="table-light">
                            <tr class="align-middle">
                                <th scope="col" class="py-3 text-center">Mã đơn hàng</th>
                                <th scope="col" class="py-3 text-center">Ngày đặt</th>
                                <th scope="col" class="py-3 text-center">Thành tiền</th>
                                <th scope="col" class="py-3 text-center">Phương thức</th>
                                <th scope="col" class="py-3 text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orders)): ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr class="align-middle">
                                        <td class="fw-bold text-dark py-3 text-center">#<?= str_pad($order['order_id'], 5, '0', STR_PAD_LEFT) ?></td>
                                        <td class="text-secondary py-3 text-center"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                        <td class="fw-bold text-danger py-3 text-center"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</td>
                                        <td class="py-3 text-center">
                                            <span class="badge bg-secondary rounded-pill">COD</span>
                                        </td>
                                        <td class="py-3 text-center">
                                            <?php
                                                $status = $order['status'] ?? 'pending';
                                                $statusBadge = match($status) {
                                                    'pending' => '<span class="badge bg-secondary rounded-pill">Chờ xử lý</span>',
                                                    'confirmed' => '<span class="badge bg-info rounded-pill">Đã xác nhận</span>',
                                                    'completed' => '<span class="badge bg-success rounded-pill">Hoàn thành</span>',
                                                    'cancelled' => '<span class="badge bg-danger rounded-pill">Đã hủy</span>',
                                                    default => '<span class="badge bg-secondary rounded-pill">Chờ xử lý</span>'
                                                };
                                                echo $statusBadge;
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="py-5 text-center text-muted">
                                        <i class="fa-solid fa-shopping-bag fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                                        <p class="mb-0">Bạn chưa có đơn hàng nào.</p>
                                        <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-dark btn-sm mt-3">Mua sắm ngay</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Tab: Sổ địa chỉ -->
            <div class="tab-pane fade" id="address-pane" role="tabpanel" aria-labelledby="address-tab" tabindex="0">
                <h5 class="fw-bold mb-4">ĐỊA CHỈ CỦA BẠN</h5>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3" id="addressDisplayBlock">
                            <div>
                                <p class="mb-2 text-dark fw-semibold">
                                    <i class="fa-solid fa-user me-2"></i><span id="displayAddrName"><?= htmlspecialchars($name) ?></span> 
                                    <span class="text-success ms-2" id="displayAddrDefault" style="font-size: 0.85rem;"><i class="fa-regular fa-circle-check"></i> Địa chỉ mặc định</span>
                                </p>
                                <p class="mb-2 text-dark"><i class="fa-solid fa-phone me-2"></i><span id="displayAddrPhone"><?= htmlspecialchars($phone) ?></span></p>
                                <p class="mb-0 text-dark"><i class="fa-solid fa-location-dot me-2"></i><span id="displayAddrDetail"><?= htmlspecialchars($address) ?></span></p>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="prepareEditAddress()">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Chỉnh sửa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab: Đổi mật khẩu -->
            <div class="tab-pane fade" id="password-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
                <h5 class="fw-bold mb-4">ĐỔI MẬT KHẨU</h5>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <?php if (!empty($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($_SESSION['error']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>
                        
                        <?php if (!empty($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($_SESSION['success']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>
                        
                        <form action="<?= base_url('account/password') ?>" method="POST" id="changePasswordForm" novalidate>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="current_password" required autocomplete="current-password">
                                <div class="invalid-feedback">Vui lòng nhập mật khẩu hiện tại</div>
                            </div>
                            
                            <div class="mb-1">
                                <label class="form-label fw-semibold text-secondary">Mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="acc_new_password" name="new_password" minlength="8" required autocomplete="new-password">
                            </div>
                            <!-- Checklist yêu cầu mật khẩu mới -->
                            <div id="accPwChecklist" class="mb-3 ps-1 pt-1" style="display:none; font-size:0.82rem;">
                                <div class="acc-pw-rule" id="acc-rule-len">⬜ Ít nhất 8 ký tự</div>
                                <div class="acc-pw-rule" id="acc-rule-upper">⬜ Ít nhất 1 chữ hoa (A-Z)</div>
                                <div class="acc-pw-rule" id="acc-rule-lower">⬜ Ít nhất 1 chữ thường (a-z)</div>
                                <div class="acc-pw-rule" id="acc-rule-special">⬜ Ít nhất 1 ký tự đặc biệt (!@#$%^&*)</div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-secondary">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="acc_confirm_password" name="confirm_password" required autocomplete="new-password">
                                <div id="accPwMatchMsg" class="form-text" style="display:none;"></div>
                                <div class="invalid-feedback">Vui lòng xác nhận mật khẩu mới</div>
                            </div>
                            
                            <button type="submit" class="btn btn-dark px-4">
                                <i class="fa-solid fa-key me-2"></i> Cập nhật mật khẩu
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Xóa tài khoản -->
                <div class="card border-danger shadow-sm mt-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-danger mb-3"><i class="fa-solid fa-triangle-exclamation me-2"></i> Xóa tài khoản</h6>
                        <p class="text-muted mb-3">Cảnh báo: Hành động này không thể hoàn tác. Toàn bộ thông tin và lịch sử đơn hàng của bạn sẽ bị xóa vĩnh viễn.</p>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="fa-solid fa-trash-can me-2"></i> Xóa tài khoản của tôi
                        </button>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Modal Xóa Tài Khoản -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3">
      <div class="modal-header border-bottom-0 bg-danger text-white">
        <h5 class="modal-title fw-bold" id="deleteAccountModalLabel"><i class="fa-solid fa-triangle-exclamation me-2"></i> Xác nhận xóa tài khoản</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <p class="text-danger fw-semibold mb-3">⚠️ Hành động này không thể hoàn tác!</p>
        <p class="mb-3">Toàn bộ dữ liệu của bạn sẽ bị xóa vĩnh viễn. Vui lòng nhập mật khẩu để xác nhận.</p>
        
        <form action="<?= base_url('account/delete') ?>" method="POST" id="deleteAccountForm">
            <div class="mb-3">
                <label class="form-label fw-semibold">Mật khẩu của bạn</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Thêm/Sửa Địa Chỉ -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" id="addressModalLabel">Thêm địa chỉ mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addressForm">
            <div class="mb-3">
                <label class="form-label text-dark fw-semibold">Họ và tên</label>
                <input type="text" class="form-control rounded-1" id="addrName" value="">
            </div>
            <div class="mb-3">
                <label class="form-label text-dark fw-semibold">Số điện thoại</label>
                <input type="tel" class="form-control rounded-1" id="addrPhone" value="">
            </div>
            <div class="mb-3">
                <label class="form-label text-dark fw-semibold">Địa chỉ cụ thể</label>
                <textarea class="form-control rounded-1" id="addrDetail" rows="3"></textarea>
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="addrDefault" checked>
                <label class="form-check-label text-dark" for="addrDefault">
                    Đặt làm địa chỉ mặc định
                </label>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light border rounded-1 px-4" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-dark rounded-1 px-4" data-bs-dismiss="modal" onclick="saveAddress()">Lưu địa chỉ</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Xử lý đổi độ đậm (fw-bold) cho tab đang chọn
function changeActiveTab(element) {
    document.querySelectorAll('#accountTabs .nav-link').forEach(el => {
        el.classList.remove('fw-bold');
    });
    element.classList.add('fw-bold');
}

// Giữ nguyên tab nếu URL có hash
document.addEventListener('DOMContentLoaded', function() {
    let hash = window.location.hash;
    if (hash) {
        let targetTab = hash.substring(1); // Remove #
        let triggerEl = document.querySelector('#' + targetTab + '-tab');
        if (triggerEl) {
            let tab = new bootstrap.Tab(triggerEl);
            tab.show();
            changeActiveTab(triggerEl);
        }
    }

    // --- Real-time password strength checker cho form đổi mật khẩu ---
    const accPw     = document.getElementById('acc_new_password');
    const accChecklist = document.getElementById('accPwChecklist');
    const accConfirm = document.getElementById('acc_confirm_password');
    const accMatchMsg = document.getElementById('accPwMatchMsg');

    const accRules = {
        'acc-rule-len':     v => v.length >= 8,
        'acc-rule-upper':   v => /[A-Z]/.test(v),
        'acc-rule-lower':   v => /[a-z]/.test(v),
        'acc-rule-special': v => /[\W_]/.test(v),
    };
    const accLabels = {
        'acc-rule-len':     'ít nhất 8 ký tự',
        'acc-rule-upper':   'ít nhất 1 chữ hoa (A-Z)',
        'acc-rule-lower':   'ít nhất 1 chữ thường (a-z)',
        'acc-rule-special': 'ít nhất 1 ký tự đặc biệt (!@#$%^&*)',
    };

    function checkAccRules(val) {
        let allOk = true;
        for (const [id, fn] of Object.entries(accRules)) {
            const ok = fn(val);
            const el = document.getElementById(id);
            el.textContent = (ok ? '✅ ' : '⬜ ') + accLabels[id];
            el.style.color = ok ? '#198754' : '#6c757d';
            if (!ok) allOk = false;
        }
        return allOk;
    }

    if (accPw) {
        accPw.addEventListener('input', function () {
            accChecklist.style.display = this.value.length > 0 ? 'block' : 'none';
            checkAccRules(this.value);
            if (accConfirm.value.length > 0) updateAccMatch();
        });
    }

    function updateAccMatch() {
        if (!accConfirm.value.length) { accMatchMsg.style.display = 'none'; return; }
        accMatchMsg.style.display = 'block';
        if (accPw.value === accConfirm.value) {
            accMatchMsg.textContent = '✅ Mật khẩu khớp';
            accMatchMsg.className = 'form-text text-success';
        } else {
            accMatchMsg.textContent = '❌ Mật khẩu xác nhận không khớp';
            accMatchMsg.className = 'form-text text-danger';
        }
    }

    if (accConfirm) accConfirm.addEventListener('input', updateAccMatch);

    const changePasswordForm = document.getElementById('changePasswordForm');
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', function (e) {
            const allOk = checkAccRules(accPw.value);
            if (!allOk) {
                e.preventDefault();
                accChecklist.style.display = 'block';
                accPw.focus();
                return;
            }
            if (accPw.value !== accConfirm.value) {
                e.preventDefault();
                updateAccMatch();
                accConfirm.focus();
            }
        });
    }
    
    // Bootstrap form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});

// Xử lý Modal Thêm vs Sửa
function prepareAddAddress() {
    document.getElementById('addressModalLabel').innerText = 'Thêm địa chỉ mới';
    document.getElementById('addrName').value = '<?= htmlspecialchars($name) ?>';
    document.getElementById('addrPhone').value = '';
    document.getElementById('addrDetail').value = '';
    document.getElementById('addrDefault').checked = true;
}

function prepareEditAddress() {
    document.getElementById('addressModalLabel').innerText = 'Chỉnh sửa địa chỉ';
    document.getElementById('addrName').value = document.getElementById('displayAddrName').innerText;
    
    let phoneText = document.getElementById('displayAddrPhone').innerText;
    document.getElementById('addrPhone').value = (phoneText === 'Chưa có') ? '' : phoneText;
    
    let addressText = document.getElementById('displayAddrDetail').innerText;
    document.getElementById('addrDetail').value = (addressText === 'Chưa có') ? '' : addressText;
    
    document.getElementById('addrDefault').checked = true;
}

// Lưu địa chỉ (Gọi API)
async function saveAddress() {
    let newName = document.getElementById('addrName').value.trim();
    let newPhone = document.getElementById('addrPhone').value.trim();
    let newDetail = document.getElementById('addrDetail').value.trim();
    let isDefault = document.getElementById('addrDefault').checked;

    if (!newName) {
        alert("Tên không được để trống!");
        return;
    }

    try {
        let response = await fetch('<?= base_url('account/update') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: newName,
                phone: newPhone,
                address: newDetail
            })
        });

        let result = await response.json();
        if (result.success) {
            // Cập nhật UI
            document.getElementById('displayAddrName').innerText = newName;
            document.getElementById('displayAddrPhone').innerText = newPhone ? newPhone : 'Chưa có';
            document.getElementById('displayAddrDetail').innerText = newDetail ? newDetail : 'Chưa có';
            
            document.getElementById('infoAddrName').innerText = newName;
            document.getElementById('infoAddrPhone').innerText = newPhone ? newPhone : 'Chưa có';
            document.getElementById('infoAddrDetail').innerText = newDetail ? newDetail : 'Chưa có';
            
            // Show success message
            showToast(result.message, 'success');
        } else {
            showToast(result.message || "Lỗi: không thể cập nhật thông tin", 'error');
        }
    } catch (error) {
        showToast("Đã xảy ra lỗi kết nối.", 'error');
        console.error(error);
    }
}

// Toast notification helper
function showToast(message, type = 'info') {
    // Sử dụng cart toast từ footer nếu có
    if (typeof showCartToast === 'function') {
        showCartToast(message, type);
    } else {
        alert(message);
    }
}
</script>

<?php
require __DIR__ . '/layouts/footer.php';
?>
