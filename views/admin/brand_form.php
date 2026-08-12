<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex align-items-center mb-4">
    <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/admin/brands" class="text-decoration-none text-muted me-3 fs-5"><i class="bi bi-arrow-left"></i></a>
    <h1 class="h3 fw-bold m-0"><?= isset($brand) ? 'Sửa thương hiệu' : 'Thêm thương hiệu' ?></h1>
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
        <form id="brand-form" action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3 position-relative">
                <label for="name" class="form-label fw-semibold">Tên thương hiệu <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($brand['name'] ?? '') ?>">
            </div>

            <div class="mb-3 position-relative">
                <label class="form-label fw-semibold">Logo thương hiệu (Tùy chọn)</label>

                <?php 
                $currentImage = $brand['logo_url'] ?? $brand['image_url'] ?? null;
                if (isset($brand) && !empty($currentImage)): 
                ?>
                    <!-- Ảnh hiện tại -->
                    <div class="mb-2 d-flex align-items-center gap-3">
                        <?php $logo = get_brand_image_url($currentImage, $brand['name']); ?>
                        <img id="currentLogoPreview" src="<?= htmlspecialchars($logo) ?>"
                             alt="Logo hiện tại"
                             class="img-thumbnail"
                             style="max-height: 80px; object-fit: contain;"
                             onerror="this.src='<?= base_url('image/logo.webp') ?>'">
                        <div>
                            <small class="text-muted d-block">Logo hiện tại</small>
                            <small class="text-muted"><?= htmlspecialchars(basename($currentImage)) ?></small>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- File upload field -->
                <input type="file" class="form-control" id="image" name="image"
                       accept="image/jpeg,image/png,image/webp"
                       onchange="previewLogo(this)">
                <small class="form-text text-muted">
                    Chọn tệp hình ảnh (JPG, PNG, WEBP — tối đa 2MB).
                    <?php if (isset($brand) && !empty($currentImage)): ?>
                        Bỏ trống nếu giữ nguyên logo cũ.
                    <?php endif; ?>
                </small>

                <!-- Preview ảnh mới được chọn -->
                <div id="newLogoPreviewWrap" class="mt-2" style="display:none;">
                    <small class="text-success fw-semibold d-block mb-1">Ảnh mới được chọn:</small>
                    <img id="newLogoPreview" src="" alt="Preview" class="img-thumbnail" style="max-height: 80px; object-fit: contain;">
                </div>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label fw-semibold">Mô tả (Tùy chọn)</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($brand['description'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary px-4"><?= isset($brand) ? 'Cập nhật' : 'Thêm mới' ?></button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>

<script>
function previewLogo(input) {
    const wrap = document.getElementById('newLogoPreviewWrap');
    const preview = document.getElementById('newLogoPreview');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('Ảnh vượt quá 2MB, vui lòng chọn ảnh nhỏ hơn.');
            input.value = '';
            wrap.style.display = 'none';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            wrap.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        wrap.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('brand-form').addEventListener('submit', function(e) {
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
            showError(name, 'Vui lòng không để trống tên thương hiệu.');
        } else if (name.value.trim().length > 100) {
            showError(name, 'Tên thương hiệu không được vượt quá 100 ký tự.');
        }

        if (!isValid) {
            e.preventDefault();
            firstInvalid.focus();
        }
    });
});
</script>