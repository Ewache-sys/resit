<?php
require_once '../config/config.php';

// Require admin login
Security::requireRole('Admin');

// Get module ID from request
$moduleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($moduleId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid module ID']);
    exit;
}

// Initialize Module class
$module = new Module($db);

// Get module data
$moduleData = $module->getModuleById($moduleId);

if (!$moduleData) {
    http_response_code(404);
    echo json_encode(['error' => 'Module not found']);
    exit;
}

// Return module data as JSON
header('Content-Type: application/json');
echo json_encode($moduleData); 