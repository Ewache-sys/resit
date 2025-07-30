<?php
require_once __DIR__ . '/../config/config.php';

// Require admin privileges
Security::requireRole('Admin');

// Initialize response
$response = ['success' => false, 'data' => null, 'error' => ''];

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Student ID is required.');
    }

    $studentId = (int)$_GET['id'];
    $student = new Student($db);
    $studentData = $student->getStudentById($studentId);

    if (!$studentData) {
        throw new Exception('Student not found.');
    }

    $response['success'] = true;
    $response['data'] = $studentData;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?> 