// ========== MAIN JAVASCRIPT ========== //

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Jobsite initialized');
    
    initializeNavigation();
    initializeEventListeners();
    initializeFormValidation();
    initializeAvatarPreview();
});

// ========== NAVIGATION ========== //

function initializeNavigation() {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');

    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            this.classList.toggle('active');
        });
        
        // Close menu when clicking on a link
        const navLinks = navMenu.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
            });
        });
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (navMenu && navToggle && !navMenu.contains(e.target) && !navToggle.contains(e.target)) {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');
        }
    });
}

// ========== EVENT LISTENERS ========== //

function initializeEventListeners() {
    // Save job buttons
    const saveJobBtns = document.querySelectorAll('.save-job-btn');
    saveJobBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            saveJob(this);
        });
    });

    // Filter buttons and selects
    const filterSelects = document.querySelectorAll('[name="category"], [name="job_type"]');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            applyFilters();
        });
    });

    // Search form
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="search"]');
            if (!searchInput || !searchInput.value.trim()) {
                e.preventDefault();
                alert('Please enter a search term');
            }
        });
    }

    // Confirm dialogs
    const confirmButtons = document.querySelectorAll('[onclick*="confirm"]');
    confirmButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!window.confirm('Are you sure?')) {
                e.preventDefault();
            }
        });
    });
}

// ========== FORM VALIDATION ========== //

function initializeFormValidation() {
    // Setup validation for all forms
    setupFormValidation('form');
    
    // Password match validation
    const passwordField = document.querySelector('input[name="password"]');
    const confirmPasswordField = document.querySelector('input[name="confirm_password"]');
    
    if (passwordField && confirmPasswordField) {
        setupPasswordMatchValidation('input[name="password"]', 'input[name="confirm_password"]');
    }

    // Email validation
    const emailFields = document.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (this.value && !validateEmail(this.value)) {
                showFieldError(this, 'Please enter a valid email address');
            } else {
                removeFieldError(this);
            }
        });
    });
}

// ========== SAVE JOB ========== //

function saveJob(button) {
    const jobId = button.getAttribute('data-job-id');
    
    if (!jobId) {
        console.error('Job ID not found');
        return;
    }

    const formData = new FormData();
    formData.append('job_id', jobId);
    formData.append('action', 'save-job');

    fetch(window.location.origin + '/jobsite/public/job-detail.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Toggle the heart icon
        if (button.textContent.includes('❤️')) {
            button.textContent = '🤍';
        } else {
            button.textContent = '❤️';
        }
        
        showNotification('Job saved successfully!', 'success');
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving job', 'error');
    });
}

// ========== FILTERS ========== //

function applyFilters() {
    const form = document.querySelector('.filter-form');
    
    if (form) {
        form.submit();
    }
}

// ========== FILE UPLOAD PREVIEW ========== //

function initializeAvatarPreview() {
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validate file
                if (!validateFileType(file, ['image/jpeg', 'image/png', 'image/gif'])) {
                    showNotification('Only image files are allowed', 'error');
                    this.value = '';
                    return;
                }

                if (!validateFileSize(file, 2)) {
                    showNotification('File size must not exceed 2MB', 'error');
                    this.value = '';
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    avatarPreview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// ========== NOTIFICATIONS ========== //

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '10000';
    notification.style.minWidth = '300px';
    notification.style.animation = 'slideDown 0.3s ease';

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideUp 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// ========== UTILITY FUNCTIONS ========== //

// Format currency
function formatCurrency(amount) {
    return '₱' + Number(amount).toLocaleString('en-US');
}

// Format date
function formatDate(date, format = 'YYYY-MM-DD') {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');

    const formats = {
        'YYYY-MM-DD': `${year}-${month}-${day}`,
        'DD/MM/YYYY': `${day}/${month}/${year}`,
        'MM/DD/YYYY': `${month}/${day}/${year}`,
        'YYYY-MM-DD HH:MM': `${year}-${month}-${day} ${hours}:${minutes}`
    };

    return formats[format] || formats['YYYY-MM-DD'];
}

// Debounce function
function debounce(func, delay = 300) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            func.apply(this, args);
        }, delay);
    };
}

// Throttle function
function throttle(func, limit = 300) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Check if element is in viewport
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Scroll to element
function scrollToElement(selector) {
    const element = document.querySelector(selector);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}

// Get URL parameters
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    const results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

// Copy to clipboard
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Copied to clipboard!', 'success');
        }).catch(err => {
            console.error('Failed to copy:', err);
        });
    } else {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showNotification('Copied to clipboard!', 'success');
    }
}

// Parse JSON safely
function parseJSON(jsonString) {
    try {
        return JSON.parse(jsonString);
    } catch (e) {
        console.error('Invalid JSON:', e);
        return null;
    }
}

// Check if device is mobile
function isMobileDevice() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

// Get device type
function getDeviceType() {
    if (isMobileDevice()) {
        return 'mobile';
    }
    
    if (window.innerWidth <= 768) {
        return 'tablet';
    }
    
    return 'desktop';
}

// Log function (for debugging)
function appLog(message, type = 'log') {
    const timestamp = new Date().toLocaleTimeString();
    console.log(`[${timestamp}] ${type.toUpperCase()}: ${message}`);
}

// Error handler
function handleError(error, context = '') {
    console.error(`Error in ${context}:`, error);
    appLog(`Error: ${error.message}`, 'error');
}

// ========== TABLE SORTING ========== //

function makeTableSortable(tableSelector) {
    const table = document.querySelector(tableSelector);
    if (!table) return;

    const headers = table.querySelectorAll('th');
    
    headers.forEach((header, index) => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            sortTable(table, index);
        });
    });
}

function sortTable(table, columnIndex) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    const isAscending = !table.dataset.sortAsc;
    table.dataset.sortAsc = isAscending;

    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent;
        const bValue = b.cells[columnIndex].textContent;

        const comparison = aValue.localeCompare(bValue, undefined, { numeric: true });
        return isAscending ? comparison : -comparison;
    });

    rows.forEach(row => tbody.appendChild(row));
}

// ========== LOCAL STORAGE HELPERS ========== //

function setLocalStorage(key, value) {
    try {
        localStorage.setItem(key, JSON.stringify(value));
        return true;
    } catch (e) {
        console.error('LocalStorage error:', e);
        return false;
    }
}

function getLocalStorage(key, defaultValue = null) {
    try {
        const item = localStorage.getItem(key);
        return item ? JSON.parse(item) : defaultValue;
    } catch (e) {
        console.error('LocalStorage error:', e);
        return defaultValue;
    }
}

function removeLocalStorage(key) {
    try {
        localStorage.removeItem(key);
        return true;
    } catch (e) {
        console.error('LocalStorage error:', e);
        return false;
    }
}

function clearLocalStorage() {
    try {
        localStorage.clear();
        return true;
    } catch (e) {
        console.error('LocalStorage error:', e);
        return false;
    }
}

// ========== SESSION HELPERS ========== //

function setSessionStorage(key, value) {
    try {
        sessionStorage.setItem(key, JSON.stringify(value));
        return true;
    } catch (e) {
        console.error('SessionStorage error:', e);
        return false;
    }
}

function getSessionStorage(key, defaultValue = null) {
    try {
        const item = sessionStorage.getItem(key);
        return item ? JSON.parse(item) : defaultValue;
    } catch (e) {
        console.error('SessionStorage error:', e);
        return defaultValue;
    }
}

// ========== LAZY LOADING ========== //

function initializeLazyLoading() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });

        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    } else {
        // Fallback for browsers without IntersectionObserver
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
        });
    }
}

// ========== ANIMATIONS ========== //

function addAnimation(element, animationClass) {
    element.classList.add(animationClass);
    
    element.addEventListener('animationend', function() {
        this.classList.remove(animationClass);
    }, { once: true });
}

function fadeIn(element, duration = 300) {
    element.style.opacity = '0';
    element.style.transition = `opacity ${duration}ms ease`;
    
    setTimeout(() => {
        element.style.opacity = '1';
    }, 10);
}

function fadeOut(element, duration = 300) {
    element.style.opacity = '1';
    element.style.transition = `opacity ${duration}ms ease`;
    
    setTimeout(() => {
        element.style.opacity = '0';
    }, 10);
}

// ========== KEYBOARD SHORTCUTS ========== //

function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S: Save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            const form = document.querySelector('form');
            if (form) {
                form.submit();
            }
        }

        // Escape: Close modals or dialogs
        if (e.key === 'Escape') {
            const modal = document.querySelector('.modal.active');
            if (modal) {
                modal.classList.remove('active');
            }
        }

        // Ctrl/Cmd + K: Focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.focus();
            }
        }
    });
}

// Initialize keyboard shortcuts
setupKeyboardShortcuts();

// Initialize lazy loading
document.addEventListener('DOMContentLoaded', initializeLazyLoading);

// ========== EXPORT ========== //

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        formatCurrency,
        formatDate,
        debounce,
        throttle,
        isInViewport,
        scrollToElement,
        getUrlParameter,
        copyToClipboard,
        parseJSON,
        isMobileDevice,
        getDeviceType,
        appLog,
        handleError,
        setLocalStorage,
        getLocalStorage,
        removeLocalStorage,
        clearLocalStorage,
        setSessionStorage,
        getSessionStorage,
        makeTableSortable,
        addAnimation,
        fadeIn,
        fadeOut
    };
}