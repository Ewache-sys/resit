<?php
require_once '../config/config.php';

// Log the logout activity if user is logged in
if (Security::isLoggedIn()) {
    $userId = $_SESSION['user_id'] ?? null;
    if ($userId) {
        $security->logActivity($userId, 'Logout', 'Users', $userId);
    }
}

// Perform logout
Security::logout();

// Set success message
showAlert('You have been successfully logged out.', 'success');

// Redirect to login page
redirectTo('login.php');
?>
