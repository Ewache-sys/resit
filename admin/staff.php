<?php
require_once __DIR__ . '/../config/config.php';

// Require admin privileges
Security::requireRole('Admin');

// Initialize Staff class
$staff = new Staff($db);

// Handle form submissions
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create':
            case 'update':
                $staffData = [
                    'name' => sanitize($_POST['name']),
                    'email' => sanitize($_POST['email']),
                    'username' => sanitize($_POST['username']),
                    'phone' => sanitize($_POST['phone']),
                    'department' => sanitize($_POST['department']),
                    'title' => sanitize($_POST['title']),
                    'bio' => sanitize($_POST['bio']),
                    'profile_image' => null
                ];

                // Add password for new staff or if provided for existing staff
                if ($action === 'create' || !empty($_POST['password'])) {
                    $staffData['password'] = $_POST['password'];
                }

                // Handle profile image upload
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = uploadImage($_FILES['profile_image'], 'uploads/staff');
                    if ($uploadResult['success']) {
                        $staffData['profile_image'] = $uploadResult['path'];
                    } else {
                        $error = $uploadResult['message'];
                        break;
                    }
                }

                if ($action === 'create') {
                    if ($staff->createStaff($staffData)) {
                        $success = 'Staff member added successfully.';
                    } else {
                        $error = 'Failed to add staff member.';
                    }
                } else {
                    $staffId = (int)$_POST['staff_id'];
                    if ($staff->updateStaff($staffId, $staffData)) {
                        $success = 'Staff member updated successfully.';
                    } else {
                        $error = 'Failed to update staff member.';
                    }
                }
                break;

            case 'delete':
                $staffId = (int)$_POST['staff_id'];
                $result = $staff->deleteStaff($staffId);
                if ($result['success']) {
                    $success = $result['message'];
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Get all staff members
$allStaff = $staff->getAllStaff(true);

$pageTitle = 'Manage Staff - ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
    
    <style>
        .staff-card {
            transition: transform 0.2s;
        }
        .staff-card:hover {
            transform: translateY(-5px);
        }
        .profile-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }
        .preview-image {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body class="admin-body">
    <?php include 'includes/admin_nav.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/admin_sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="text-black">Manage Staff</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staffModal">
                        <i class="fas fa-plus me-2"></i>Add New Staff
                    </button>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Staff Grid -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                    <?php foreach ($allStaff as $s): ?>
                        <div class="col">
                            <div class="card h-100 staff-card <?php echo $s['IsActive'] ? '' : 'bg-light'; ?>">
                                <div class="card-body text-center">
                                    <img src="<?php echo $s['ProfileImage'] ? '../' . $s['ProfileImage'] : '../assets/images/default-profile.png'; ?>"
                                         alt="<?php echo htmlspecialchars($s['Name']); ?>"
                                         class="profile-image mb-3">
                                    
                                    <h5 class="card-title">
                                        <?php echo htmlspecialchars($s['Name']); ?>
                                        <?php if (!$s['IsActive']): ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </h5>
                                    
                                    <p class="card-text text-muted mb-1">
                                        <?php echo htmlspecialchars($s['Title'] ?? 'No title specified'); ?>
                                    </p>
                                    
                                    <p class="card-text small mb-1">
                                        <i class="fas fa-building me-2"></i>
                                        <?php echo htmlspecialchars($s['Department'] ?? 'No department specified'); ?>
                                    </p>
                                    
                                    <p class="card-text small mb-3">
                                        <i class="fas fa-envelope me-2"></i>
                                        <?php echo htmlspecialchars($s['Email'] ?? 'No email specified'); ?>
                                    </p>

                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary view-staff"
                                                data-staff-id="<?php echo $s['StaffID']; ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#staffModal">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-staff"
                                                data-staff-id="<?php echo $s['StaffID']; ?>"
                                                data-staff-name="<?php echo htmlspecialchars($s['Name']); ?>">
                                            <i class="fas fa-trash me-2"></i>Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Staff Modal -->
                <div class="modal fade" id="staffModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add/Edit Staff</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="staffForm" method="POST" enctype="multipart/form-data" class="text-dark">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="create">
                                    <input type="hidden" name="staff_id" value="">

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control" name="name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Username</label>
                                            <input type="text" class="form-control" name="username" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="password" id="password">
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <div class="form-text password-help">Leave blank to keep existing password when editing.</div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <input type="tel" class="form-control" name="phone">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Department</label>
                                            <input type="text" class="form-control" name="department" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Bio</label>
                                        <textarea class="form-control" name="bio" rows="4"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Profile Image</label>
                                        <input type="file" class="form-control" name="profile_image" accept="image/*">
                                        <div id="imagePreview" class="mt-2 text-center"></div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" form="staffForm" class="btn btn-primary">Save Staff</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-black">
                                Are you sure you want to delete this staff member?
                            </div>
                            <div class="modal-footer">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="staff_id" value="">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Delete Staff</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview
            const imageInput = document.querySelector('input[name="profile_image"]');
            const imagePreview = document.getElementById('imagePreview');
            
            imageInput.addEventListener('change', function() {
                imagePreview.innerHTML = '';
                if (this.files && this.files[0]) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(this.files[0]);
                    img.className = 'preview-image';
                    imagePreview.appendChild(img);
                }
            });

            // Handle edit staff
            const staffModal = document.getElementById('staffModal');
            staffModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const isEdit = button.classList.contains('view-staff');
                const form = this.querySelector('#staffForm');
                const title = this.querySelector('.modal-title');
                const passwordInput = form.querySelector('input[name="password"]');
                const passwordHelp = form.querySelector('.password-help');
                
                // Reset form first
                form.reset();
                imagePreview.innerHTML = '';
                
                if (isEdit) {
                    const staffId = button.dataset.staffId;
                    title.textContent = 'Edit Staff';
                    form.querySelector('input[name="action"]').value = 'update';
                    form.querySelector('input[name="staff_id"]').value = staffId;
                    passwordInput.required = false;
                    passwordHelp.style.display = 'block';
                    
                    // Fetch staff data and populate form
                    fetch(`get_staff.php?id=${staffId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const staff = data.data;
                                form.querySelector('input[name="name"]').value = staff.Name || '';
                                form.querySelector('input[name="email"]').value = staff.Email || '';
                                form.querySelector('input[name="username"]').value = staff.Username || '';
                                form.querySelector('input[name="phone"]').value = staff.Phone || '';
                                form.querySelector('input[name="department"]').value = staff.Department || '';
                                form.querySelector('input[name="title"]').value = staff.Title || '';
                                form.querySelector('textarea[name="bio"]').value = staff.Bio || '';
                                
                                if (staff.ProfileImage) {
                                    imagePreview.innerHTML = `<img src="../${staff.ProfileImage}" class="preview-image">`;
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching staff data:', error);
                            alert('Failed to load staff data. Please try again.');
                        });
                } else {
                    title.textContent = 'Add New Staff';
                    form.querySelector('input[name="action"]').value = 'create';
                    form.querySelector('input[name="staff_id"]').value = '';
                    passwordInput.required = true;
                    passwordHelp.style.display = 'none';
                    
                    // Clear all inputs
                    form.querySelectorAll('input:not([type="hidden"])').forEach(input => {
                        input.value = '';
                    });
                    form.querySelector('textarea[name="bio"]').value = '';
                }
            });

            // Handle password visibility toggle
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');
            
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });

            // Handle delete staff
            document.querySelectorAll('.delete-staff').forEach(button => {
                button.addEventListener('click', function() {
                    const staffId = this.dataset.staffId;
                    const staffName = this.dataset.staffName;
                    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    const modalBody = document.querySelector('#deleteModal .modal-body');
                    const staffIdInput = document.querySelector('#deleteModal input[name="staff_id"]');
                    
                    modalBody.textContent = `Are you sure you want to delete ${staffName}?`;
                    staffIdInput.value = staffId;
                    modal.show();
                });
            });
        });
    </script>
</body>
</html> 