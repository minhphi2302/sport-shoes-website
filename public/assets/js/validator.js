document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form.needs-validation');
    
    forms.forEach(form => {
        form.setAttribute('novalidate', 'novalidate');
        
        form.addEventListener('submit', function(event) {
            let isValid = true;
            let firstInvalid = null;
            
            const inputs = form.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                if (input.hasAttribute('required') || input.value.trim() !== '') {
                    if (!validateInput(input)) {
                        isValid = false;
                        if (!firstInvalid) firstInvalid = input;
                    }
                }
            });
            
            if (!isValid) {
                event.preventDefault();
                event.stopPropagation();
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
            }
        });
        
        // Xử lý sự kiện blur và input để validate realtime
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateInput(this);
            });
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateInput(this);
                }
            });
        });
    });
    
    function validateInput(input) {
        let isValid = true;
        let errorMessage = '';
        const val = input.value.trim();
        
        // Tìm hoặc tạo the div chứa thông báo lỗi
        let errorDiv = input.parentElement.querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback mt-1';
            input.parentElement.appendChild(errorDiv);
        }
        
        if (input.hasAttribute('required') && val === '') {
            isValid = false;
            let label = '';
            const labelEl = input.closest('.mb-3, .mb-2, .mb-4')?.querySelector('label');
            if (labelEl) {
                label = labelEl.innerText.replace('*', '').trim();
            } else {
                label = input.getAttribute('placeholder') || 'trường này';
            }
            errorMessage = 'Vui lòng nhập ' + label;
        } 
        else if (val !== '') {
            // Kiểm tra Email
            if (input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                isValid = false;
                errorMessage = 'Email không hợp lệ.';
            }
            // Kiểm tra Số điện thoại
            else if (input.type === 'tel' && !/^[0-9]{9,12}$/.test(val)) {
                isValid = false;
                errorMessage = 'Số điện thoại không hợp lệ.';
            }
            // Kiểm tra độ dài tối thiểu
            else if (input.hasAttribute('minlength') && val.length < parseInt(input.getAttribute('minlength'))) {
                isValid = false;
                errorMessage = 'Vui lòng nhập tối thiểu ' + input.getAttribute('minlength') + ' ký tự.';
            }
            // Kiểm tra xác nhận mật khẩu
            else if (input.name === 'password_confirm') {
                const passwordInput = input.closest('form').querySelector('input[name="password"]');
                if (passwordInput && val !== passwordInput.value) {
                    isValid = false;
                    errorMessage = 'Mật khẩu xác nhận không khớp.';
                }
            }
        }
        
        if (!isValid) {
            input.classList.add('is-invalid');
            errorDiv.innerText = errorMessage;
            errorDiv.style.display = 'block';
            return false;
        } else {
            input.classList.remove('is-invalid');
            errorDiv.style.display = 'none';
            return true;
        }
    }
});
