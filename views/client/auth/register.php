<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Shop Giày</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .auth-card { border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        /* Checklist yêu cầu mật khẩu */
        .pw-rule { font-size: 0.82rem; transition: color 0.2s; }
        .pw-rule.ok  { color: #198754; }
        .pw-rule.bad { color: #6c757d; }
        .pw-rule .icon-ok  { display: none; }
        .pw-rule .icon-bad { display: inline; }
        .pw-rule.ok .icon-ok  { display: inline; }
        .pw-rule.ok .icon-bad { display: none; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card auth-card border-0">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4 fw-bold text-primary">Đăng Ký</h2>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/register" method="POST" id="registerForm" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ và tên</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= isset($old['name']) ? htmlspecialchars($old['name']) : '' ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= isset($old['email']) ? htmlspecialchars($old['email']) : '' ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="gender" class="form-label">Giới tính</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="other" <?= (isset($old['gender']) && $old['gender'] === 'other') ? 'selected' : '' ?>>Khác</option>
                                    <option value="male" <?= (isset($old['gender']) && $old['gender'] === 'male') ? 'selected' : '' ?>>Nam</option>
                                    <option value="female" <?= (isset($old['gender']) && $old['gender'] === 'female') ? 'selected' : '' ?>>Nữ</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="8" autocomplete="new-password">
                            </div>
                            <!-- Real-time password strength checklist -->
                            <div id="pwChecklist" class="mb-3 ps-1 pt-1" style="display:none;">
                                <div class="pw-rule bad" id="rule-len">
                                    <span class="icon-ok">✅</span><span class="icon-bad">⬜</span> Ít nhất 8 ký tự
                                </div>
                                <div class="pw-rule bad" id="rule-upper">
                                    <span class="icon-ok">✅</span><span class="icon-bad">⬜</span> Ít nhất 1 chữ hoa (A-Z)
                                </div>
                                <div class="pw-rule bad" id="rule-lower">
                                    <span class="icon-ok">✅</span><span class="icon-bad">⬜</span> Ít nhất 1 chữ thường (a-z)
                                </div>
                                <div class="pw-rule bad" id="rule-special">
                                    <span class="icon-ok">✅</span><span class="icon-bad">⬜</span> Ít nhất 1 ký tự đặc biệt (!@#$%^&*)
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password">
                                <div id="pwMatchMsg" class="form-text" style="display:none;"></div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2" id="registerBtn">Đăng ký</button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="mb-0">Đã có tài khoản? <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/login" class="text-decoration-none">Đăng nhập ngay</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function () {
        const pwInput    = document.getElementById('password');
        const pwChecklist = document.getElementById('pwChecklist');
        const confirmInput = document.getElementById('confirm_password');
        const matchMsg   = document.getElementById('pwMatchMsg');

        function checkRules(val) {
            const rules = {
                'rule-len':     val.length >= 8,
                'rule-upper':   /[A-Z]/.test(val),
                'rule-lower':   /[a-z]/.test(val),
                'rule-special': /[\W_]/.test(val),
            };
            let allOk = true;
            for (const [id, ok] of Object.entries(rules)) {
                const el = document.getElementById(id);
                el.classList.toggle('ok',  ok);
                el.classList.toggle('bad', !ok);
                if (!ok) allOk = false;
            }
            return allOk;
        }

        pwInput.addEventListener('input', function () {
            const val = this.value;
            if (val.length > 0) {
                pwChecklist.style.display = 'block';
            } else {
                pwChecklist.style.display = 'none';
            }
            checkRules(val);
            // Cập nhật lại match khi gõ password
            if (confirmInput.value.length > 0) {
                updateMatchMsg();
            }
        });

        function updateMatchMsg() {
            if (confirmInput.value.length === 0) {
                matchMsg.style.display = 'none';
                return;
            }
            matchMsg.style.display = 'block';
            if (pwInput.value === confirmInput.value) {
                matchMsg.textContent = '✅ Mật khẩu khớp';
                matchMsg.className = 'form-text text-success';
            } else {
                matchMsg.textContent = '❌ Mật khẩu xác nhận không khớp';
                matchMsg.className = 'form-text text-danger';
            }
        }

        confirmInput.addEventListener('input', updateMatchMsg);

        // Chặn submit nếu password chưa đủ điều kiện
        document.getElementById('registerForm').addEventListener('submit', function (e) {
            const allOk = checkRules(pwInput.value);
            if (!allOk) {
                e.preventDefault();
                pwChecklist.style.display = 'block';
                pwInput.focus();
                return;
            }
            if (pwInput.value !== confirmInput.value) {
                e.preventDefault();
                updateMatchMsg();
                confirmInput.focus();
            }
        });
    })();
    </script>
</body>
</html>
