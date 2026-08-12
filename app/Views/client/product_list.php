<?php 
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/layouts/header.php'; 
?>
<!-- Breadcrumb -->
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item active text-red fw-semibold" aria-current="page">Danh sách sản phẩm</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        
        <!-- SIDEBAR BỘ LỌC -->
        <div class="col-lg-3">
            <div class="filter-sidebar">
                <form action="<?= ($_ENV['APP_URL'] ?? '') ?>/products" method="GET" id="filterForm">
                    
                    <!-- Lọc Danh mục -->
                    <div class="filter-group">
                        <h6 class="filter-title"><i class="fa-solid fa-list me-2 text-red"></i> Danh mục</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="category_id" value="" id="cat_all" <?= empty($_GET['category_id']) ? 'checked' : '' ?> onchange="this.form.submit()">
                            <label class="form-check-label" for="cat_all">Tất cả danh mục</label>
                        </div>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <?php
                                    $catName = $cat['name'];
                                    if (stripos($catName, 'Giày') === false && stripos($catName, 'Giay') === false) {
                                        $catName = 'Giày ' . $catName;
                                    }
                                ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="category_id" value="<?= $cat['category_id'] ?>" id="cat_<?= $cat['category_id'] ?>" <?= (isset($_GET['category_id']) && $_GET['category_id'] == $cat['category_id']) ? 'checked' : '' ?> onchange="this.form.submit()">
                                    <label class="form-check-label" for="cat_<?= $cat['category_id'] ?>"><?= htmlspecialchars($catName) ?></label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Lọc Thương hiệu -->
                    <div class="filter-group">
                        <h6 class="filter-title"><i class="fa-solid fa-tags me-2 text-red"></i> Thương hiệu</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="brand_id" value="" id="brand_all" <?= empty($_GET['brand_id']) ? 'checked' : '' ?> onchange="this.form.submit()">
                            <label class="form-check-label" for="brand_all">Tất cả thương hiệu</label>
                        </div>
                        <?php if (!empty($brands)): ?>
                            <?php foreach ($brands as $b): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="brand_id" value="<?= $b['brand_id'] ?>" id="brand_<?= $b['brand_id'] ?>" <?= (isset($_GET['brand_id']) && $_GET['brand_id'] == $b['brand_id']) ? 'checked' : '' ?> onchange="this.form.submit()">
                                    <label class="form-check-label" for="brand_<?= $b['brand_id'] ?>"><?= htmlspecialchars($b['name']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    

            
                    <!-- Giới tính -->
                    <div class="filter-group">
                        <h6 class="filter-title"><i class="fa-solid fa-venus-mars me-2 text-red"></i> Giới tính</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="gender" value="" id="gender_all" <?= empty($_GET['gender']) ? 'checked' : '' ?> onchange="this.form.submit()">
                            <label class="form-check-label" for="gender_all">Tất cả</label>
                        </div>
                        <?php 
                        $genders = $availableGenders ?? ['male', 'female'];
                        $genderLabels = ['male' => 'Nam', 'female' => 'Nữ'];
                        foreach ($genders as $g): 
                            $label = $genderLabels[$g] ?? ucfirst($g);
                        ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="gender" value="<?= $g ?>" id="gender_<?= $g ?>" <?= (isset($_GET['gender']) && $_GET['gender'] == $g) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <label class="form-check-label" for="gender_<?= $g ?>"><?= $label ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
            
                    <!-- Chọn Size Giày -->
                    <div class="filter-group">
                        <h6 class="filter-title"><i class="fa-solid fa-shoe-prints me-2 text-red"></i> Kích thước (Size)</h6>
                        <div class="size-btn-group flex-wrap gap-2 d-flex">
                            <?php 
                            $sizes = $availableSizes ?? [38, 39, 40, 41, 42, 43, 44]; 
                            foreach ($sizes as $s): 
                            ?>
                                <input type="radio" class="btn-check" name="size" id="size_<?= $s ?>" value="<?= $s ?>" autocomplete="off" <?= (isset($_GET['size']) && $_GET['size'] == $s) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <label class="btn btn-outline-dark" for="size_<?= $s ?>"><?= $s ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Màu sắc -->
                    <div class="filter-group">
                        <h6 class="filter-title"><i class="fa-solid fa-palette me-2 text-red"></i> Màu sắc</h6>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <?php 
                            $colors = $availableColors ?? ['Đen', 'Trắng', 'Đỏ'];
                            foreach ($colors as $c): 
                                $isChecked = (isset($_GET['color']) && $_GET['color'] == $c) ? 'checked' : '';
                                $colorId = 'color_' . md5((string)$c);
                            ?>
                                <input type="radio" class="btn-check" name="color" id="<?= $colorId ?>" value="<?= htmlspecialchars((string)$c) ?>" <?= $isChecked ?> autocomplete="off" onchange="this.form.submit()">
                                <label class="btn btn-outline-dark btn-sm rounded-pill" for="<?= $colorId ?>"><?= htmlspecialchars((string)$c) ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Sắp xếp ẩn trong form -->
                    <?php if (!empty($_GET['sort'])): ?>
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort']) ?>">
                    <?php endif; ?>

                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products" class="btn btn-outline-secondary w-100 mt-2 btn-sm">Xóa tất cả bộ lọc</a>
                </form>
            </div>
        </div>

        <!-- MAIN PRODUCT DISPLAY -->
        <div class="col-lg-9">
            
            <div id="product-grid-container">
                <?php require __DIR__ . '/partials/product_grid.php'; ?>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    
    // Hàm gọi AJAX chung
    function fetchProducts(urlStr) {
        document.getElementById('product-grid-container').style.opacity = '0.5';
        
        fetch(urlStr, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('product-grid-container').innerHTML = html;
            document.getElementById('product-grid-container').style.opacity = '1';
            // Đổi URL trên trình duyệt mà không tải lại trang
            window.history.pushState({}, '', urlStr);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('product-grid-container').style.opacity = '1';
        });
    }

    // Khi người dùng submit form (bấm Enter tìm kiếm, hoặc trigger từ JS)
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const searchParams = new URLSearchParams(formData);
        
        // Loại bỏ các param rỗng
        for (const [key, value] of [...searchParams.entries()]) {
            if (!value) {
                searchParams.delete(key);
            }
        }
        
        const urlStr = form.action + '?' + searchParams.toString();
        fetchProducts(urlStr);
    });

    // Khi click vào số phân trang
    document.addEventListener('click', function(e) {
        if (e.target.closest('.ajax-pagination a')) {
            e.preventDefault();
            let page = e.target.closest('a').getAttribute('data-page');
            if (!page) return;
            
            // Lấy lại các param hiện tại từ form
            const formData = new FormData(form);
            const searchParams = new URLSearchParams(formData);
            
            for (const [key, value] of [...searchParams.entries()]) {
                if (!value) searchParams.delete(key);
            }
            searchParams.set('page', page);
            
            const urlStr = form.action + '?' + searchParams.toString();
            fetchProducts(urlStr);
        }
    });

    const inputs = form.querySelectorAll('input[type="radio"], input[type="checkbox"]');
    inputs.forEach(input => {
        input.onchange = null; // Triệt để xóa onchange inline
        input.removeAttribute('onchange'); 
        input.addEventListener('change', () => {
            const formData = new FormData(form);
            const searchParams = new URLSearchParams(formData);
            for (const [key, value] of [...searchParams.entries()]) {
                if (!value) searchParams.delete(key);
            }
            const urlStr = form.action + '?' + searchParams.toString();
            fetchProducts(urlStr);
        });
    });

});
</script>



<?php require __DIR__ . '/layouts/footer.php'; ?>
