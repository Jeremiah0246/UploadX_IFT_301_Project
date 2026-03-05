/**
 * UploadX Authentication Scripts
 * Password strength, validation, and form handling
 */

// Password visibility toggle
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const btn = input.nextElementSibling;
    
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '👁️‍🗨️';
    } else {
        input.type = 'password';
        btn.textContent = '👁️';
    }
}

// Password strength checker
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('passwordStrengthBar');
    const requirements = {
        length: document.getElementById('req-length'),
        uppercase: document.getElementById('req-uppercase'),
        lowercase: document.getElementById('req-lowercase'),
        number: document.getElementById('req-number')
    };
    
    if (passwordInput && strengthBar) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Check length
            if (password.length >= 8) {
                strength++;
                if (requirements.length) requirements.length.classList.add('met');
            } else {
                if (requirements.length) requirements.length.classList.remove('met');
            }
            
            // Check uppercase
            if (/[A-Z]/.test(password)) {
                strength++;
                if (requirements.uppercase) requirements.uppercase.classList.add('met');
            } else {
                if (requirements.uppercase) requirements.uppercase.classList.remove('met');
            }
            
            // Check lowercase
            if (/[a-z]/.test(password)) {
                strength++;
                if (requirements.lowercase) requirements.lowercase.classList.add('met');
            } else {
                if (requirements.lowercase) requirements.lowercase.classList.remove('met');
            }
            
            // Check number
            if (/[0-9]/.test(password)) {
                strength++;
                if (requirements.number) requirements.number.classList.add('met');
            } else {
                if (requirements.number) requirements.number.classList.remove('met');
            }
            
            // Update strength bar
            strengthBar.className = 'password-strength-bar';
            if (strength === 0) {
                strengthBar.style.width = '0%';
            } else if (strength <= 2) {
                strengthBar.classList.add('weak');
            } else if (strength === 3) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
        });
    }
    
    // Form submission with loading state
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.classList.contains('btn-loading')) {
                submitBtn.classList.add('btn-loading');
            }
        });
    });
    
    // Real-time email validation
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.classList.add('error');
            } else {
                this.classList.remove('error');
            }
        });
    }
    
    // Confirm password match validation
    const confirmPasswordInput = document.getElementById('confirm_password');
    if (confirmPasswordInput && passwordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value && this.value !== passwordInput.value) {
                this.classList.add('error');
            } else {
                this.classList.remove('error');
            }
        });
    }
});

// Show toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    const icon = type === 'success' ? '✓' : type === 'error' ? '⚠️' : 'ℹ️';
    
    toast.innerHTML = `
        <span class="toast-icon">${icon}</span>
        <div class="toast-content">
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">×</button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

// Slide out animation for toast
const style = document.createElement('style');
style.textContent = `
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
