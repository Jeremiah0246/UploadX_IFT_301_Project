<?php
/**
 * UploadX Configuration
 * Database and Application Settings
 */

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Your MySQL password here (usually empty for XAMPP)
define('DB_NAME', 'uploadx');

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 52428800); // 50MB in bytes
define('ALLOWED_PHOTO_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
define('ALLOWED_DOCUMENT_TYPES', ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'ppt', 'pptx', 'csv']);

// Email Configuration (SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com'); // Change to your email
define('SMTP_PASS', 'your-app-password'); // Change to your Gmail app password
define('SMTP_FROM', 'noreply@uploadx.com');
define('SMTP_FROM_NAME', 'UploadX');

// Application Settings
define('APP_NAME', 'UploadX');
define('APP_URL', 'http://localhost/UploadX'); // Change to your domain

// Session Settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get database connection
 */
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            error_log("Database connection failed: " . $conn->connect_error);
            die("We're experiencing technical difficulties. Please try again later.");
        }
        
        $conn->set_charset('utf8mb4');
    }
    
    return $conn;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 */
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current username
 */
function getUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * Get current user email
 */
function getUserEmail() {
    return $_SESSION['email'] ?? null;
}

/**
 * Require login - redirect if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Redirect if already logged in
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: dashboard.php');
        exit();
    }
}

/**
 * Sanitize input
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format file size
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Get file icon based on extension
 */
function getFileIcon($extension) {
    $extension = strtolower($extension);
    
    $icons = [
        // Images
        'jpg' => '🖼️', 'jpeg' => '🖼️', 'png' => '🖼️', 'gif' => '🖼️', 'webp' => '🖼️', 'svg' => '🖼️',
        // Documents
        'pdf' => '📄', 'doc' => '📝', 'docx' => '📝', 'txt' => '📃',
        'xls' => '📊', 'xlsx' => '📊', 'ppt' => '📊', 'pptx' => '📊',
        'csv' => '📋'
    ];
    
    return $icons[$extension] ?? '📁';
}

/**
 * Check if file is an image
 */
function isImage($extension) {
    return in_array(strtolower($extension), ALLOWED_PHOTO_TYPES);
}

/**
 * Check if file is a document
 */
function isDocument($extension) {
    return in_array(strtolower($extension), ALLOWED_DOCUMENT_TYPES);
}

/**
 * Get allowed file types
 */
function getAllowedTypes() {
    return array_merge(ALLOWED_PHOTO_TYPES, ALLOWED_DOCUMENT_TYPES);
}
?>