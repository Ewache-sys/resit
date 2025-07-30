<?php
require_once '../config/config.php';

// Require admin login
Security::requireRole('Admin');

// Get staff ID from URL if editing
$staffId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEditing = $staffId > 0;

// Get staff data if editing
$staffData = null;
if ($isEditing) {
    $staffData = $staff->getStaffById($staffId);
    if (!$staffData) {
        showAlert('Staff member not found.', 'danger');
        redirectTo('staff.php');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
        showAlert('Invalid request. Please try again.', 'danger');
        redirectTo('staff.php');
    }

    // Prepare staff data
    $data = [
        'username' => $_POST['username'] ?? '',
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'department' => $_POST['department'] ?? '',
        'title' => $_POST['title'] ?? '',
        'bio' => $_POST['bio'] ?? ''
    ];

    // Handle password
    if (!$isEditing || !empty($_POST['password'])) {
        $data['password'] = $_POST['password'];
    }

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
            redirectTo($isEditing ? "staff_form.php?id=$staffId" : 'staff_form.php');
        }

        // Generate unique filename
        $filename = uniqid('staff_') . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
            $data['profile_image'] = 'uploads/staff/' . $filename;

            // Delete old image if exists
            if ($isEditing && $staffData['ProfileImage']) {
                $oldImage = '../' . $staffData['ProfileImage'];
                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }
        }
    }

    // Create or update staff
    if ($isEditing) {
        $result = $staff->updateStaff($staffId, $data);
        $message = 'Staff member updated successfully.';
        $action = 'Update Staff';
    } else {
        $result = $staff->createStaff($data);
        $message = 'Staff member created successfully.';
        $action = 'Create Staff';
    }

    if ($result) {
        // Log activity
        $security->logActivity(
            $_SESSION['user_id'],
            $action,
            'Staff',
            $isEditing ? $staffId : $result
        );
        showAlert($message, 'success');
        redirectTo('staff.php');
    } else {
        showAlert('Failed to save staff member.', 'danger');
    }
}

$pageTitle = ($isEditing ? 'Edit' : 'Add') . ' Staff Member - Admin - ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="admin-body">
    <?php include 'includes/admin_nav.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/admin_sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1><?php echo $isEditing ? 'Edit' : 'Add'; ?> Staff Member</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="staff.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Staff
                        </a>
                    </div>
                </div>

                <?php displayAlert(); ?>

                <div class="card">
                    <div class="card-body">
                        <form action="<?php echo $_SERVER['PHP_SELF'] . ($isEditing ? "?id=$staffId" : ''); ?>"
                              method="POST"
                              enctype="multipart/form-data"
                              class="needs-validation"
                              novalidate>
                            
                            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control"
                                           name="username"
                                           value="<?php echo htmlspecialchars($staffData['Username'] ?? ''); ?>"
                                           required>
                                    <div class="form-text">This will be used for login.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Password 
                                        <?php if (!$isEditing): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif; ?>
                                    </label>
                                    <input type="password"
                                           class="form-control"
                                           name="password"
                                           <?php echo $isEditing ? '' : 'required'; ?>>
                                    <?php if ($isEditing): ?>
                                        <div class="form-text">Leave empty to keep current password.</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control"
                                           name="name"
                                           value="<?php echo htmlspecialchars($staffData['Name'] ?? ''); ?>"
                                           required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email"
                                           class="form-control"
                                           name="email"
                                           value="<?php echo htmlspecialchars($staffData['Email'] ?? ''); ?>"
                                           required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="tel"
                                           class="form-control"
                                           name="phone"
                                           value="<?php echo htmlspecialchars($staffData['Phone'] ?? ''); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Department</label>
                                    <input type="text"
                                           class="form-control"
                                           name="department"
                                           value="<?php echo htmlspecialchars($staffData['Department'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text"
                                       class="form-control"
                                       name="title"
                                       value="<?php echo htmlspecialchars($staffData['Title'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bio</label>
                                <textarea class="form-control"
                                         name="bio"
                                         rows="4"><?php echo htmlspecialchars($staffData['Bio'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Profile Image</label>
                                <input type="file"
                                       class="form-control"
                                       name="profile_image"
                                       accept="image/*">
                                <?php if ($isEditing && $staffData['ProfileImage']): ?>
                                    <div class="mt-2">
                                        <img src="../<?php echo htmlspecialchars($staffData['ProfileImage']); ?>"
                                             alt="Current profile image"
                                             class="img-thumbnail"
                                             style="max-height: 100px;">
                                        <div class="form-text">Leave empty to keep current image.</div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="staff.php" class="btn btn-light me-md-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Staff Member
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Form validation -->
    <script>
        // Enable Bootstrap form validation
        (function() {
            'use strict';

            var forms = document.querySelectorAll('.needs-validation');

            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html> 