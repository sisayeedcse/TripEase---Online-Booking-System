/**
 * TripEase - Main JavaScript
 * Handles interactive features and UI enhancements
 */

(function() {
    'use strict';

    // ========================================
    // INITIALIZATION
    // ========================================
    document.addEventListener('DOMContentLoaded', function() {
        initSmoothScroll();
        initFormValidation();
        initImagePreview();
        initDatePickers();
        initTooltips();
        initSearchFilters();
        initLoadingStates();
    });

    // ========================================
    // SMOOTH SCROLL
    // ========================================
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // ========================================
    // FORM VALIDATION
    // ========================================
    function initFormValidation() {
        const forms = document.querySelectorAll('.needs-validation');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });

        // Email validation
        const emailInputs = document.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateEmail(this);
            });
        });

        // Password validation
        const passwordInputs = document.querySelectorAll('input[type="password"]');
        passwordInputs.forEach(input => {
            input.addEventListener('input', function() {
                validatePassword(this);
            });
        });

        // Confirm password validation
        const confirmPasswordInputs = document.querySelectorAll('input[name="confirm_password"]');
        confirmPasswordInputs.forEach(input => {
            input.addEventListener('input', function() {
                const passwordInput = document.querySelector('input[name="password"]');
                validateConfirmPassword(passwordInput, this);
            });
        });
    }

    function validateEmail(input) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValid = emailRegex.test(input.value);
        
        if (!isValid && input.value) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        } else if (input.value) {
            input.classList.add('is-valid');
            input.classList.remove('is-invalid');
        }
        
        return isValid;
    }

    function validatePassword(input) {
        const minLength = 6;
        const isValid = input.value.length >= minLength;
        
        if (!isValid && input.value) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            showPasswordStrength(input, input.value);
        } else if (input.value) {
            input.classList.add('is-valid');
            input.classList.remove('is-invalid');
            showPasswordStrength(input, input.value);
        }
        
        return isValid;
    }

    function validateConfirmPassword(passwordInput, confirmInput) {
        const isValid = passwordInput.value === confirmInput.value;
        
        if (!isValid && confirmInput.value) {
            confirmInput.classList.add('is-invalid');
            confirmInput.classList.remove('is-valid');
        } else if (confirmInput.value) {
            confirmInput.classList.add('is-valid');
            confirmInput.classList.remove('is-invalid');
        }
        
        return isValid;
    }

    function showPasswordStrength(input, password) {
        let strength = 0;
        if (password.length >= 6) strength++;
        if (password.length >= 10) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^a-zA-Z\d]/.test(password)) strength++;

        const strengthTexts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
        const strengthColors = ['#f44336', '#ff9800', '#ffc107', '#8bc34a', '#4caf50'];
        
        let strengthIndicator = input.parentElement.querySelector('.password-strength');
        if (!strengthIndicator) {
            strengthIndicator = document.createElement('div');
            strengthIndicator.className = 'password-strength';
            input.parentElement.appendChild(strengthIndicator);
        }
        
        strengthIndicator.textContent = `Password Strength: ${strengthTexts[strength - 1] || 'Very Weak'}`;
        strengthIndicator.style.color = strengthColors[strength - 1] || strengthColors[0];
    }

    // ========================================
    // IMAGE PREVIEW
    // ========================================
    function initImagePreview() {
        const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
        
        imageInputs.forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        let preview = input.parentElement.querySelector('.image-preview');
                        if (!preview) {
                            preview = document.createElement('div');
                            preview.className = 'image-preview mt-2';
                            input.parentElement.appendChild(preview);
                        }
                        preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 200px;">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }

    // ========================================
    // DATE PICKERS
    // ========================================
    function initDatePickers() {
        const dateInputs = document.querySelectorAll('input[type="date"]');
        const today = new Date().toISOString().split('T')[0];
        
        dateInputs.forEach(input => {
            if (!input.hasAttribute('min')) {
                input.setAttribute('min', today);
            }
        });

        // Handle date range validation
        const startDateInputs = document.querySelectorAll('input[name="start_date"]');
        const endDateInputs = document.querySelectorAll('input[name="end_date"]');
        
        startDateInputs.forEach((startInput, index) => {
            const endInput = endDateInputs[index];
            if (endInput) {
                startInput.addEventListener('change', function() {
                    endInput.setAttribute('min', this.value);
                    if (endInput.value && endInput.value < this.value) {
                        endInput.value = this.value;
                    }
                });
            }
        });
    }

    // ========================================
    // TOOLTIPS
    // ========================================
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // ========================================
    // SEARCH FILTERS
    // ========================================
    function initSearchFilters() {
        const filterForm = document.getElementById('searchFilterForm');
        if (!filterForm) return;

        const filterInputs = filterForm.querySelectorAll('input, select');
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Auto-submit form on filter change (optional)
                // filterForm.submit();
            });
        });

        // Price range slider
        const priceRange = document.getElementById('priceRange');
        const priceValue = document.getElementById('priceValue');
        
        if (priceRange && priceValue) {
            priceRange.addEventListener('input', function() {
                priceValue.textContent = formatPrice(this.value);
            });
        }
    }

    // ========================================
    // LOADING STATES
    // ========================================
    function initLoadingStates() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
                    
                    // Re-enable after 10 seconds as fallback
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 10000);
                }
            });
        });
    }

    // ========================================
    // BOOKING FUNCTIONS
    // ========================================
    window.calculateBookingPrice = function(pricePerUnit, startDate, endDate, priceUnit) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        let duration = diffDays;
        if (priceUnit === 'hour') {
            duration = diffDays * 24; // Assuming full days
        }
        
        const totalPrice = pricePerUnit * duration;
        return {
            duration: duration,
            totalPrice: totalPrice,
            formattedPrice: formatPrice(totalPrice)
        };
    };

    window.checkAvailability = function(listingId, startDate, endDate) {
        // This would make an AJAX call to check availability
        return fetch(`/api/check-availability.php?listing_id=${listingId}&start_date=${startDate}&end_date=${endDate}`)
            .then(response => response.json())
            .then(data => data.available)
            .catch(error => {
                console.error('Error checking availability:', error);
                return false;
            });
    };

    // ========================================
    // RATING FUNCTIONS
    // ========================================
    window.initStarRating = function() {
        const ratingContainers = document.querySelectorAll('.star-rating');
        
        ratingContainers.forEach(container => {
            const stars = container.querySelectorAll('.star');
            const input = container.querySelector('input[type="hidden"]');
            
            stars.forEach((star, index) => {
                star.addEventListener('click', function() {
                    const rating = index + 1;
                    input.value = rating;
                    
                    stars.forEach((s, i) => {
                        if (i < rating) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });
                
                star.addEventListener('mouseenter', function() {
                    stars.forEach((s, i) => {
                        if (i <= index) {
                            s.classList.add('hover');
                        } else {
                            s.classList.remove('hover');
                        }
                    });
                });
            });
            
            container.addEventListener('mouseleave', function() {
                stars.forEach(s => s.classList.remove('hover'));
            });
        });
    };

    // ========================================
    // NOTIFICATION FUNCTIONS
    // ========================================
    window.showNotification = function(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show alert-floating`;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    };

    // ========================================
    // CONFIRMATION DIALOGS
    // ========================================
    window.confirmAction = function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    };

    window.confirmDelete = function(itemName, deleteUrl) {
        if (confirm(`Are you sure you want to delete "${itemName}"? This action cannot be undone.`)) {
            window.location.href = deleteUrl;
        }
    };

    // ========================================
    // UTILITY FUNCTIONS
    // ========================================
    function formatPrice(amount) {
        return '৳ ' + parseFloat(amount).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    window.formatPrice = formatPrice;

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    window.debounce = debounce;

    // ========================================
    // LAZY LOADING IMAGES
    // ========================================
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img.lazy').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // ========================================
    // AJAX HELPER
    // ========================================
    window.ajaxRequest = function(url, method = 'GET', data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        if (data && method !== 'GET') {
            options.body = JSON.stringify(data);
        }

        return fetch(url, options)
            .then(response => response.json())
            .catch(error => {
                console.error('AJAX Error:', error);
                throw error;
            });
    };

})();
