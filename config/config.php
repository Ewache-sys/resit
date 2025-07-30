<?php
/**
 * Main Configuration File
 * Student Course Hub System
 */

// Start session with secure settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
session_start();

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define constants
define('BASE_PATH', dirname(__DIR__));
define('SITE_URL', 'http://localhost');
define('SITE_NAME', 'Course Hub');
define('SITE_DESCRIPTION', 'Discover your future with our comprehensive range of undergraduate and postgraduate programmes');

// Include required files
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/includes/security.php';
require_once BASE_PATH . '/classes/Programme.php';
require_once BASE_PATH . '/classes/StudentInterest.php';
require_once BASE_PATH . '/classes/Level.php';
require_once BASE_PATH . '/classes/Staff.php';
require_once BASE_PATH . '/classes/Module.php';
require_once BASE_PATH . '/classes/Student.php';  // Add Student class
require_once BASE_PATH . '/classes/User.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize classes
$security = new Security($db);
$programme = new Programme($db);
$studentInterest = new StudentInterest($db);
$level = new Level($db);
$staff = new Staff($db);

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// CSRF token for forms
$csrf_token = Security::generateCSRFToken();

/**
 * Helper functions
 */

function sanitize($input) {
    return Security::sanitizeInput($input);
}

function formatDate($date) {
    return date('j M Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('j M Y, g:i A', strtotime($datetime));
}

function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function isCurrentPage($page) {
    $currentFile = basename($_SERVER['PHP_SELF']);
    return $currentFile === $page;
}

function redirectTo($url) {
    header("Location: $url");
    exit();
}

function showAlert($message, $type = 'info') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

function displayAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        echo "<div class='alert alert-{$alert['type']} alert-dismissible fade show' role='alert'>
                {$alert['message']}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
        unset($_SESSION['alert']);
    }
}


function sendEmail($to, $subject, $body, $isHTML = true) {
    // Implementation would go here using PHPMailer or similar
    // For now, just log the email
    error_log("Email to: $to, Subject: $subject");
    return true;
}

function sendWelcomeEmail($studentData, $programmeData) {
    $subject = "Thank you for your interest in " . $programmeData['ProgrammeName'];
    $body = "
        <h2>Thank you for your interest!</h2>
        <p>Dear {$studentData['StudentName']},</p>
        <p>Thank you for expressing interest in our {$programmeData['ProgrammeName']} programme.</p>
        <p>We will contact you with updates about open days, application deadlines, and programme information.</p>
        <p>Best regards,<br>The Admissions Team</p>
    ";
    return sendEmail($studentData['Email'], $subject, $body);
}

/**
 * Image handling functions
 */
function uploadImage($file, $directory = 'uploads') {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024;

    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.'];
    }

    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File too large. Maximum size is 5MB.'];
    }

    $uploadDir = BASE_PATH . "/$directory/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'path' => "$directory/$filename"];
    }

    return ['success' => false, 'message' => 'Failed to upload file.'];
}

/**
 * Pagination helper
 */
function paginate($currentPage, $totalItems, $itemsPerPage = 10) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $offset = ($currentPage - 1) * $itemsPerPage;

    return [
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'total_items' => $totalItems,
        'items_per_page' => $itemsPerPage,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}
?>
