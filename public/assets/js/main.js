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
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update cart badge
                    const badge = document.querySelector('.badge-badge-count');
                    if (badge && data.cartCount !== undefined) {
                        badge.textContent = data.cartCount;
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
                        showCartToast(data.error || data.message || 'Có lỗi xảy ra.', 'error');
                    } else {
                        alert(data.error || data.message || 'Có lỗi xảy ra.');
                    }
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                if (typeof showCartToast === 'function') {
                    showCartToast('Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
                }
            });
        });
    });

    // AJAX Add to Cart for forms
    const addToCartForms = document.querySelectorAll('form[action*="/cart/add"]');
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Check which button was clicked
            const submitter = e.submitter;
            if (submitter && submitter.value === 'buy_now') {
                return; // Let it submit normally
            }
            
            e.preventDefault();
            const formData = new FormData(this);
            // Append action manually if needed since submitter value is not in FormData automatically by all browsers
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
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update cart badge
                    const badge = document.querySelector('.badge-badge-count');
                    if (badge && data.cartCount !== undefined) {
                        badge.textContent = data.cartCount;
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
                        showCartToast(data.error || data.message || 'Có lỗi xảy ra.', 'error');
                    } else {
                        alert(data.error || data.message || 'Có lỗi xảy ra.');
                    }
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                if (typeof showCartToast === 'function') {
                    showCartToast('Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
                }
            });
        });
    });
});
