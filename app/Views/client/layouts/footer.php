<footer class="footer" id="contact-footer">
    <div class="container">
        <div class="row">
            <!-- Cột 1 -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-title">LIÊN HỆ</h5>
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
            </div>

            <!-- Cột 2 -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-title">VỀ CHÚNG TÔI</h5>
                <ul class="footer-link">
                    <li><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/#about-brand">Giới thiệu về thương hiệu</a></li>
                    <li><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/products">Danh sách cửa hàng</a></li>
                </ul>
            </div>

            <!-- Cột 3 -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-title">HỖ TRỢ KHÁCH HÀNG</h5>
                <ul class="footer-link">
                    <li><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/">Chính sách vận chuyển</a></li>
                    <li><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/">Chính sách đổi trả</a></li>
                    <li><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/">Chính sách bảo hành</a></li>
                    <li><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/">Hướng dẫn mua sắm</a></li>
                    <li><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/">Bảng size</a></li>
                </ul>
            </div>

            <!-- Cột 4 -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-title">ĐĂNG KÝ NHẬN TIN</h5>
                <form>
                    <div class="newsletter">
                        <input type="email" placeholder="Nhập email">
                        <button>Đăng ký</button>
                    </div>
                </form>
                <div class="social">
                    <a href="https://facebook.com" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://instagram.com" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://tiktok.com" target="_blank"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            Công ty TNHH ANTA STORE Việt Nam - MST: 0123456789 - Địa chỉ: Hà Nội - Việt Nam
        </div>
    </div>
    
    <!-- Back to top button -->
    <a href="#" class="btn-back-to-top" id="btnBackToTop">
        <i class="fa-solid fa-arrow-up"></i>
    </a>
</footer>

<!-- Custom Toast Notification -->
<div id="cartToast" class="custom-toast shadow">
    <div class="toast-progress"></div>
    <div class="toast-content d-flex align-items-center">
        <div class="toast-icon">
            <i class="fa-solid fa-check" id="toastIcon"></i>
        </div>
        <div class="toast-text ms-3 text-start flex-grow-1">
            <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;" id="toastTitle">Thành công</h6>
            <p class="mb-0" style="font-size: 0.8rem;" id="cartToastMessage">Sản phẩm đã được thêm vào giỏ hàng.</p>
        </div>
        <button type="button" class="btn-close btn-close-white ms-2" onclick="hideCartToast()"></button>
    </div>
</div>

<script>
let toastTimeout;
function showCartToast(message = 'Sản phẩm đã được thêm vào giỏ hàng.', type = 'success') {
    const toast = document.getElementById('cartToast');
    const msgEl = document.getElementById('cartToastMessage');
    const titleEl = document.getElementById('toastTitle');
    const iconEl = document.getElementById('toastIcon');
    const progressEl = toast.querySelector('.toast-progress');
    
    if(msgEl) msgEl.innerText = message;
    
    if (type === 'error') {
        toast.style.backgroundColor = '#d32f2f'; // Red for error
        if (progressEl) progressEl.style.backgroundColor = '#ffcdd2';
        if (titleEl) titleEl.innerText = 'Thất bại';
        if (iconEl) iconEl.className = 'fa-solid fa-xmark';
    } else {
        toast.style.backgroundColor = '#388e3c'; // Green for success
        if (progressEl) progressEl.style.backgroundColor = '#cddc39';
        if (titleEl) titleEl.innerText = 'Thành công';
        if (iconEl) iconEl.className = 'fa-solid fa-check';
    }
    
    // Reset animation
    toast.classList.remove('show');
    void toast.offsetWidth; // trigger reflow
    toast.classList.add('show');
    
    clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
        hideCartToast();
    }, 3000);
}

function hideCartToast() {
    const toast = document.getElementById('cartToast');
    if(toast) {
        toast.classList.remove('show');
    }
}

// AJAX Add to Cart Handler
document.addEventListener('DOMContentLoaded', function() {
    // Handle quick add to cart buttons (not in detail page)
    document.querySelectorAll('.btn-cart-hover:not(.btn-out-of-stock)').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            
            // Send AJAX request
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCartToast(data.message, 'success');
                    
                    // Update cart count in header
                    const cartCountEl = document.querySelector('.badge-badge-count');
                    if (cartCountEl && data.cartCount !== undefined) {
                        cartCountEl.textContent = data.cartCount;
                    }
                } else {
                    showCartToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCartToast('Không thể thêm sản phẩm vào giỏ hàng', 'error');
            });
        });
    });
});

</script>

<!-- Bootstrap 5 JS Bundle (bao gồm Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= base_url('assets/js/main.js?v=' . time()) ?>"></script>
</body>
</html>
