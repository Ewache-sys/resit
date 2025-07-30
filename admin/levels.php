<?php
require_once __DIR__ . '/../config/config.php';

// Require admin privileges
Security::requireRole('Admin');

// Initialize Level class
$level = new Level($db);

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
                $levelData = [
                    'name' => sanitize($_POST['name']),
                    'description' => sanitize($_POST['description']),
                    'sort_order' => (int)$_POST['sort_order']
                ];

                if ($action === 'create') {
                    if ($level->createLevel($levelData)) {
                        $success = 'Level created successfully.';
                    } else {
                        $error = 'Failed to create level.';
                    }
                } else {
                    $levelId = (int)$_POST['level_id'];
                    if ($level->updateLevel($levelId, $levelData)) {
                        $success = 'Level updated successfully.';
                    } else {
                        $error = 'Failed to update level.';
                    }
                }
                break;

            case 'delete':
                $levelId = (int)$_POST['level_id'];
                $result = $level->deleteLevel($levelId);
                if ($result['success']) {
                    $success = $result['message'];
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Get all levels
$levels = $level->getAllLevels();

$pageTitle = 'Manage Levels - ' . SITE_NAME;
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
        .level-card {
            transition: transform 0.2s;
        }
        .level-card:hover {
            transform: translateY(-5px);
        }
        .sort-handle {
            cursor: move;
            color: #6c757d;
        }
        .sort-handle:hover {
            color: #343a40;
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
                    <h1 class="text-dark">Manage Levels</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#levelModal">
                        <i class="fas fa-plus me-2"></i>Add New Level
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

                <!-- Levels Grid -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                    <?php foreach ($levels as $lvl): ?>
                        <div class="col">
                            <div class="card h-100 level-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="sort-handle me-2">
                                            <i class="fas fa-grip-vertical"></i>
                                        </span>
                                        <h5 class="card-title mb-0">
                                            <?php echo htmlspecialchars($lvl['LevelName']); ?>
                                        </h5>
                                    </div>
                                    
                                    <p class="card-text">
                                        <?php echo htmlspecialchars($lvl['Description'] ?? 'No description available.'); ?>
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-secondary">
                                            Sort Order: <?php echo $lvl['SortOrder']; ?>
                                        </span>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary edit-level"
                                                    data-level-id="<?php echo $lvl['LevelID']; ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#levelModal"
                                                    data-level='<?php echo htmlspecialchars(json_encode($lvl)); ?>'>
                                                <i class="fas fa-edit me-2"></i>Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-level"
                                                    data-level-id="<?php echo $lvl['LevelID']; ?>"
                                                    data-level-name="<?php echo htmlspecialchars($lvl['LevelName']); ?>">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Level Modal -->
                <div class="modal fade" id="levelModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add/Edit Level</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="levelForm" method="POST" class="text-dark">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="create">
                                    <input type="hidden" name="level_id" value="">

                                    <div class="mb-3">
                                        <label class="form-label">Level Name</label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="3"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Sort Order</label>
                                        <input type="number" class="form-control" name="sort_order" 
                                               value="0" min="0" required>
                                        <div class="form-text">
                                            Lower numbers appear first. Use this to customize the display order.
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" form="levelForm" class="btn btn-primary">Save Level</button>
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
                                Are you sure you want to delete this level?
                            </div>
                            <div class="modal-footer">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="level_id" value="">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Delete Level</button>
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
            // Handle edit level
            const levelModal = document.getElementById('levelModal');
            levelModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const isEdit = button.classList.contains('edit-level');
                const form = this.querySelector('#levelForm');
                const title = this.querySelector('.modal-title');
                
                if (isEdit) {
                    const levelData = JSON.parse(button.dataset.level);
                    title.textContent = 'Edit Level';
                    form.action.value = 'update';
                    form.level_id.value = levelData.LevelID;
                    form.name.value = levelData.LevelName;
                    form.description.value = levelData.Description || '';
                    form.sort_order.value = levelData.SortOrder;
                } else {
                    title.textContent = 'Add New Level';
                    form.action.value = 'create';
                    form.level_id.value = '';
                    form.reset();
                }
            });

            // Handle delete level
            document.querySelectorAll('.delete-level').forEach(button => {
                button.addEventListener('click', function() {
                    const levelId = this.dataset.levelId;
                    const levelName = this.dataset.levelName;
                    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    const modalBody = document.querySelector('#deleteModal .modal-body');
                    const levelIdInput = document.querySelector('#deleteModal input[name="level_id"]');
                    
                    modalBody.textContent = `Are you sure you want to delete the level "${levelName}"?`;
                    levelIdInput.value = levelId;
                    modal.show();
                });
            });
        });
    </script>
</body>
</html> 