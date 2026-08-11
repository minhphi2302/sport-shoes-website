document.addEventListener('DOMContentLoaded', function() {
    // Back to Top Button
    const btnBackToTop = document.getElementById('btnBackToTop');
    
    if (btnBackToTop) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                btnBackToTop.classList.add('show');
            } else {
                btnBackToTop.classList.remove('show');
            }
        });

        btnBackToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // AJAX Add to Cart for links
    const addToCartLinks = document.querySelectorAll('a[href*="/cart/add/"]');
    addToCartLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart badge
                    const badge = document.querySelector('.badge-badge-count');
                    if (badge) {
                        badge.textContent = data.cart_count;
                    }
                    // Show Toast
                    if (typeof showCartToast === 'function') {
                        showCartToast(data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                } else {
                    // Show Error Toast
                    if (typeof showCartToast === 'function') {
                        showCartToast(data.message || 'Có lỗi xảy ra.', 'error');
                    } else {
                        alert(data.message || 'Có lỗi xảy ra.');
                    }
                }
            })
            .catch(error => console.error('Error adding to cart:', error));
        });
    });

    // AJAX Add to Cart for forms (nút "Thêm vào giỏ")
    const addToCartForms = document.querySelectorAll('form[action*="/cart/add"]');
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Nếu nhấn "Mua ngay" thì submit bình thường (không AJAX)
            const submitter = e.submitter;
            if (submitter && submitter.value === 'buy_now') {
                return;
            }
            
            e.preventDefault();
            const formData = new FormData(this);
            if (submitter && submitter.name) {
                formData.append(submitter.name, submitter.value);
            }
            
            fetch(this.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật badge số lượng giỏ hàng trên header
                    const badge = document.querySelector('.badge-badge-count');
                    if (badge) {
                        badge.textContent = data.cart_count;
                    }
                    // Hiện toast thành công
                    if (typeof showCartToast === 'function') {
                        showCartToast(data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                } else {
                    // Hiện toast lỗi
                    if (typeof showCartToast === 'function') {
                        showCartToast(data.message || 'Có lỗi xảy ra.', 'error');
                    } else {
                        alert(data.message || 'Có lỗi xảy ra.');
                    }
                }
            })
            .catch(error => console.error('Error adding to cart:', error));
        });
    });

    // Hiện toast từ session nếu có (trường hợp redirect bình thường, không phải AJAX)
    // Dùng sessionStorage để đảm bảo chỉ hiện 1 lần, không hiện lại khi F5
    const sessionSuccess = document.getElementById('session-success-message');
    const sessionError   = document.getElementById('session-error-message');

    if (sessionSuccess) {
        const msg = sessionSuccess.dataset.message;
        const key = 'toast_shown_' + btoa(encodeURIComponent(msg)).substring(0, 20);
        if (!sessionStorage.getItem(key)) {
            sessionStorage.setItem(key, '1');
            if (typeof showCartToast === 'function') {
                showCartToast(msg, 'success');
            }
        }
    }

    if (sessionError) {
        const msg = sessionError.dataset.message;
        const key = 'toast_err_' + btoa(encodeURIComponent(msg)).substring(0, 20);
        if (!sessionStorage.getItem(key)) {
            sessionStorage.setItem(key, '1');
            if (typeof showCartToast === 'function') {
                showCartToast(msg, 'error');
            }
        }
    }
});

