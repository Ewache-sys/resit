<?php
require_once '../config/config.php';

// Require staff login
Security::requireRole('Staff');

// Get staff data
$staffData = $staff->getStaffByUserId($_SESSION['user_id']);
if (!$staffData) {
    http_response_code(403);
    echo json_encode(['error' => 'Staff profile not found']);
    exit;
}

// Get module ID from request
$moduleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($moduleId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid module ID']);
    exit;
}

// Get module data
$moduleData = $module->getModuleById($moduleId);

// Verify module exists and staff is the leader
if (!$moduleData || $moduleData['ModuleLeaderID'] != $staffData['StaffID']) {
    http_response_code(403);
    echo json_encode(['error' => 'You do not have permission to view this module']);
    exit;
}

// Return module data as JSON
header('Content-Type: application/json');
echo json_encode($moduleData); 