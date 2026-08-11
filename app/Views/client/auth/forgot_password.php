<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Shop Giày</title>
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
                        <h2 class="text-center mb-4 fw-bold text-primary">Quên Mật Khẩu</h2>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
    <p class="text-muted mb-4">Vui lòng nhập địa chỉ email bạn đã dùng để đăng ký tài khoản. Chúng tôi sẽ gửi hướng dẫn đặt lại mật khẩu cho bạn.</p>

                        <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/forgot-password" method="POST">
                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Gửi yêu cầu đặt lại mật khẩu</button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="mb-0"><a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/login" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Quay lại Đăng nhập</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
