<?php 
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/layouts/header.php'; 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <h2 class="fw-bold mb-4">Quản lý tài khoản</h2>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold">Thông tin cá nhân</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/profile/update" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email (không thể thay đổi)</label>
                            <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="Nhập số điện thoại">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea class="form-control" name="address" rows="3" placeholder="Số nhà, đường, xã/phường, quận/huyện, tỉnh/TP"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-0">
                            <p class="mb-0"><strong>Vai trò:</strong> <?= isset($user['role']) && $user['role'] === 'admin' ? 'Quản trị viên' : 'Khách hàng' ?></p>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 mt-3">Cập nhật thông tin</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold">Đổi mật khẩu</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/profile/password" method="POST">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0">Mật khẩu hiện tại</label>
                                <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/forgot-password" class="text-decoration-none small">Quên mật khẩu?</a>
                            </div>
                            <input type="password" class="form-control mt-2" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" name="new_password" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" name="confirm_password" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Cập nhật mật khẩu</button>
                    </form>
                </div>
            </div>

            <?php if (isset($user['role']) && $user['role'] !== 'admin'): ?>
            <div class="card border-0 shadow-sm rounded-4 border-danger border border-1">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-danger">Xóa tài khoản</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted">Cảnh báo: Hành động này không thể hoàn tác. Toàn bộ thông tin tài khoản và lịch sử đơn hàng của bạn sẽ bị xóa vĩnh viễn.</p>
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        Xóa tài khoản
                    </button>
                </div>
            </div>

            <!-- Delete Account Modal -->
            <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content rounded-4 border-0 shadow">
                        <div class="modal-header border-0">
                            <h5 class="modal-title fw-bold text-danger">Xác nhận xóa tài khoản</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/profile/delete" method="POST">
                            <div class="modal-body">
                                <p>Để xác nhận, vui lòng nhập mật khẩu của bạn:</p>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-danger rounded-pill px-4">Xác nhận xóa</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
