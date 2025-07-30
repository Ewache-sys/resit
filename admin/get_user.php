<?php
require_once __DIR__ . '/../config/config.php';

// Require admin privileges
Security::requireRole('Admin');

// Initialize response
$response = ['success' => false, 'data' => null, 'error' => ''];

try {
    if (!isset($_GET['id'])) {
        throw new Exception('User ID is required.');
    }

    $userId = (int)$_GET['id'];
    $user = new User($db);
    $userData = $user->getUserById($userId);

    if (!$userData) {
        throw new Exception('User not found.');
    }

    $response['success'] = true;
    $response['data'] = $userData;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?> 