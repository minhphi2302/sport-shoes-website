<?php
require __DIR__ . '/layouts/header.php';
?>

<?php
$redirectParam = !empty($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '';
?>
<div class="auth-wrapper bg-white py-5">
    <div class="container d-flex justify-content-center">
        <div class="auth-card" style="max-width: 600px; width: 100%; padding: 2rem;">
            <!-- Header -->
            <div class="text-center mb-4 pb-2">
                <h3 class="fw-normal text-uppercase mb-3" style="letter-spacing: 0.5px;">Đăng nhập tài khoản</h3>
                <p class="text-dark mb-0">Bạn chưa có tài khoản ? <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/register<?= $redirectParam ?>" class="fw-bold text-decoration-none" style="color: #d9534f; transition: color 0.2s;" onmouseover="this.style.color='#c9302c';" onmouseout="this.style.color='#d9534f';">Đăng ký tại đây</a></p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 fs-7 mb-3">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Form Đăng nhập -->
            <form action="<?= ($_ENV['APP_URL'] ?? '') ?>/login<?= $redirectParam ?>" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control rounded-1 py-2" placeholder="Email" required>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-bold text-dark">Mật khẩu <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control rounded-1 py-2" placeholder="Mật khẩu" required>
                </div>

                <div class="mb-4">
                    <span class="text-dark">Quên mật khẩu? Nhấn vào <a href="#" class="text-primary text-decoration-none">đây</a></span>
                </div>

                <button type="submit" class="btn btn-dark w-100 py-2 fs-5 mb-5 rounded-1">Đăng nhập</button>

                <!-- Social Login -->
                <div class="text-center mb-4">
                    <span class="text-secondary" style="font-size: 1.1rem;">Hoặc đăng nhập bằng</span>
                </div>

                <div class="d-flex justify-content-center">
                    <a href="#" class="btn text-white px-4 py-2 rounded-1 fw-normal" style="background-color: #d9534f; font-size: 1.05rem;">
                        <i class="fa-brands fa-google-plus-g me-2 fw-bold fs-5 align-middle"></i> Đăng nhập Google
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require __DIR__ . '/layouts/footer.php';
?>
