<?php
/**
 * TripEase Configuration File
 * Contains all application settings and constants
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tripease');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'TripEase');
define('APP_URL', 'http://localhost/TripEase');
define('APP_VERSION', '1.0.0');

// Path Configuration
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
define('ASSETS_PATH', ROOT_PATH . '/assets/');

// URL Configuration
define('BASE_URL', APP_URL . '/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('UPLOADS_URL', BASE_URL . 'uploads/');

// Upload Directories
define('USER_UPLOAD_DIR', UPLOAD_PATH . 'users/');
define('PROVIDER_UPLOAD_DIR', UPLOAD_PATH . 'providers/');
define('LISTING_UPLOAD_DIR', UPLOAD_PATH . 'listings/');

// Security Configuration
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_COST', 10);
define('SESSION_LIFETIME', 3600 * 24); // 24 hours
define('RESET_TOKEN_EXPIRY', 3600); // 1 hour

// Pagination
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// File Upload Configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// Booking Configuration
define('CANCELLATION_HOURS', 24);
define('MAX_BOOKING_DAYS', 30);
define('MIN_BOOKING_DAYS', 1);

// Email Configuration (for future implementation)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-password');
define('SMTP_FROM_EMAIL', 'noreply@tripease.com');
define('SMTP_FROM_NAME', 'TripEase');

// Timezone
date_default_timezone_set('Asia/Dhaka');

// Currency
define('CURRENCY', 'BDT');
define('CURRENCY_SYMBOL', '৳');

// User Roles
define('ROLE_USER', 'user');
define('ROLE_PROVIDER', 'provider');
define('ROLE_ADMIN', 'admin');
define('ROLE_SUPER_ADMIN', 'super_admin');
define('ROLE_MODERATOR', 'moderator');

// Booking Status
define('BOOKING_PENDING', 'pending');
define('BOOKING_CONFIRMED', 'confirmed');
define('BOOKING_COMPLETED', 'completed');
define('BOOKING_CANCELLED', 'cancelled');
define('BOOKING_DECLINED', 'declined');

// Listing Status
define('LISTING_ACTIVE', 'active');
define('LISTING_INACTIVE', 'inactive');
define('LISTING_PENDING', 'pending');
define('LISTING_REJECTED', 'rejected');

// Review Status
define('REVIEW_PENDING', 'pending');
define('REVIEW_APPROVED', 'approved');
define('REVIEW_REJECTED', 'rejected');

// Create upload directories if they don't exist
$upload_dirs = [
    UPLOAD_PATH,
    USER_UPLOAD_DIR,
    PROVIDER_UPLOAD_DIR,
    LISTING_UPLOAD_DIR
];

foreach ($upload_dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Helper Functions
function base_url($path = '') {
    return BASE_URL . ltrim($path, '/');
}

function assets_url($path = '') {
    return ASSETS_URL . ltrim($path, '/');
}

function uploads_url($path = '') {
    return UPLOADS_URL . ltrim($path, '/');
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function is_logged_in($role = null) {
    if ($role === null) {
        return isset($_SESSION['user_id']) || isset($_SESSION['provider_id']) || isset($_SESSION['admin_id']);
    }
    
    switch ($role) {
        case ROLE_USER:
            return isset($_SESSION['user_id']);
        case ROLE_PROVIDER:
            return isset($_SESSION['provider_id']);
        case ROLE_ADMIN:
        case ROLE_SUPER_ADMIN:
        case ROLE_MODERATOR:
            return isset($_SESSION['admin_id']);
        default:
            return false;
    }
}

function get_user_id($role = ROLE_USER) {
    switch ($role) {
        case ROLE_USER:
            return $_SESSION['user_id'] ?? null;
        case ROLE_PROVIDER:
            return $_SESSION['provider_id'] ?? null;
        case ROLE_ADMIN:
        case ROLE_SUPER_ADMIN:
        case ROLE_MODERATOR:
            return $_SESSION['admin_id'] ?? null;
        default:
            return null;
    }
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function format_price($amount) {
    return CURRENCY_SYMBOL . ' ' . number_format($amount, 2);
}

function format_date($date, $format = 'd M Y') {
    return date($format, strtotime($date));
}

function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    if ($difference < 60) {
        return 'Just now';
    } elseif ($difference < 3600) {
        $minutes = floor($difference / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($difference < 86400) {
        $hours = floor($difference / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($difference < 604800) {
        $days = floor($difference / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('d M Y', $timestamp);
    }
}

function generate_booking_reference() {
    return 'TE' . date('Ymd') . strtoupper(substr(uniqid(), -6));
}

function generate_reset_token() {
    return bin2hex(random_bytes(32));
}

function flash_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

function upload_image($file, $directory, $prefix = '') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'No file uploaded or upload error'];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds maximum allowed size'];
    }
    
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and WEBP allowed'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . uniqid() . '_' . time() . '.' . $extension;
    $filepath = $directory . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
}

function delete_image($filepath) {
    if (file_exists($filepath) && !in_array(basename($filepath), ['default-avatar.png', 'default-provider.png', 'default-listing.jpg'])) {
        return unlink($filepath);
    }
    return false;
}
