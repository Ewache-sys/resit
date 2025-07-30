<?php
require_once '../config/config.php';

// Require staff login
Security::requireRole('Staff');

// Get staff data based on user ID
$staffData = $staff->getStaffByUserId($_SESSION['user_id']);
if (!$staffData) {
    showAlert('Staff profile not found.', 'danger');
    Security::logout();
    redirectTo('login.php');
}

// Get staff modules and programmes
$staffModules = $staff->getStaffModules($staffData['StaffID']);
$staffProgrammes = $staff->getStaffProgrammes($staffData['StaffID']);

$pageTitle = 'Staff Dashboard - ' . SITE_NAME;
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
                <!-- Staff Profile Section -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h1 class="h2 mb-4">Welcome, <?php echo htmlspecialchars($staffData['Name']); ?></h1>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 text-center mb-3 mb-md-0">
                                        <?php if ($staffData['ProfileImage']): ?>
                                            <img src="../<?php echo htmlspecialchars($staffData['ProfileImage']); ?>"
                                                 alt="Profile Image"
                                                 class="img-fluid rounded-circle mb-2"
                                                 style="width: 150px; height: 150px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                                 style="width: 150px; height: 150px;">
                                                <i class="fas fa-user fa-4x text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editProfileModal">
                                            <i class="fas fa-edit me-1"></i>Edit Profile
                                        </button>
                                    </div>
                                    <div class="col-md-9">
                                        <h3 class="h5"><?php echo htmlspecialchars($staffData['Title']); ?></h3>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($staffData['Email']); ?>
                                        </p>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-building me-2"></i><?php echo htmlspecialchars($staffData['Department']); ?>
                                        </p>
                                        <?php if ($staffData['Phone']): ?>
                                            <p class="text-muted mb-2">
                                                <i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($staffData['Phone']); ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ($staffData['Bio']): ?>
                                            <hr>
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($staffData['Bio'])); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Quick Stats</h5>
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <div class="h3 mb-0"><?php echo count($staffModules); ?></div>
                                        <div class="small text-muted">Modules</div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="h3 mb-0"><?php echo count($staffProgrammes); ?></div>
                                        <div class="small text-muted">Programmes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Programmes Section -->
                <section class="mb-4">
                    <h2 class="h4 mb-3">Programme Leadership</h2>
                    <?php if (empty($staffProgrammes)): ?>
                        <div class="card">
                            <div class="card-body text-center text-muted py-5">
                                <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                                <p class="mb-0">You are not currently leading any programmes.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($staffProgrammes as $prog): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <?php echo htmlspecialchars($prog['ProgrammeName']); ?>
                                                <span class="badge bg-primary ms-2">
                                                    <?php echo htmlspecialchars($prog['ProgrammeCode']); ?>
                                                </span>
                                            </h5>
                                            <p class="text-muted small mb-2">
                                                <?php echo htmlspecialchars($prog['LevelName']); ?>
                                            </p>
                                            <p class="card-text">
                                                <?php echo htmlspecialchars(truncateText($prog['Description'], 150)); ?>
                                            </p>
                                            <div class="mt-3">
                                                <a href="../programme.php?id=<?php echo $prog['ProgrammeID']; ?>"
                                                   class="btn btn-sm btn-outline-primary"
                                                   target="_blank">
                                                    <i class="fas fa-external-link-alt me-1"></i>View Programme
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Modules Section -->
                <section>
                    <h2 class="h4 mb-3">Module Leadership</h2>
                    <?php if (empty($staffModules)): ?>
                        <div class="card">
                            <div class="card-body text-center text-muted py-5">
                                <i class="fas fa-book fa-3x mb-3"></i>
                                <p class="mb-0">You are not currently leading any modules.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($staffModules as $mod): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <?php echo htmlspecialchars($mod['ModuleCode']); ?>:
                                                <?php echo htmlspecialchars($mod['ModuleName']); ?>
                                            </h5>
                                            <p class="text-muted mb-2">
                                                <span class="badge bg-primary"><?php echo $mod['Credits']; ?> Credits</span>
                                            </p>
                                            <p class="card-text">
                                                <?php echo htmlspecialchars(truncateText($mod['Description'], 100)); ?>
                                            </p>
                                            <div class="mt-3">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModuleModal"
                                                        data-module-id="<?php echo $mod['ModuleID']; ?>">
                                                    <i class="fas fa-edit me-1"></i>Edit Module
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </main>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="update_staff_profile.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Profile Image</label>
                            <input type="file" class="form-control" name="profile_image" accept="image/*">
                            <?php if ($staffData['ProfileImage']): ?>
                                <div class="form-text">Leave empty to keep current image</div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone"
                                   value="<?php echo htmlspecialchars($staffData['Phone'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea class="form-control" name="bio" rows="4"><?php echo htmlspecialchars($staffData['Bio'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Module Modal -->
    <div class="modal fade" id="editModuleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Module</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editModuleForm" action="update_module.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                        <input type="hidden" name="module_id" id="editModuleId">

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Learning Outcomes</label>
                            <textarea class="form-control" name="learning_outcomes" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assessment Methods</label>
                            <textarea class="form-control" name="assessment_methods" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle edit module modal
            const editModuleModal = document.getElementById('editModuleModal');
            if (editModuleModal) {
                editModuleModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const moduleId = button.dataset.moduleId;
                    const form = this.querySelector('#editModuleForm');
                    
                    // Set module ID
                    form.querySelector('#editModuleId').value = moduleId;
                    
                    // Fetch module data
                    fetch(`get_module.php?id=${moduleId}`)
                        .then(response => response.json())
                        .then(data => {
                            form.querySelector('[name="description"]').value = data.Description || '';
                            form.querySelector('[name="learning_outcomes"]').value = data.LearningOutcomes || '';
                            form.querySelector('[name="assessment_methods"]').value = data.AssessmentMethods || '';
                        });
                });
            }
        });
    </script>
</body>
</html> 