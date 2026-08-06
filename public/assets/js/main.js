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
                        badge.textContent = data.cartCount;
                    }
                    // Show modal instead of alert
                    const modalEl = document.getElementById('addToCartModal');
                    if (modalEl) {
                        document.getElementById('modalProductImage').src = data.productImage;
                        document.getElementById('modalProductName').textContent = data.productName;
                        document.getElementById('modalTotalAmount').textContent = data.totalAmount + 'đ';
                        document.getElementById('modalTotalQuantity').textContent = `(${data.cartCount}) sản phẩm`;
                        
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.show();
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(error => console.error('Error adding to cart:', error));
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart badge
                    const badge = document.querySelector('.badge-badge-count');
                    if (badge) {
                        badge.textContent = data.cartCount;
                    }
                    // Show modal instead of alert
                    const modalEl = document.getElementById('addToCartModal');
                    if (modalEl) {
                        document.getElementById('modalProductImage').src = data.productImage;
                        document.getElementById('modalProductName').textContent = data.productName;
                        document.getElementById('modalProductSize').textContent = data.productSize ? data.productSize : '';
                        document.getElementById('modalTotalAmount').textContent = data.totalAmount + 'đ';
                        document.getElementById('modalTotalQuantity').textContent = `(${data.cartCount}) sản phẩm`;
                        
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.show();
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(error => console.error('Error adding to cart:', error));
        });
    });
});
