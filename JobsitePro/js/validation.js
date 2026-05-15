// ========== FORM VALIDATION ========== //

// Validate email format
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Validate password strength
function validatePassword(password) {
    if (password.length < 8) {
        return { valid: false, message: 'Password must be at least 8 characters' };
    }
    
    if (!/[A-Z]/.test(password)) {
        return { valid: false, message: 'Password must contain at least one uppercase letter' };
    }
    
    if (!/[a-z]/.test(password)) {
        return { valid: false, message: 'Password must contain at least one lowercase letter' };
    }
    
    if (!/[0-9]/.test(password)) {
        return { valid: false, message: 'Password must contain at least one number' };
    }
    
    return { valid: true, message: 'Password is strong' };
}

// Validate phone number
function validatePhone(phone) {
    const phoneRegex = /^[0-9\-\+\s\(\)]{7,}$/;
    return phoneRegex.test(phone);
}

// Validate URL
function validateUrl(url) {
    try {
        new URL(url);
        return true;
    } catch (error) {
        return false;
    }
}

// Validate required field
function validateRequired(value) {
    return value && value.trim().length > 0;
}

// Validate minimum length
function validateMinLength(value, minLength) {
    return value && value.length >= minLength;
}

// Validate maximum length
function validateMaxLength(value, maxLength) {
    return value && value.length <= maxLength;
}

// Validate number range
function validateRange(value, min, max) {
    const num = parseFloat(value);
    return !isNaN(num) && num >= min && num <= max;
}

// Validate date format (YYYY-MM-DD)
function validateDate(dateString) {
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    if (!dateRegex.test(dateString)) {
        return false;
    }
    
    const date = new Date(dateString);
    return date instanceof Date && !isNaN(date);
}

// Validate file size
function validateFileSize(file, maxSizeInMB) {
    const maxSizeInBytes = maxSizeInMB * 1024 * 1024;
    return file.size <= maxSizeInBytes;
}

// Validate file type
function validateFileType(file, allowedTypes) {
    return allowedTypes.includes(file.type);
}

// Validate file extension
function validateFileExtension(filename, allowedExtensions) {
    const extension = filename.split('.').pop().toLowerCase();
    return allowedExtensions.includes(extension);
}

// Validate credit card (basic Luhn algorithm)
function validateCreditCard(cardNumber) {
    const sanitized = cardNumber.replace(/\D/g, '');
    
    if (sanitized.length < 13 || sanitized.length > 19) {
        return false;
    }
    
    let sum = 0;
    let isEven = false;
    
    for (let i = sanitized.length - 1; i >= 0; i--) {
        let digit = parseInt(sanitized.charAt(i), 10);
        
        if (isEven) {
            digit *= 2;
            if (digit > 9) {
                digit -= 9;
            }
        }
        
        sum += digit;
        isEven = !isEven;
    }
    
    return sum % 10 === 0;
}

// Validate username (alphanumeric, underscore, hyphen)
function validateUsername(username) {
    const usernameRegex = /^[a-zA-Z0-9_-]{3,20}$/;
    return usernameRegex.test(username);
}

// Validate strong email
function validateStrongEmail(email) {
    const strongEmailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
    return strongEmailRegex.test(email);
}

// Validate salary format
function validateSalary(salary) {
    const salaryNum = parseFloat(salary);
    return !isNaN(salaryNum) && salaryNum > 0;
}

// Validate job title (no special characters except spaces)
function validateJobTitle(title) {
    const titleRegex = /^[a-zA-Z0-9\s\-&,()]{3,100}$/;
    return titleRegex.test(title);
}

// Validate company name
function validateCompanyName(name) {
    const nameRegex = /^[a-zA-Z0-9\s\-&,.()]{2,100}$/;
    return nameRegex.test(name);
}

// Real-time form validation setup
function setupFormValidation(formSelector) {
    const form = document.querySelector(formSelector);
    
    if (!form) return;
    
    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        // Validate on blur
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        // Clear error on input
        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                this.classList.remove('error');
                const errorMsg = this.parentElement.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });
    });
    
    // Validate entire form on submit
    form.addEventListener('submit', function(e) {
        const isValid = validateForm(this);
        if (!isValid) {
            e.preventDefault();
        }
    });
}

// Validate individual field
function validateField(field) {
    let isValid = true;
    let errorMessage = '';
    
    const fieldName = field.name;
    const fieldValue = field.value.trim();
    const fieldType = field.type;
    
    // Check required
    if (field.hasAttribute('required') && !validateRequired(fieldValue)) {
        isValid = false;
        errorMessage = 'This field is required';
    }
    
    // Email validation
    if (fieldType === 'email' && fieldValue) {
        if (!validateEmail(fieldValue)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        }
    }
    
    // Password validation
    if (fieldType === 'password' && fieldValue) {
        const passwordCheck = validatePassword(fieldValue);
        if (!passwordCheck.valid) {
            isValid = false;
            errorMessage = passwordCheck.message;
        }
    }
    
    // Phone validation
    if (field.hasAttribute('data-validate') && field.getAttribute('data-validate') === 'phone' && fieldValue) {
        if (!validatePhone(fieldValue)) {
            isValid = false;
            errorMessage = 'Please enter a valid phone number';
        }
    }
    
    // URL validation
    if (fieldType === 'url' && fieldValue) {
        if (!validateUrl(fieldValue)) {
            isValid = false;
            errorMessage = 'Please enter a valid URL';
        }
    }
    
    // Min length
    if (field.hasAttribute('minlength')) {
        const minLength = parseInt(field.getAttribute('minlength'));
        if (fieldValue && !validateMinLength(fieldValue, minLength)) {
            isValid = false;
            errorMessage = `Minimum length is ${minLength} characters`;
        }
    }
    
    // Max length
    if (field.hasAttribute('maxlength')) {
        const maxLength = parseInt(field.getAttribute('maxlength'));
        if (fieldValue && !validateMaxLength(fieldValue, maxLength)) {
            isValid = false;
            errorMessage = `Maximum length is ${maxLength} characters`;
        }
    }
    
    // Custom salary validation
    if (field.hasAttribute('data-validate') && field.getAttribute('data-validate') === 'salary' && fieldValue) {
        if (!validateSalary(fieldValue)) {
            isValid = false;
            errorMessage = 'Please enter a valid salary amount';
        }
    }
    
    // Show/hide error message
    if (!isValid) {
        field.classList.add('error');
        showFieldError(field, errorMessage);
    } else {
        field.classList.remove('error');
        removeFieldError(field);
    }
    
    return isValid;
}

// Validate entire form
function validateForm(form) {
    const inputs = form.querySelectorAll('input, textarea, select');
    let isFormValid = true;
    
    inputs.forEach(input => {
        const fieldValid = validateField(input);
        if (!fieldValid) {
            isFormValid = false;
        }
    });
    
    return isFormValid;
}

// Show field error
function showFieldError(field, message) {
    // Remove existing error message
    const existingError = field.parentElement.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Create and show error message
    const errorElement = document.createElement('small');
    errorElement.className = 'error-message';
    errorElement.style.color = '#dc3545';
    errorElement.style.display = 'block';
    errorElement.style.marginTop = '5px';
    errorElement.textContent = message;
    
    field.parentElement.appendChild(errorElement);
}

// Remove field error
function removeFieldError(field) {
    const errorElement = field.parentElement.querySelector('.error-message');
    if (errorElement) {
        errorElement.remove();
    }
}

// Validate password match
function validatePasswordMatch(passwordField, confirmPasswordField) {
    return passwordField.value === confirmPasswordField.value;
}

// Add password match validation
function setupPasswordMatchValidation(passwordSelector, confirmPasswordSelector) {
    const passwordField = document.querySelector(passwordSelector);
    const confirmPasswordField = document.querySelector(confirmPasswordSelector);
    
    if (!passwordField || !confirmPasswordField) return;
    
    confirmPasswordField.addEventListener('blur', function() {
        if (!validatePasswordMatch(passwordField, this)) {
            this.classList.add('error');
            showFieldError(this, 'Passwords do not match');
        } else {
            this.classList.remove('error');
            removeFieldError(this);
        }
    });
}

// Sanitize input
function sanitizeInput(input) {
    const div = document.createElement('div');
    div.textContent = input;
    return div.innerHTML;
}

// Trim and clean input
function cleanInput(input) {
    return input.trim().replace(/\s+/g, ' ');
}

// Export validation functions
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        validateEmail,
        validatePassword,
        validatePhone,
        validateUrl,
        validateRequired,
        validateMinLength,
        validateMaxLength,
        validateRange,
        validateDate,
        validateFileSize,
        validateFileType,
        validateFileExtension,
        validateCreditCard,
        validateUsername,
        validateStrongEmail,
        validateSalary,
        validateJobTitle,
        validateCompanyName,
        setupFormValidation,
        validateField,
        validateForm,
        validatePasswordMatch,
        setupPasswordMatchValidation,
        sanitizeInput,
        cleanInput
    };
}