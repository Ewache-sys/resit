<?php
require_once 'config/config.php';

// Ensure this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('index.php');
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
    showAlert('Invalid request. Please try again.', 'danger');
    redirectTo('index.php');
}

// Sanitize and validate input
$programmeId = isset($_POST['programme_id']) ? (int)$_POST['programme_id'] : 0;
$studentName = sanitize($_POST['student_name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$country = sanitize($_POST['country'] ?? '');
$currentEducation = sanitize($_POST['current_education'] ?? '');
$message = sanitize($_POST['message'] ?? '');
$consent = isset($_POST['consent']);

// Validation
$errors = [];

if (empty($studentName)) {
    $errors[] = 'Student name is required.';
}

if (empty($email) || !Security::validateEmail($email)) {
    $errors[] = 'Valid email address is required.';
}

if ($programmeId <= 0) {
    $errors[] = 'Invalid programme selected.';
}

if (!$consent) {
    $errors[] = 'You must consent to receive communications.';
}

// Check if programme exists and is published
$programmeData = $programme->getProgrammeById($programmeId, true);
if (!$programmeData) {
    $errors[] = 'Selected programme is not available.';
}

// If there are validation errors, redirect back with errors
if (!empty($errors)) {
    showAlert(implode('<br>', $errors), 'danger');
    redirectTo('index.php');
}

// Prepare data for registration
$registrationData = [
    'programme_id' => $programmeId,
    'student_name' => $studentName,
    'email' => $email,
    'phone' => $phone,
    'country' => $country,
    'current_education' => $currentEducation,
    'message' => $message
];

// Register interest
$result = $studentInterest->registerInterest($registrationData);

if ($result['success']) {
    // Send welcome email
    try {
        sendWelcomeEmail($registrationData, $programmeData);
    } catch (Exception $e) {
        error_log("Welcome email error: " . $e->getMessage());
        // Don't fail the registration if email fails
    }

    // Success - redirect to thank you page or back to index with success message
    showAlert($result['message'], 'success');

    // Redirect to programme page if available, otherwise to homepage
    if (isset($_POST['redirect_to']) && $_POST['redirect_to'] === 'programme') {
        redirectTo('programme.php?id=' . $programmeId . '&registered=1');
    } else {
        redirectTo('index.php?registered=1');
    }
} else {
    // Error - redirect back with error message
    showAlert($result['message'], 'danger');

    if (isset($_POST['redirect_to']) && $_POST['redirect_to'] === 'programme') {
        redirectTo('programme.php?id=' . $programmeId);
    } else {
        redirectTo('index.php');
    }
}
?>
