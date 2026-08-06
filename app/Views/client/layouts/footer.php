
<footer class="footer" id="contact-footer">
    <div class="container">

        <div class="row">

            <!-- Cột 1 -->
            <div class="col-lg-4 col-md-6 mb-2 pe-lg-4">

                <a class="mb-2 d-inline-block" href="<?= base_url('/') ?>">
                        <img src="<?= base_url('image/logo.webp') ?>" alt="Logo Footer" style="height: 46px; filter: brightness(0) invert(1); object-fit: contain;">
                    </a>
                    <p class="footer-desc mb-2">
                        Hệ thống bán lẻ giày thể thao chính hãng hàng đầu Việt Nam. Cam kết 100% hàng chính hãng, giao hàng nhanh toàn quốc.
                    </p>

                <ul class="footer-info">
                    <li>
                        <i class="fa-solid fa-location-dot"></i>
                        123 Hà Nội
                    </li>

                    <li>
                        <i class="fa-solid fa-phone"></i>
                        Hotline: 1900 6868
                    </li>

                    <li>
                        <i class="fa-solid fa-envelope"></i>
                        Email: ANTA@gmail.com
                    </li>
                </ul>

                <p class="copyright">
                    © <?=date('Y')?> SPORT SHOES
                </p>

            </div>

            <!-- Cột 2 -->

            <div class="col-lg-2 col-md-6 mb-2">

                <h5 class="footer-title">CHÍNH SÁCH</h5>

                <ul class="footer-link">
                    <li><a href="#">Chính sách vận chuyển</a></li>

                    <li><a href="#">Chính sách đổi trả</a></li>

                    <li><a href="#">Chính sách bảo hành</a></li>

                    <li><a href="#">Chính sách bảo mật</a></li>

                    <li><a href="#">Điều khoản sử dụng</a></li>

                </ul>

            </div>

            <!-- Cột 3 -->

            <div class="col-lg-2 col-md-6 mb-2">

                <h5 class="footer-title">HỖ TRỢ KHÁCH HÀNG</h5>

                <ul class="footer-link">

                    <li><a href="#">Giới thiệu</a></li>

                    <li><a href="#">Hệ thống cửa hàng</a></li>

                    <li><a href="#">Kiểm tra đơn hàng</a></li>

                    <li><a href="#">Hướng dẫn mua hàng</a></li>

                    <li><a href="#">Liên hệ</a></li>

                </ul>

            </div>

            <!-- Cột 4 -->

            <div class="col-lg-3 col-md-6">

                <h5 class="footer-title">
                    ĐĂNG KÝ NHẬN TIN
                </h5>

                <form>
                    <div class="newsletter">

                        <input
                            type="email"
                            placeholder="Nhập email">

                        <button>
                            Đăng ký
                        </button>

                    </div>

                </form>

                <div class="social">

                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>

                    <a href="#"><i class="fa-brands fa-instagram"></i></a>

                    <a href="#"><i class="fa-brands fa-tiktok"></i></a>

                    <a href="#"><i class="fa-solid fa-magnifying-glass"></i></a>

                </div>

            </div>

        </div>

        <div class="footer-bottom">

            Công ty TNHH SPORT SHOES Việt Nam - MST: 0123456789 -
            Địa chỉ: Hà Nội - Việt Nam
        </div>

    </div>
    
    <!-- Back to top button -->
    <a href="#" class="btn-back-to-top" id="btnBackToTop">
        <i class="fa-solid fa-arrow-up"></i>
    </a>
</footer>

<!-- Modal Thêm vào giỏ hàng -->
<div class="modal fade" id="addToCartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 bg-light rounded-top-4 pb-2 pt-3">
                <h5 class="modal-title fs-6 text-success fw-bold">
                    <i class="fa-solid fa-circle-check me-2"></i> Thêm vào giỏ hàng thành công
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-2">
                <div class="d-flex align-items-center mb-4">
                    <img id="modalProductImage" src="" alt="Product" class="img-fluid rounded border me-3" style="width: 80px; height: 80px; object-fit: contain;">
                    <div>
                        <h6 id="modalProductName" class="mb-0 fw-bold"></h6>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                    <span class="text-primary fw-semibold" style="color: #0d6efd !important;">Giỏ hàng hiện có</span>
                    <div class="text-end">
                        <h5 id="modalTotalAmount" class="text-danger fw-bold mb-0"></h5>
                        <small id="modalTotalQuantity" class="text-muted"></small>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <a href="<?= base_url('checkout') ?>" class="btn btn-outline-dark w-100 py-2 rounded-3">Thanh toán</a>
                    </div>
                    <div class="col-6">
                        <a href="<?= base_url('cart') ?>" class="btn btn-dark w-100 py-2 rounded-3">Xem giỏ hàng</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle (bao gồm Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= base_url('assets/js/main.js?v=' . time()) ?>"></script>
</body>
</html>