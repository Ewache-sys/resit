<?php
require_once __DIR__ . '/../config/config.php';

// Require  admin privileges
Security::requireRole('Admin');

// Get filter parameters
$userId = isset($_GET['user']) ? (int)$_GET['user'] : null;
$action = isset($_GET['action']) ? sanitize($_GET['action']) : null;
$table = isset($_GET['table']) ? sanitize($_GET['table']) : null;
$startDate = isset($_GET['start_date']) ? sanitize($_GET['start_date']) : null;
$endDate = isset($_GET['end_date']) ? sanitize($_GET['end_date']) : null;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 50;

// Build query
$sql = "
    SELECT l.LogID, l.Action, l.TableName, l.RecordID, l.OldValues,
           l.NewValues, l.IPAddress, l.UserAgent, l.CreatedAt,
           u.Username, u.FirstName, u.LastName
    FROM ActivityLog l
    LEFT JOIN Users u ON l.UserID = u.UserID
    WHERE 1=1
";
$params = [];

if ($userId) {
    $sql .= " AND l.UserID = ?";
    $params[] = $userId;
}

if ($action) {
    $sql .= " AND l.Action = ?";
    $params[] = $action;
}

if ($table) {
    $sql .= " AND l.TableName = ?";
    $params[] = $table;
}

if ($startDate) {
    $sql .= " AND DATE(l.CreatedAt) >= ?";
    $params[] = $startDate;
}

if ($endDate) {
    $sql .= " AND DATE(l.CreatedAt) <= ?";
    $params[] = $endDate;
}

// Get total count
$countSql = "SELECT COUNT(*) as count FROM (" . $sql . ") as subquery";
$stmt = $db->prepare($countSql);
$stmt->execute($params);
$totalLogs = $stmt->fetch()['count'];
$totalPages = ceil($totalLogs / $perPage);

// Add pagination
$sql .= " ORDER BY l.CreatedAt DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = ($page - 1) * $perPage;

// Get logs
$stmt = $db->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Get unique actions and tables for filters
$stmt = $db->prepare("SELECT DISTINCT Action FROM ActivityLog ORDER BY Action");
$stmt->execute();
$actions = $stmt->fetchAll();

$stmt = $db->prepare("SELECT DISTINCT TableName FROM ActivityLog WHERE TableName IS NOT NULL ORDER BY TableName");
$stmt->execute();
$tables = $stmt->fetchAll();

// Get all users for filter
$user = new User($db);
$users = $user->getAllUsers();

$pageTitle = 'Activity Log - ' . SITE_NAME;
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
        .log-entry {
            transition: background-color 0.2s;
        }
        .log-entry:hover {
            background-color: rgba(0,0,0,0.02);
        }
        .changes-data {
            max-height: 200px;
            overflow-y: auto;
        }
        pre {
            margin: 0;
            white-space: pre-wrap;
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
                    <h1 class="text-black">Activity Log</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='activity_log.php'">
                            <i class="fas fa-sync-alt me-2"></i>Reset Filters
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">User</label>
                                <select name="user" class="form-select">
                                    <option value="">All Users</option>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?php echo $u['UserID']; ?>"
                                                <?php echo $userId == $u['UserID'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($u['Username']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Action</label>
                                <select name="action" class="form-select">
                                    <option value="">All Actions</option>
                                    <?php foreach ($actions as $a): ?>
                                        <option value="<?php echo $a['Action']; ?>"
                                                <?php echo $action == $a['Action'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($a['Action']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Table</label>
                                <select name="table" class="form-select">
                                    <option value="">All Tables</option>
                                    <?php foreach ($tables as $t): ?>
                                        <option value="<?php echo $t['TableName']; ?>"
                                                <?php echo $table == $t['TableName'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($t['TableName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" 
                                       value="<?php echo $startDate; ?>">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" 
                                       value="<?php echo $endDate; ?>">
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Activity Log -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="text-white">Time</th>
                                        <th class="text-white">User</th>
                                        <th class="text-white">Action</th>
                                        <th class="text-white">Details</th>
                                        <th class="text-white">IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log): ?>
                                        <tr class="log-entry">
                                            <td class="text-nowrap">
                                                <?php echo formatDateTime($log['CreatedAt']); ?>
                                            </td>
                                            <td>
                                                <?php if ($log['Username']): ?>
                                                    <?php echo htmlspecialchars($log['Username']); ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($log['FirstName'] . ' ' . $log['LastName']); ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">System</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php
                                                    echo match($log['Action']) {
                                                        'Create' => 'success',
                                                        'Update' => 'info',
                                                        'Delete' => 'danger',
                                                        'Login' => 'primary',
                                                        'Logout' => 'secondary',
                                                        default => 'secondary'
                                                    };
                                                ?>">
                                                    <?php echo htmlspecialchars($log['Action']); ?>
                                                </span>
                                                <?php if ($log['TableName']): ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($log['TableName']); ?>
                                                        <?php if ($log['RecordID']): ?>
                                                            #<?php echo $log['RecordID']; ?>
                                                        <?php endif; ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($log['OldValues'] || $log['NewValues']): ?>
                                                    <button class="btn btn-sm btn-outline-secondary view-changes"
                                                            data-old='<?php echo htmlspecialchars($log['OldValues'] ?? ''); ?>'
                                                            data-new='<?php echo htmlspecialchars($log['NewValues'] ?? ''); ?>'
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#changesModal">
                                                        <i class="fas fa-eye me-2"></i>View Changes
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-nowrap">
                                                <?php echo htmlspecialchars($log['IPAddress']); ?>
                                                <br>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    <?php echo htmlspecialchars($log['UserAgent']); ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?<?php
                                            $_GET['page'] = $page - 1;
                                            echo http_build_query($_GET);
                                        ?>">Previous</a>
                                    </li>
                                    
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                            <a class="page-link" href="?<?php
                                                $_GET['page'] = $i;
                                                echo http_build_query($_GET);
                                            ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?<?php
                                            $_GET['page'] = $page + 1;
                                            echo http_build_query($_GET);
                                        ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Changes Modal -->
                <div class="modal fade" id="changesModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Changes Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Old Values</h6>
                                        <div class="changes-data old-values"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>New Values</h6>
                                        <div class="changes-data new-values"></div>
                                    </div>
                                </div>
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
            // Handle changes view
            document.querySelectorAll('.view-changes').forEach(button => {
                button.addEventListener('click', function() {
                    const oldValues = this.dataset.old;
                    const newValues = this.dataset.new;
                    
                    const oldContainer = document.querySelector('.old-values');
                    const newContainer = document.querySelector('.new-values');
                    
                    oldContainer.innerHTML = oldValues ? 
                        `<pre>${JSON.stringify(JSON.parse(oldValues), null, 2)}</pre>` : 
                        '<em>No old values</em>';
                    
                    newContainer.innerHTML = newValues ? 
                        `<pre>${JSON.stringify(JSON.parse(newValues), null, 2)}</pre>` : 
                        '<em>No new values</em>';
                });
            });

            // Auto-submit form on filter change
            document.querySelectorAll('form select').forEach(select => {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            });
        });
    </script>
</body>
</html> 