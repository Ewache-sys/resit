<?php
require_once '../config/config.php';

// Log the logout activity if user is logged in
if (isset($_SESSION['user_id'])) {
    $security->logActivity(
        $_SESSION['user_id'],
        'Staff Logout',
        'Staff',
        $_SESSION['user_id']
    );
}

// Clear all session data
session_destroy();

// Redirect to login page
redirectTo('login.php');
?> 