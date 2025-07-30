<?php
require_once '../config/config.php';
Security::requireRole("Staff");
// Require staff role
// if (!Security::isLoggedIn() || !Security::hasRole('Staff')) {
//     redirectTo('login.php');
// }

// Get staff data
$staffId = $_SESSION['user_id'];
$staffData = $staff->getStaffById($staffId);
$staffStats = $staff->getStaffStats($staffId);

$pageTitle = 'Staff Dashboard - ' . SITE_NAME;
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
        .stats-card {
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body class="admin-body">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-user-tie me-2"></i>Staff Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../">
                            <i class="fas fa-home me-1"></i>Website
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                                    <i class="fas fa-user-edit me-2"></i>Edit Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-5 pt-4">
        <div class="row">
            <!-- Profile Section -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="<?php echo $staffData['ProfileImage'] ? '../' . $staffData['ProfileImage'] : '../assets/images/default-profile.png'; ?>"
                             alt="<?php echo htmlspecialchars($staffData['Name']); ?>"
                             class="profile-image mb-3">
                        
                        <h4><?php echo htmlspecialchars($staffData['Name']); ?></h4>
                        <p class="text-muted mb-1"><?php echo htmlspecialchars($staffData['Title']); ?></p>
                        <p class="text-muted mb-3"><?php echo htmlspecialchars($staffData['Department']); ?></p>
                        
                        <div class="d-grid">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#profileModal">
                                <i class="fas fa-edit me-2"></i>Edit Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="col-md-8 mb-4">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card stats-card bg-primary text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-book-open fa-3x mb-3"></i>
                                <h5>Programmes</h5>
                                <h3><?php echo $staffStats['programme_count']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card bg-success text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-tasks fa-3x mb-3"></i>
                                <h5>Modules</h5>
                                <h3><?php echo $staffStats['module_count']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card bg-info text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <h5>Interested Students</h5>
                                <h3><?php echo $staffStats['interested_students']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modules Section -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">My Modules</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($staffStats['modules'])): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead >
                                        <tr >
                                            <th class="text-white">Code</th>
                                            <th class="text-white">Name</th>
                                            <th class="text-white">Credits</th>
                                            <th class="text-white">Programmes</th>
                                            <th class="text-white">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($staffStats['modules'] as $module): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($module['ModuleCode']); ?></td>
                                                <td><?php echo htmlspecialchars($module['ModuleName']); ?></td>
                                                <td><?php echo $module['Credits']; ?></td>
                                                <td><?php echo $module['programme_count']; ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary edit-module"
                                                            data-module-id="<?php echo $module['ModuleID']; ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#moduleModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center mb-0">You are not currently leading any modules.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Programmes Section -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">My Programmes</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($staffStats['programmes'])): ?>
                            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                                <?php foreach ($staffStats['programmes'] as $prog): ?>
                                    <div class="col">
                                        <div class="card h-100">
                                            <img src="<?php echo $prog['Image'] ? '../' . $prog['Image'] : '../assets/images/default-programme.jpg'; ?>"
                                                 class="card-img-top"
                                                 alt="<?php echo htmlspecialchars($prog['ProgrammeName']); ?>"
                                                 style="height: 200px; object-fit: cover;">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($prog['ProgrammeName']); ?></h5>
                                                <p class="card-text text-muted">
                                                    <?php echo htmlspecialchars($prog['LevelName']); ?>
                                                </p>
                                                <a href="../programme.php?id=<?php echo $prog['ProgrammeID']; ?>"
                                                   class="btn btn-outline-primary"
                                                   target="_blank">
                                                    <i class="fas fa-external-link-alt me-2"></i>View Programme
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center mb-0">You are not currently leading any programmes.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="profileForm" method="POST" action="update_profile.php" class="text-black" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($staffData['Phone'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea class="form-control" name="bio" rows="4"><?php echo htmlspecialchars($staffData['Bio'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Profile Image</label>
                            <input type="file" class="form-control" name="profile_image" accept="image/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="profileForm" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Modal -->
    <div class="modal fade" id="moduleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Module</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="moduleForm" class="text-black" method="POST" action="update_module.php">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                        <input type="hidden" name="module_id" value="">

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Learning Outcomes</label>
                            <textarea class="form-control" name="learning_outcomes" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assessment Methods</label>
                            <textarea class="form-control" name="assessment_methods" rows="4" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="moduleForm" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle module edit
            const moduleModal = document.getElementById('moduleModal');
            moduleModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const moduleId = button.dataset.moduleId;
                const form = this.querySelector('#moduleForm');
                form.module_id.value = moduleId;
                
                // Fetch module data
                fetch(`get_module.php?id=${moduleId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const module = data.data;
                            form.querySelector('textarea[name="description"]').value = module.Description || '';
                            form.querySelector('textarea[name="learning_outcomes"]').value = module.LearningOutcomes || '';
                            form.querySelector('textarea[name="assessment_methods"]').value = module.AssessmentMethods || '';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching module data:', error);
                        alert('Failed to load module data. Please try again.');
                    });
            });
        });
    </script>
</body>
</html> 