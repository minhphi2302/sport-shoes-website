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

                        <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/reset-password" method="POST">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                            </div>
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Lưu mật khẩu mới</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
