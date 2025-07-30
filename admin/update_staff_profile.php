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

// Prepare update data
$updateData = [
    'phone' => $_POST['phone'] ?? '',
    'bio' => $_POST['bio'] ?? '',
    'profile_image' => null
];

// Handle image upload
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/staff/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileInfo = pathinfo($_FILES['profile_image']['name']);
    $extension = strtolower($fileInfo['extension']);
    
    // Validate file type
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($extension, $allowedTypes)) {
        showAlert('Invalid file type. Please upload an image.', 'danger');
        redirectTo('staff_dashboard.php');
    }

    // Generate unique filename
    $filename = uniqid('staff_') . '.' . $extension;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
        $updateData['profile_image'] = 'uploads/staff/' . $filename;

        // Delete old image if exists
        if ($staffData['ProfileImage']) {
            $oldImage = '../' . $staffData['ProfileImage'];
            if (file_exists($oldImage)) {
                unlink($oldImage);
            }
        }
    }
}

// Update staff profile
if ($staff->updateStaffProfile($staffData['StaffID'], $updateData)) {
    // Log activity
    $security->logActivity(
        $_SESSION['user_id'],
        'Update Profile',
        'Staff',
        $staffData['StaffID']
    );
    showAlert('Profile updated successfully.', 'success');
} else {
    showAlert('Failed to update profile.', 'danger');
}

redirectTo('staff_dashboard.php'); 