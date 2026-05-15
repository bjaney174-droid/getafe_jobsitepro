<?php
// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Session - MUST be before session_start()
if (session_status() === PHP_SESSION_NONE) {
    // Set session options BEFORE starting session
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Database
require_once __DIR__ . '/database.php';

// Base URLs
define('BASE_URL', 'http://localhost/JobsitePro/public/');
define('SITE_NAME', 'Getafe Jobsite');
define('SITE_EMAIL', 'info@getafejobsite.com');

// Upload paths (MUST be inside /public so browser can access files)
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('RESUME_PATH', UPLOAD_PATH . 'resumes/');
define('AVATAR_PATH', UPLOAD_PATH . 'avatars/');
define('LOGO_PATH', UPLOAD_PATH . 'company_logos/');

// File size limits (in bytes)
define('MAX_RESUME_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_AVATAR_SIZE', 2 * 1024 * 1024); // 2MB
define('MAX_LOGO_SIZE', 2 * 1024 * 1024); // 2MB

// Allowed file types
define('ALLOWED_RESUME_TYPES', ['pdf', 'doc', 'docx']);
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Security functions
function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(htmlspecialchars(trim($data)));
}

function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getUserType() {
    return $_SESSION['user_type'] ?? null;
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getFullName() {
    return ($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '');
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: " . BASE_URL . "login.php");
        exit();
    }
}

function requireEmployer() {
    requireLogin();
    if (getUserType() !== 'employer') {
        header("Location: " . BASE_URL . "home.php");
        exit();
    }
}

function requireJobSeeker() {
    requireLogin();
    if (getUserType() !== 'jobseeker') {
        header("Location: " . BASE_URL . "home.php");
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (getUserType() !== 'admin') {
        header("Location: " . BASE_URL . "home.php");
        exit();
    }
}

// Helper functions
function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d > 30) {
        return $ago->format('M d, Y');
    } else if ($diff->d > 0) {
        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    } else if ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    } else if ($diff->i > 0) {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    } else {
        return 'Just now';
    }
}

function formatSalary($min, $max) {
    if ($min && $max) {
        return '₱' . number_format($min) . ' - ₱' . number_format($max);
    }
    return 'Not specified';
}

// Logging function
function log_action($action, $details = '') {
    global $conn;
    $user_id = getUserId() ?? 0;
    $details = sanitize($details);
    $sql = "INSERT INTO admin_logs (admin_id, action, details) VALUES ('$user_id', '$action', '$details')";
    $conn->query($sql);
}

// Get setting value
function getSetting($key, $default = '') {
    global $conn;
    $key = sanitize($key);
    $result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = '$key'");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['setting_value'];
    }
    return $default;
}

// Email validation
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// URL validation
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
}

?>
