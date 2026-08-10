<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold m-0">Quản lý Thuộc tính (Size & Màu sắc)</h1>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-rulers me-2"></i>1. Quản lý Size</h5>
        
        <ul class="nav nav-pills nav-sm mb-3" id="sizeTab" role="tablist">
            <li class="nav-item"><button class="nav-link active py-1 px-3" data-bs-toggle="tab" data-bs-target="#attr-size-men" type="button">Giày Nam</button></li>
            <li class="nav-item"><button class="nav-link py-1 px-3" data-bs-toggle="tab" data-bs-target="#attr-size-women" type="button">Giày Nữ</button></li>
            <li class="nav-item"><button class="nav-link py-1 px-3" data-bs-toggle="tab" data-bs-target="#attr-size-kids" type="button">Trẻ Em</button></li>
        </ul>

        <div class="tab-content border rounded p-3 bg-light">
            <?php 
                $genders = ['Nam' => 'attr-size-men', 'Nữ' => 'attr-size-women', 'Trẻ em' => 'attr-size-kids'];
                $first = true;
                foreach ($genders as $genderLabel => $tabId): 
            ?>
            <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" id="<?= $tabId ?>">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php foreach ($sizes as $s): if ($s['gender'] !== $genderLabel) continue; ?>
                        <div class="input-group input-group-sm" style="width: auto;">
                            <span class="input-group-text bg-white fw-bold"><?= htmlspecialchars($s['name']) ?></span>
                            <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/attributes/sizes/<?= $s['id'] ?>/delete" class="btn btn-outline-danger" onclick="return confirm('Xóa size này?');"><i class="bi bi-x"></i></a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/attributes/sizes" method="POST" class="mt-3">
                    <input type="hidden" name="gender" value="<?= $genderLabel ?>">
                    <div class="input-group input-group-sm" style="max-width: 300px;">
                        <input type="text" name="name" class="form-control" placeholder="Nhập size <?= $genderLabel ?> mới..." required>
                        <button class="btn btn-primary" type="submit"><i class="bi bi-plus"></i> Thêm</button>
                    </div>
                </form>
            </div>
            <?php $first = false; endforeach; ?>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-palette me-2"></i>2. Quản lý Màu sắc</h5>
        
        <div class="border rounded p-3 bg-light">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <?php foreach ($colors as $c): ?>
                    <div class="input-group input-group-sm" style="width: auto;">
                        <span class="input-group-text bg-white fw-bold"><?= htmlspecialchars($c['name']) ?></span>
                        <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/attributes/colors/<?= $c['id'] ?>/delete" class="btn btn-outline-danger" onclick="return confirm('Xóa màu này?');"><i class="bi bi-x"></i></a>
                    </div>
                <?php endforeach; ?>
            </div>
            <form action="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/attributes/colors" method="POST" class="mt-3">
                <div class="input-group input-group-sm" style="max-width: 300px;">
                    <input type="text" name="name" class="form-control" placeholder="Nhập màu mới..." required>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-plus"></i> Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const input = form.querySelector('input[name="name"]');
            if (input && input.value.trim() === '') {
                e.preventDefault();
                alert('Giá trị không được để trống hoặc chỉ chứa khoảng trắng!');
                input.value = '';
                input.focus();
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
