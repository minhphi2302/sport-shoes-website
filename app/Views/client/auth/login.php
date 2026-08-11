<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Shop Giày</title>
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
                        <h2 class="text-center mb-4 fw-bold text-primary">Đăng Nhập</h2>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

                        <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/login" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= isset($old_email) ? htmlspecialchars($old_email) : '' ?>" required>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="password" class="form-label mb-0">Mật khẩu</label>
                                    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/forgot-password" class="text-decoration-none small">Quên mật khẩu?</a>
                                </div>
                                <input type="password" class="form-control mt-2" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Đăng nhập</button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="mb-0">Chưa có tài khoản? <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/register" class="text-decoration-none">Đăng ký ngay</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
