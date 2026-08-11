</main>

<!-- Footer -->
<footer id="contact-footer" class="bg-dark text-white pt-5 pb-4 mt-auto border-top border-secondary">
    <div class="container text-center text-md-start">
        <div class="row g-4">
            <div class="col-md-4 col-lg-4 col-xl-4 mx-auto">
                <h5 class="text-uppercase mb-4 fw-bold text-primary"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>ANTA STORE</h5>
                <p class="text-secondary small" style="line-height: 1.8;">
                    ANTA Store - Nơi cung cấp những mẫu giày thể thao chính hãng, thời trang và hiệu năng cao nhất. Đồng hành cùng bạn chinh phục mọi mốc thời gian và khoảng cách.
                </p>
                <div class="mt-3">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
            
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto">
                <h6 class="text-uppercase mb-4 fw-bold text-white">Danh Mục</h6>
                <p class="mb-2"><a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products" class="text-secondary text-decoration-none small">Tất cả sản phẩm</a></p>
                <p class="mb-2"><a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products?sort=newest" class="text-secondary text-decoration-none small">Sản phẩm mới nhất</a></p>
                <p class="mb-2"><a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products?sale=1" class="text-secondary text-decoration-none small text-warning"><i class="bi bi-fire me-1"></i> Siêu khuyến mãi</a></p>
            </div>
            
            <div class="col-md-3 col-lg-2 col-xl-2 mx-auto">
                <h6 class="text-uppercase mb-4 fw-bold text-white">Hỗ Trợ Khách Hàng</h6>
                <p class="mb-2"><a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/profile" class="text-secondary text-decoration-none small">Tài khoản của tôi</a></p>
                <p class="mb-2"><a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/orders" class="text-secondary text-decoration-none small">Theo dõi đơn hàng</a></p>
                <p class="mb-2"><a href="#" class="text-secondary text-decoration-none small">Chính sách đổi trả 7 ngày</a></p>
                <p class="mb-2"><a href="#" class="text-secondary text-decoration-none small">Bảo mật thông tin</a></p>
            </div>
            
            <div class="col-md-3 col-lg-3 col-xl-3 mx-auto">
                <h6 class="text-uppercase mb-4 fw-bold text-white">Thông Tin Liên Hệ</h6>
                <p class="text-secondary small mb-2"><i class="bi bi-geo-alt-fill me-2 text-primary"></i> Hà Nội & TP. Hồ Chí Minh, Việt Nam</p>
                <p class="text-secondary small mb-2"><i class="bi bi-envelope-fill me-2 text-primary"></i> support@antastore.vn</p>
                <p class="text-secondary small mb-2"><i class="bi bi-telephone-fill me-2 text-primary"></i> Hotline: 1900 6789 (8:00 - 21:00)</p>
            </div>
        </div>
        <hr class="my-4 border-secondary">
        <div class="row align-items-center">
            <div class="col-md-7 col-lg-8">
                <p class="small text-secondary m-0">&copy; 2026 Bản quyền thuộc về <strong class="text-white">ANTA Store</strong>. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
