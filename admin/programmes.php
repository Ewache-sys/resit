<?php
require_once '../config/config.php';

// Require admin login
Security::requireRole('Admin');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
        showAlert('Invalid request. Please try again.', 'danger');
        redirectTo('programmes.php');
    }

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'toggle_published':
            $programmeId = (int)($_POST['programme_id'] ?? 0);
            if ($programmeId > 0) {
                $result = $programme->togglePublished($programmeId);
                if ($result) {
                    $security->logActivity($_SESSION['user_id'], 'Toggle Published', 'Programmes', $programmeId);
                    showAlert('Programme status updated successfully.', 'success');
                } else {
                    showAlert('Failed to update programme status.', 'danger');
                }
            }
            break;

        case 'delete':
            $programmeId = (int)($_POST['programme_id'] ?? 0);
            if ($programmeId > 0) {
                $result = $programme->deleteProgramme($programmeId);
                if ($result) {
                    $security->logActivity($_SESSION['user_id'], 'Delete Programme', 'Programmes', $programmeId);
                    showAlert('Programme deleted successfully.', 'success');
                } else {
                    showAlert('Failed to delete programme.', 'danger');
                }
            }
            break;
    }

    redirectTo('programmes.php');
}

// Get filter parameters
$levelFilter = isset($_GET['level']) ? (int)$_GET['level'] : null;
$statusFilter = $_GET['status'] ?? '';
$searchQuery = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Get all levels for filter
$levels = $level->getAllLevels();

// Get programmes
$programmes = $programme->getAllProgrammes(true); // Include inactive

// Apply filters
if ($levelFilter) {
    $programmes = array_filter($programmes, function($prog) use ($levelFilter) {
        return $prog['LevelID'] == $levelFilter;
    });
}

if ($statusFilter === 'published') {
    $programmes = array_filter($programmes, function($prog) {
        return $prog['IsPublished'] == 1 && $prog['IsActive'] == 1;
    });
} elseif ($statusFilter === 'unpublished') {
    $programmes = array_filter($programmes, function($prog) {
        return $prog['IsPublished'] == 0 && $prog['IsActive'] == 1;
    });
} elseif ($statusFilter === 'inactive') {
    $programmes = array_filter($programmes, function($prog) {
        return $prog['IsActive'] == 0;
    });
}

if ($searchQuery) {
    $programmes = array_filter($programmes, function($prog) use ($searchQuery) {
        return stripos($prog['ProgrammeName'], $searchQuery) !== false ||
               stripos($prog['ProgrammeCode'], $searchQuery) !== false ||
               stripos($prog['Description'], $searchQuery) !== false;
    });
}

$pageTitle = 'Manage Programmes - Admin - ' . SITE_NAME;
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
<body>
    <!-- Admin Navigation -->
    <?php include 'includes/admin_nav.php'; ?>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/admin_sidebar.php'; ?>

            <!-- Main Content Area -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2 text-black">
                        <i class="fas fa-graduation-cap me-2 text-primary"></i>Manage Programmes
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="add_programme.php" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add New Programme
                            </a>
                         
                        </div>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php displayAlert(); ?>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="programmes.php" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search"
                                       placeholder="Search programmes..."
                                       value="<?php echo htmlspecialchars($searchQuery); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="level" class="form-label">Level</label>
                                <select class="form-select" id="level" name="level">
                                    <option value="">All Levels</option>
                                    <?php foreach ($levels as $lvl): ?>
                                        <option value="<?php echo $lvl['LevelID']; ?>"
                                                <?php echo $levelFilter == $lvl['LevelID'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($lvl['LevelName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="published" <?php echo $statusFilter === 'published' ? 'selected' : ''; ?>>Published</option>
                                    <option value="unpublished" <?php echo $statusFilter === 'unpublished' ? 'selected' : ''; ?>>Unpublished</option>
                                    <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search me-1"></i>Filter
                                    </button>
                                </div>
                            </div>
                        </form>

                        <?php if ($searchQuery || $levelFilter || $statusFilter): ?>
                            <div class="mt-3">
                                <a href="programmes.php" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Clear Filters
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Programmes Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            All Programmes
                            <span class="badge bg-primary ms-2"><?php echo count($programmes); ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($programmes)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                <h4>No programmes found</h4>
                                <p class="text-muted">Try adjusting your filters or add a new programme.</p>
                                <a href="programme_form.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Add First Programme
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-white">Programme</th>
                                            <th class="text-white">Level</th>
                                            <th class="text-white">Leader</th>
                                            <th class="text-center text-white">Modules</th>
                                            <th class="text-center text-white">Interested</th>
                                            <th class="text-center text-white">Status</th>
                                            <th class="text-center text-white">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($programmes as $prog): ?>
                                            <?php $stats = $programme->getProgrammeStats($prog['ProgrammeID']); ?>
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($prog['ProgrammeName']); ?></strong>
                                                        <br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($prog['ProgrammeCode']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?php echo htmlspecialchars($prog['LevelName']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($prog['ProgrammeLeader']): ?>
                                                        <small><?php echo htmlspecialchars($prog['ProgrammeLeader']); ?></small>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not assigned</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-info"><?php echo $stats['module_count']; ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-success"><?php echo $stats['interested_students']; ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <?php if (!$prog['IsActive']): ?>
                                                        <span class="badge bg-danger">Inactive</span>
                                                    <?php elseif ($prog['IsPublished']): ?>
                                                        <span class="badge bg-success">Published</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Draft</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="programme_form.php?id=<?php echo $prog['ProgrammeID']; ?>"
                                                           class="btn btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="../programme.php?id=<?php echo $prog['ProgrammeID']; ?>"
                                                           class="btn btn-outline-info" title="View" target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if ($prog['IsActive']): ?>
                                                            <button type="button"
                                                                    class="btn btn-outline-<?php echo $prog['IsPublished'] ? 'warning' : 'success'; ?>"
                                                                    title="<?php echo $prog['IsPublished'] ? 'Unpublish' : 'Publish'; ?>"
                                                                    onclick="togglePublished(<?php echo $prog['ProgrammeID']; ?>, '<?php echo htmlspecialchars($prog['ProgrammeName']); ?>')">
                                                                <i class="fas fa-<?php echo $prog['IsPublished'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <button type="button"
                                                                class="btn btn-outline-danger"
                                                                title="Delete"
                                                                onclick="deleteProgramme(<?php echo $prog['ProgrammeID']; ?>, '<?php echo htmlspecialchars($prog['ProgrammeName']); ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Hidden Forms for Actions -->
    <form id="toggleForm" method="POST" style="display: none;">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="action" value="toggle_published">
        <input type="hidden" name="programme_id" id="toggleProgrammeId">
    </form>

    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="programme_id" id="deleteProgrammeId">
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        function togglePublished(programmeId, programmeName) {
            if (confirm(`Are you sure you want to change the publication status of "${programmeName}"?`)) {
                document.getElementById('toggleProgrammeId').value = programmeId;
                document.getElementById('toggleForm').submit();
            }
        }

        function deleteProgramme(programmeId, programmeName) {
            if (confirm(`Are you sure you want to delete "${programmeName}"? This action cannot be undone.`)) {
                document.getElementById('deleteProgrammeId').value = programmeId;
                document.getElementById('deleteForm').submit();
            }
        }

        // Auto-submit form on filter change
        document.getElementById('status').addEventListener('change', function() {
            this.form.submit();
        });

        document.getElementById('level').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>
