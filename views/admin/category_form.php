<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex align-items-center mb-4">
    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/categories" class="text-decoration-none text-muted me-3 fs-5"><i class="bi bi-arrow-left"></i></a>
    <h1 class="h3 fw-bold m-0"><?= isset($category) ? 'Sửa danh mục' : 'Thêm danh mục' ?></h1>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form id="category-form" action="" method="POST">
            <div class="mb-3 position-relative">
                <label for="name" class="form-label fw-semibold">Tên danh mục</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['name'] ?? '') ?>">
            </div>
            
            <div class="mb-4">
                <label for="description" class="form-label fw-semibold">Mô tả (Tùy chọn)</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary px-4"><?= isset($category) ? 'Cập nhật' : 'Thêm mới' ?></button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('category-form').addEventListener('submit', function(e) {
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        
        let isValid = true;
        let firstInvalid = null;

        const showError = (input, message) => {
            if (!isValid) return; 
            isValid = false;
            firstInvalid = input;
            input.classList.add('is-invalid');
            const err = document.createElement('div');
            err.className = 'invalid-feedback fw-bold';
            err.innerText = message;
            input.parentElement.appendChild(err);
        };

        const name = document.querySelector('input[name="name"]');
        if (name.value.trim().length === 0) {
            showError(name, 'Vui lòng không để trống tên danh mục.');
        } else if (name.value.trim().length > 100) {
            showError(name, 'Tên danh mục không được vượt quá 100 ký tự.');
        }

        if (!isValid) {
            e.preventDefault();
            firstInvalid.focus();
        }
    });
});
</script>
