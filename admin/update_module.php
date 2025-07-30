<?php
require_once '../config/config.php';

// Require staff login
Security::requireRole('Staff');

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
    showAlert('Invalid request. Please try again.', 'danger');
    redirectTo('staff_dashboard.php');
}

// Get staff data
$staffData = $staff->getStaffByUserId($_SESSION['user_id']);
if (!$staffData) {
    showAlert('Staff profile not found.', 'danger');
    redirectTo('staff_dashboard.php');
}

// Get module data
$moduleId = (int)($_POST['module_id'] ?? 0);
$moduleData = $module->getModuleById($moduleId);

// Verify module exists and staff is the leader
if (!$moduleData || $moduleData['ModuleLeaderID'] != $staffData['StaffID']) {
    showAlert('You do not have permission to edit this module.', 'danger');
    redirectTo('staff_dashboard.php');
}

// Prepare update data
$updateData = [
    'description' => $_POST['description'] ?? '',
    'learning_outcomes' => $_POST['learning_outcomes'] ?? '',
    'assessment_methods' => $_POST['assessment_methods'] ?? ''
];

// Update module
if ($module->updateModuleContent($moduleId, $updateData)) {
    // Log activity
    $security->logActivity(
        $_SESSION['user_id'],
        'Update Module',
        'Modules',
        $moduleId,
        [
            'description' => $moduleData['Description'],
            'learning_outcomes' => $moduleData['LearningOutcomes'],
            'assessment_methods' => $moduleData['AssessmentMethods']
        ],
        $updateData
    );
    showAlert('Module updated successfully.', 'success');
} else {
    showAlert('Failed to update module.', 'danger');
}

redirectTo('staff_dashboard.php'); 