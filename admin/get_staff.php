<?php
require_once __DIR__ . '/../config/config.php';

// Require admin privileges
Security::requireRole('Admin');

// Initialize response
$response = ['success' => false, 'data' => null, 'error' => ''];

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Staff ID is required.');
    }

    $staffId = (int)$_GET['id'];
    $staff = new Staff($db);
    $staffData = $staff->getStaffById($staffId);

    if (!$staffData) {
        throw new Exception('Staff member not found.');
    }

    $response['success'] = true;
    $response['data'] = $staffData;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?> 