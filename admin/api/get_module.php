<?php
require_once '../../config/config.php';

// Require admin privileges
Security::requireRole('Admin');

// Initialize response
$response = ['success' => false, 'data' => null, 'error' => ''];

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Module ID is required.');
    }

    $moduleId = (int)$_GET['id'];
    $module = new Module($db);
    $moduleData = $module->getModuleById($moduleId);

    if (!$moduleData) {
        throw new Exception('Module not found.');
    }

    $response['success'] = true;
    $response['data'] = $moduleData;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?> 