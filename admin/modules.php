<?php
require_once __DIR__ . '/../config/config.php';

// Require admin privileges
Security::requireRole('Admin');

// Initialize Module class
$module = new Module($db);

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
                $moduleData = [
                    'code' => sanitize($_POST['code']),
                    'name' => sanitize($_POST['name']),
                    'leader_id' => $_POST['leader_id'] ?: null,
                    'description' => sanitize($_POST['description']),
                    'learning_outcomes' => sanitize($_POST['learning_outcomes']),
                    'assessment_methods' => sanitize($_POST['assessment_methods']),
                    'credits' => (int)$_POST['credits'],
                    'image' => null // Handle image upload if needed
                ];

                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = uploadImage($_FILES['image'], 'uploads/modules');
                    if ($uploadResult['success']) {
                        $moduleData['image'] = $uploadResult['path'];
                    } else {
                        $error = $uploadResult['message'];
                        break;
                    }
                }

                if ($action === 'create') {
                    if ($module->createModule($moduleData)) {
                        $success = 'Module created successfully.';
                    } else {
                        $error = 'Failed to create module.';
                    }
                } else {
                    $moduleId = (int)$_POST['module_id'];
                    if ($module->updateModule($moduleId, $moduleData)) {
                        $success = 'Module updated successfully.';
                    } else {
                        $error = 'Failed to update module.';
                    }
                }
                break;

            case 'delete':
                $moduleId = (int)$_POST['module_id'];
                $result = $module->deleteModule($moduleId);
                if ($result['success']) {
                    $success = $result['message'];
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Get all modules
$modules = $module->getAllModules();

// Get all staff for module leader selection
$allStaff = $staff->getAllStaff();

$pageTitle = 'Manage Modules - ' . SITE_NAME;
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
        .module-card {
            transition: transform 0.2s;
        }
        .module-card:hover {
            transform: translateY(-5px);
        }
        .credits-badge {
            position: absolute;
            top: 10px;
            right: 10px;
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
                    <h1 class="text-black">Manage Modules</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#moduleModal">
                        <i class="fas fa-plus me-2"></i>Add New Module
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

                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                    <?php foreach ($modules as $mod): ?>
                        <div class="col">
                            <div class="card h-100 module-card">
                                <div class="card-body">
                                    <span class="badge bg-primary credits-badge">
                                        <?php echo htmlspecialchars($mod['Credits']); ?> Credits
                                    </span>
                                    <h5 class="card-title">
                                        <?php echo htmlspecialchars($mod['ModuleCode']); ?>:
                                        <?php echo htmlspecialchars($mod['ModuleName']); ?>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <i class="fas fa-user-tie me-2"></i>
                                        <?php echo htmlspecialchars($mod['ModuleLeader'] ?? 'No leader assigned'); ?>
                                    </p>
                                    <p class="card-text">
                                        <?php echo truncateText($mod['Description'], 150); ?>
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent border-top-0">
                                    <button class="btn btn-sm btn-outline-primary edit-module"
                                            data-module-id="<?php echo $mod['ModuleID']; ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#moduleModal">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-module"
                                            data-module-id="<?php echo $mod['ModuleID']; ?>"
                                            data-module-name="<?php echo htmlspecialchars($mod['ModuleName']); ?>">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Module Modal -->
                <div class="modal fade" id="moduleModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add/Edit Module</h5>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="moduleForm" class="text-dark" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="create">
                                    <input type="hidden" name="module_id" value="">

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Module Code</label>
                                            <input type="text" class="form-control" name="code" required>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label">Module Name</label>
                                            <input type="text" class="form-control" name="name" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <label class="form-label">Module Leader</label>
                                            <select class="form-select" name="leader_id">
                                                <option value="">Select Module Leader</option>
                                                <?php foreach ($allStaff as $s): ?>
                                                    <option value="<?php echo $s['StaffID']; ?>">
                                                        <?php echo htmlspecialchars($s['Name']); ?> (<?php echo htmlspecialchars($s['Title']); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Credits</label>
                                            <input type="number" class="form-control" name="credits" value="20" min="0" max="60" required>
                                        </div>
                                    </div>

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

                                    <div class="mb-3">
                                        <label class="form-label">Module Image</label>
                                        <input type="file" class="form-control" name="image" accept="image/*">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" form="moduleForm" class="btn btn-primary">Save Module</button>
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
                            <div class="modal-body">
                                Are you sure you want to delete this module? This action cannot be undone.
                            </div>
                            <div class="modal-footer">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="module_id" value="">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Delete Module</button>
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
            // Handle edit module
            const moduleModal = document.getElementById('moduleModal');
            moduleModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const isEdit = button.classList.contains('edit-module');
                const form = this.querySelector('#moduleForm');
                const title = this.querySelector('.modal-title');
                
                if (isEdit) {
                    const moduleId = button.dataset.moduleId;
                    title.textContent = 'Edit Module';
                    form.action.value = 'update';
                    form.module_id.value = moduleId;
                    
                    // Fetch module data and populate form
                    fetch(`get_module.php?id=${moduleId}`)
                        .then(response => response.json())
                        .then(data => {
                            form.code.value = data.ModuleCode;
                            form.name.value = data.ModuleName;
                            form.leader_id.value = data.ModuleLeaderID || '';
                            form.credits.value = data.Credits;
                            form.description.value = data.Description;
                            form.learning_outcomes.value = data.LearningOutcomes;
                            form.assessment_methods.value = data.AssessmentMethods;
                        });
                } else {
                    title.textContent = 'Add New Module';
                    form.action.value = 'create';
                    form.module_id.value = '';
                    form.reset();
                }
            });

            // Handle delete module
            const deleteModal = document.getElementById('deleteModal');
            document.querySelectorAll('.delete-module').forEach(button => {
                button.addEventListener('click', function() {
                    const moduleId = this.dataset.moduleId;
                    const moduleName = this.dataset.moduleName;
                    const modal = new bootstrap.Modal(deleteModal);
                    deleteModal.querySelector('.modal-body').textContent = 
                        `Are you sure you want to delete the module "${moduleName}"? This action cannot be undone.`;
                    deleteModal.querySelector('input[name="module_id"]').value = moduleId;
                    modal.show();
                });
            });
        });
    </script>
</body>
</html> 