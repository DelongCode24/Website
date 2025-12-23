// ===== HAMBURGER MENU =====
const hamburger = document.querySelector(".hamburger");
const menu = document.querySelector(".menu");

if (hamburger && menu) {
  hamburger.addEventListener("click", (e) => {
    e.stopPropagation();
    menu.classList.toggle("show");
  });

  menu.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", () => {
      menu.classList.remove("show");
    });
  });

  document.addEventListener("click", (e) => {
    if (!menu.contains(e.target) && !hamburger.contains(e.target)) {
      menu.classList.remove("show");
    }
  });
}

// ===== PREVENT DOUBLE SUBMIT =====
document.querySelectorAll('form').forEach(form => {
  form.addEventListener('submit', function(e) {
    const btn = this.querySelector('button[type="submit"]');
    if (btn && !btn.disabled) {
      btn.disabled = true;
      btn.classList.add('loading');
      
      // Re-enable after 3 seconds in case of validation error
      setTimeout(() => {
        btn.disabled = false;
        btn.classList.remove('loading');
      }, 3000);
    }
  });
});

// ===== TOAST NOTIFICATIONS =====
function showToast(message, type = 'success') {
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.textContent = message;
  document.body.appendChild(toast);
  
  setTimeout(() => {
    toast.style.animation = 'slideIn 0.3s ease reverse';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Auto-show toasts from URL params
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('success')) {
  const successMessages = {
    'updated': 'Account updated successfully!',
    'password': 'Password changed successfully!',
    'reset': 'Password reset successful!'
  };
  const message = successMessages[urlParams.get('success')] || 'Success!';
  showToast(message, 'success');
}

if (urlParams.get('error')) {
  const errorMessages = {
    'csrf': 'Session expired. Please try again.',
    'invalid_credentials': 'Invalid username or password.',
    'exists': 'Username or email already exists.',
    'password_short': 'Password must be at least 8 characters.',
    'email': 'Invalid email address.',
    'password': 'Current password is incorrect.'
  };
  const message = errorMessages[urlParams.get('error')] || 'An error occurred.';
  showToast(message, 'error');
}

// ===== STICKY HEADER ON SCROLL =====
let lastScroll = 0;
const header = document.querySelector('.header');

if (header) {
  window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 100) {
      header.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.1)';
    } else {
      header.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.05)';
    }
    
    lastScroll = currentScroll;
  });
}

// ===== KEYBOARD NAVIGATION =====
// Close menu on ESC key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && menu && menu.classList.contains('show')) {
    menu.classList.remove('show');
    hamburger.focus();
  }
});

// ===== FORM VALIDATION ENHANCEMENTS =====
// Add real-time validation feedback
document.querySelectorAll('input[type="email"]').forEach(input => {
  input.addEventListener('blur', function() {
    if (this.value && !this.validity.valid) {
      this.style.borderColor = 'var(--error)';
    } else {
      this.style.borderColor = '';
    }
  });
});

document.querySelectorAll('input[type="password"][minlength]').forEach(input => {
  input.addEventListener('input', function() {
    const minLength = parseInt(this.getAttribute('minlength'));
    const small = this.parentElement.querySelector('small');
    
    if (small) {
      if (this.value.length > 0 && this.value.length < minLength) {
        small.style.color = 'var(--error)';
        small.textContent = `Password must be at least ${minLength} characters (${this.value.length}/${minLength})`;
      } else if (this.value.length >= minLength) {
        small.style.color = 'var(--success)';
        small.textContent = `Password strength: Good ✓`;
      } else {
        small.style.color = '';
        small.textContent = `Must be at least ${minLength} characters`;
      }
    }
  });
});

// ===== SMOOTH SCROLL FOR ANCHOR LINKS =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    const href = this.getAttribute('href');
    if (href !== '#' && href !== '') {
      e.preventDefault();
      const target = document.querySelector(href);
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    }
  });
});