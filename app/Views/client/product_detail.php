<?php
require __DIR__ . '/layouts/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products" class="text-decoration-none text-dark">Sản phẩm</a></li>
                <li class="breadcrumb-item active text-red fw-semibold" aria-current="page"><?= htmlspecialchars($product['name'] ?? 'Chi tiết sản phẩm') ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-5">
        
        <!-- BÊN TRÁI: GALLERY ÁNH -->
        <div class="col-lg-6">
            <div class="product-detail-gallery">
                <!-- Ảnh lớn chính -->
                <?php 
                    $fallbackImg = 'image/slide/slide' . (($product['product_id'] % 3) + 1) . '.avif';
                    $imgSrc = !empty($product['image_url']) ? $product['image_url'] : $fallbackImg;
                ?>
                <img id="mainProductImg" src="<?= htmlspecialchars(base_url($imgSrc)) ?>" class="main-img" alt="<?= htmlspecialchars($product['name'] ?? 'Product') ?>">
            </div>
        </div>

        <!-- BÊN PHẢI: THÔNG TIN SẢN PHẨM & NÚT MUA -->
        <div class="col-lg-6">
            <div class="ps-lg-3">
                <span class="badge bg-dark text-white px-3 py-2 text-uppercase mb-2">Thương hiệu: <?= htmlspecialchars($product['brand_name'] ?? 'Chính Hãng') ?></span>
                <h1 class="product-detail-title mb-3"><?= htmlspecialchars($product['name'] ?? 'Nike Air Zoom Pegasus 39') ?></h1>

                <!-- Đánh giá sao & Tình trạng kho -->
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning me-3">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                        <span class="text-dark fw-bold ms-1">(4.9/5 - 128 đánh giá)</span>
                    </div>
                    <div class="border-start ps-3 <?= ($product['quantity'] ?? 50) > 0 ? 'text-success' : 'text-danger' ?> fw-semibold">
                        <?php if (($product['quantity'] ?? 50) > 0): ?>
                            <i class="fa-solid fa-check-circle me-1"></i> Còn hàng (<?= htmlspecialchars($product['quantity'] ?? 50) ?> sản phẩm)
                        <?php else: ?>
                            <i class="fa-solid fa-circle-xmark me-1"></i> Hết hàng
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Giá bán -->
                <div class="bg-light p-3 rounded-3 mb-4 d-flex align-items-baseline gap-3">
                    <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                        <span class="fs-2 fw-bold text-red"><?= number_format($product['sale_price'], 0, ',', '.') ?>đ</span>
                        <span class="fs-5 text-muted text-decoration-line-through"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                        <span class="badge bg-red ms-auto fs-6">Tiết kiệm <?= number_format($product['price'] - $product['sale_price'], 0, ',', '.') ?>đ</span>
                    <?php else: ?>
                        <span class="fs-2 fw-bold text-red"><?= number_format($product['price'] ?? 3000000, 0, ',', '.') ?>đ</span>
                    <?php endif; ?>
                </div>

                <!-- Mô tả ngắn -->
                <p class="text-secondary mb-4">
                    <?= htmlspecialchars($product['description'] ?? 'Mẫu giày thể thao với thiết kế hiện đại, thoáng khí tối đa, đế cao su chống trượt tuyệt đối. Mang lại cảm giác êm ái cho mọi chuyển động hàng ngày.') ?>
                </p>

                <!-- Form chọn Size, Màu & Số lượng -->
                <form action="<?= ($_ENV['APP_URL'] ?? '') ?>/cart/add" method="POST">
                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?? 1 ?>">

                    <!-- Chọn Size -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-uppercase fs-7 d-block">Chọn Kích thước (Size EU):</label>
                        <div class="size-btn-group">
                            <?php $sizes = [38, 39, 40, 41, 42, 43, 44]; ?>
                            <?php foreach ($sizes as $idx => $s): ?>
                                <input type="radio" class="btn-check" name="size" id="detail_size_<?= $s ?>" value="<?= $s ?>" autocomplete="off" <?= $idx === 2 ? 'checked' : '' ?>>
                                <label class="btn btn-outline-dark" for="detail_size_<?= $s ?>"><?= $s ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Chọn Màu Sắc -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-uppercase fs-7 d-block">Chọn Phối Màu:</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="radio" class="btn-check" name="color" id="color_black" value="Black" checked>
                            <label class="btn btn-outline-dark btn-sm rounded-pill" for="color_black"><i class="fa-solid fa-circle text-dark me-1"></i> Đen / Trắng</label>

                            <input type="radio" class="btn-check" name="color" id="color_red" value="Red">
                            <label class="btn btn-outline-dark btn-sm rounded-pill" for="color_red"><i class="fa-solid fa-circle text-danger me-1"></i> Đỏ Sport</label>
                        </div>
                    </div>

                    <!-- Chọn Số lượng -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-uppercase fs-7 d-block">Số lượng:</label>
                        <div class="quantity-selector input-group" style="width: 150px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="decrementQuantity()" <?= (($product['quantity'] ?? 50) <= 0) ? 'disabled' : '' ?>><i class="fa-solid fa-minus"></i></button>
                            <input type="number" id="quantityInput" name="quantity" class="form-control text-center" value="1" min="1" max="<?= $product['quantity'] ?? 50 ?>" <?= (($product['quantity'] ?? 50) <= 0) ? 'disabled' : '' ?>>
                            <button class="btn btn-outline-secondary" type="button" onclick="incrementQuantity()" <?= (($product['quantity'] ?? 50) <= 0) ? 'disabled' : '' ?>><i class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>

                    <!-- Nút Thêm Giỏ & Mua Ngay -->
                    <div class="d-grid gap-2 d-md-flex mt-4">
                        <button type="submit" name="action" value="add" class="btn btn-outline-dark-custom btn-lg flex-fill py-3" <?= (($product['quantity'] ?? 50) <= 0) ? 'disabled' : '' ?>>
                            <i class="fa-solid fa-cart-plus me-2"></i> THÊM VÀO GIỎ
                        </button>
                        <button type="submit" name="action" value="buy_now" class="btn btn-red btn-lg flex-fill py-3" <?= (($product['quantity'] ?? 50) <= 0) ? 'disabled' : '' ?>>
                            <i class="fa-solid fa-bolt me-2"></i> MUA NGAY
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PHÍA DƯỚI: TAB MÔ TẢ CHI TIẾT & ĐÁNH GIÁ -->
    <div class="mt-5 pt-4">
        <ul class="nav nav-tabs nav-tabs-custom mb-4" id="productTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-uppercase" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc-tab-pane" type="button" role="tab">Mô tả chi tiết</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-uppercase" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs-tab-pane" type="button" role="tab">Thông số kỹ thuật</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-uppercase" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-tab-pane" type="button" role="tab">Đánh giá (128)</button>
            </li>
        </ul>
        <div class="tab-content border rounded-3 p-4 bg-white" id="productTabContent">
            <!-- Tab 1: Mô tả -->
            <div class="tab-pane fade show active" id="desc-tab-pane" role="tabpanel">
                <h4>Đặc điểm nổi bật của <?= htmlspecialchars($product['name'] ?? 'sản phẩm') ?></h4>
                <p>Được thiết kế tối ưu cho các hoạt động thể thao cường độ cao cũng như dạo phố hàng ngày. Chất liệu vải lưới dệt cao cấp giúp đôi chân luôn thoáng mát, không bị hầm bí suốt cả ngày dài.</p>
                <ul>
                    <li>Đế giữa trang bị đệm khí tiên tiến mang lại cảm giác êm ái vượt trội.</li>
                    <li>Đế ngoài làm bằng cao su chịu lực với các rãnh bám sâu chống trơn trượt trên mọi địa hình.</li>
                    <li>Thiết kế ôm sát cổ chân hỗ trợ di chuyển linh hoạt và giảm thiểu chấn thương.</li>
                </ul>
            </div>

            <!-- Tab 2: Thông số -->
            <div class="tab-pane fade" id="specs-tab-pane" role="tabpanel">
                <table class="table table-striped table-bordered mb-0">
                    <tbody>
                        <tr><th width="30%">Thương hiệu</th><td><?= htmlspecialchars($product['brand_name'] ?? 'Nike') ?></td></tr>
                        <tr><th>Mã SKU</th><td><?= htmlspecialchars($product['sku'] ?? 'NK-RN-001') ?></td></tr>
                        <tr><th>Chất liệu mặt trên (Upper)</th><td>Vải lưới thoáng khí Flyknit / Synthetic</td></tr>
                        <tr><th>Chất liệu đế (Outsole)</th><td>Cao su đúc nguyên khối chịu ma sát</td></tr>
                        <tr><th>Kiểu dáng</th><td>Thể thao Running</td></tr>
                        <tr><th>Xuất xứ</th><td>Chính hãng nhập khẩu</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Tab 3: Đánh giá -->
            <div class="tab-pane fade" id="reviews-tab-pane" role="tabpanel">
                <div class="d-flex align-items-center mb-4">
                    <div class="me-4 text-center">
                        <h1 class="display-4 fw-bold text-red mb-0">4.9</h1>
                        <div class="text-warning fs-5">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <small class="text-muted">128 đánh giá</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SẢN PHẨM LIÊN QUAN -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="mt-5 pt-4">
            <h3 class="section-title">Sản phẩm liên quan</h3>
            <div class="row g-4">
                <?php foreach ($relatedProducts as $rel): ?>
                    <div class="col-6 col-md-3">
                        <div class="product-card">
                            <div class="product-img-wrapper">
                                <?php 
                                    $fallbackImgRel = 'image/slide/slide' . (($rel['product_id'] % 3) + 1) . '.avif';
                                    $imgSrcRel = !empty($rel['image_url']) ? $rel['image_url'] : $fallbackImgRel;
                                ?>
                                <img src="<?= htmlspecialchars(base_url($imgSrcRel)) ?>" alt="<?= htmlspecialchars($rel['name']) ?>">
                                <div class="product-actions-overlay">
                                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $rel['product_id'] ?>" class="btn-action-icon"><i class="fa-regular fa-eye"></i></a>
                                </div>
                            </div>
                            <div class="product-body">
                                <span class="product-brand-tag"><?= htmlspecialchars($rel['brand_name'] ?? 'Chính Hãng') ?></span>
                                <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/product/<?= $rel['product_id'] ?>" class="product-title"><?= htmlspecialchars($rel['name']) ?></a>
                                <div class="product-price-box">
                                    <span class="product-price"><?= number_format($rel['sale_price'] ?? $rel['price'], 0, ',', '.') ?>đ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
function changeImage(src) {
    document.getElementById('mainProductImg').src = src;
    document.querySelectorAll('.thumbnail-img').forEach(img => img.classList.remove('active'));
    event.target.classList.add('active');
}

function incrementQuantity() {
    let input = document.getElementById('quantityInput');
    input.value = parseInt(input.value) + 1;
}

function decrementQuantity() {
    let input = document.getElementById('quantityInput');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}
</script>

<?php
require __DIR__ . '/layouts/footer.php';
?>
