</main>
<!-- Footer -->
<footer class="bg-dark text-white pt-5 pb-4 mt-5">
    <div class="container text-center text-md-start">
        <div class="row text-center text-md-start">
            <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold text-primary">Shop Giày</h5>
                <p>Nơi cung cấp những mẫu giày thể thao chính hãng, mới nhất với giá tốt nhất thị trường.</p>
            </div>
            
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold">Sản phẩm</h5>
                <p><a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products?category_id=1" class="text-white text-decoration-none">Giày chạy bộ</a></p>
                <p><a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products?category_id=2" class="text-white text-decoration-none">Giày thời trang</a></p>
                <p><a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>/products" class="text-white text-decoration-none">Tất cả sản phẩm</a></p>
            </div>
            
            <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold">Hỗ trợ</h5>
                <p><a href="#" class="text-white text-decoration-none">Tài khoản của tôi</a></p>
                <p><a href="#" class="text-white text-decoration-none">Theo dõi đơn hàng</a></p>
                <p><a href="#" class="text-white text-decoration-none">Chính sách đổi trả</a></p>
            </div>
            
            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold">Liên hệ</h5>
                <p><i class="bi bi-house-door-fill me-3"></i> Hà Nội, Việt Nam</p>
                <p><i class="bi bi-envelope-fill me-3"></i> contact@shopgiay.ai</p>
                <p><i class="bi bi-telephone-fill me-3"></i> +84 123 456 789</p>
            </div>
        </div>
        <hr class="mb-4">
        <div class="row align-items-center">
            <div class="col-md-7 col-lg-8">
                <p>&copy; 2026 Bản quyền thuộc về <strong class="text-primary">Shop Giày</strong>.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
