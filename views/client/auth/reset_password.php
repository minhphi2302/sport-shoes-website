<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - Shop Giày</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .auth-card { border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
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
                        <h2 class="text-center mb-4 fw-bold text-primary">Đặt Lại Mật Khẩu</h2>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <p class="text-muted mb-4">Vui lòng nhập mật khẩu mới cho tài khoản của bạn.</p>

                        <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/reset-password" method="POST" id="resetForm" novalidate>
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                            <div class="mb-1">
                                <label for="password" class="form-label">Mật khẩu mới</label>
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
                                <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password">
                                <div id="pwMatchMsg" class="form-text" style="display:none;"></div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Lưu mật khẩu mới</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function () {
        const pwInput     = document.getElementById('password');
        const pwChecklist = document.getElementById('pwChecklist');
        const confirmInput = document.getElementById('confirm_password');
        const matchMsg    = document.getElementById('pwMatchMsg');

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
            pwChecklist.style.display = val.length > 0 ? 'block' : 'none';
            checkRules(val);
            if (confirmInput.value.length > 0) updateMatchMsg();
        });

        function updateMatchMsg() {
            if (confirmInput.value.length === 0) { matchMsg.style.display = 'none'; return; }
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

        document.getElementById('resetForm').addEventListener('submit', function (e) {
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
