<?php
require_once __DIR__ . '/../config/config.php';

// Require admin privileges
Security::requireRole('Admin');

// Initialize User class
$user = new User($db);

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
                $userData = [
                    'username' => sanitize($_POST['username']),
                    'email' => sanitize($_POST['email']),
                    'first_name' => sanitize($_POST['first_name']),
                    'last_name' => sanitize($_POST['last_name']),
                    'role_id' => (int)$_POST['role_id'],
                    'is_active' => isset($_POST['is_active']),
                    'password' => $_POST['password'] ?? null
                ];

                if ($action === 'create') {
                    if (empty($userData['password'])) {
                        $error = 'Password is required for new users.';
                        break;
                    }
                    $result = $user->createUser($userData);
                } else {
                    $userId = (int)$_POST['user_id'];
                    $result = $user->updateUser($userId, $userData);
                }

                if ($result['success']) {
                    $success = $result['message'];
                } else {
                    $error = $result['message'];
                }
                break;

            case 'delete':
                $userId = (int)$_POST['user_id'];
                $result = $user->deleteUser($userId);
                if ($result['success']) {
                    $success = $result['message'];
                } else {
                    $error = $result['message'];
                }
                break;
        }
    }
}

// Get all users and roles
$users = $user->getAllUsers();
$roles = $user->getAllRoles();
$stats = $user->getUserStats();

$pageTitle = 'Manage Users - ' . SITE_NAME;
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
        .user-card {
            transition: transform 0.2s;
        }
        .user-card:hover {
            transform: translateY(-5px);
        }
        .role-badge {
            font-size: 0.8rem;
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
                    <h1 class="text-black">Manage Users</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                        <i class="fas fa-plus me-2"></i>Add New User
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

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <h2 class="card-text"><?php echo $stats['total']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Active Users</h5>
                                <h2 class="card-text">
                                    <?php
                                    foreach ($stats['by_status'] as $status) {
                                        if ($status['IsActive'] == 1) {
                                            echo $status['count'];
                                            break;
                                        }
                                    }
                                    ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Recent Logins</h5>
                                <h2 class="card-text"><?php echo $stats['recent_logins']; ?></h2>
                                <small class="text-muted">Last 7 days</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Users by Role</h5>
                                <?php foreach ($stats['by_role'] as $role): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small><?php echo htmlspecialchars($role['RoleName']); ?></small>
                                        <span class="badge bg-primary"><?php echo $role['count']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-white">Username</th>
                                        <th class="text-white">Name</th>
                                        <th class="text-white">Email</th>
                                        <th class="text-white">Role</th>
                                        <th class="text-white">Status</th>
                                        <th class="text-white">Last Login</th>
                                        <th class="text-white">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $u): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($u['Username']); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($u['FirstName'] . ' ' . $u['LastName']); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($u['Email']); ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo htmlspecialchars($u['RoleName']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $u['IsActive'] ? 'success' : 'secondary'; ?>">
                                                    <?php echo $u['IsActive'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo $u['LastLogin'] ? formatDateTime($u['LastLogin']) : 'Never'; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary edit-user"
                                                        data-user-id="<?php echo $u['UserID']; ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#userModal">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if ($u['UserID'] !== $_SESSION['user_id']): ?>
                                                    <button class="btn btn-sm btn-outline-danger delete-user"
                                                            data-user-id="<?php echo $u['UserID']; ?>"
                                                            data-username="<?php echo htmlspecialchars($u['Username']); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- User Modal -->
                <div class="modal fade" id="userModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add/Edit User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="userForm" method="POST" class="text-dark">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="create">
                                    <input type="hidden" name="user_id" value="">

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="first_name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="last_name" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" name="username" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select class="form-select" name="role_id" required>
                                            <option value="">Select Role</option>
                                            <?php foreach ($roles as $role): ?>
                                                <option value="<?php echo $role['RoleID']; ?>">
                                                    <?php echo htmlspecialchars($role['RoleName']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" name="password">
                                        <div class="form-text password-hint">
                                            Leave blank to keep current password when editing.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                            <label class="form-check-label">Active Account</label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" form="userForm" class="btn btn-primary">Save User</button>
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
                            <div class="modal-body text-white">
                                Are you sure you want to delete this user?
                            </div>
                            <div class="modal-footer">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Delete User</button>
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
            // Handle edit user
            const userModal = document.getElementById('userModal');
            userModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const isEdit = button.classList.contains('edit-user');
                const form = this.querySelector('#userForm');
                const title = this.querySelector('.modal-title');
                const passwordHint = form.querySelector('.password-hint');
                
                if (isEdit) {
                    const userId = button.dataset.userId;
                    title.textContent = 'Edit User';
                    form.action.value = 'update';
                    form.user_id.value = userId;
                    passwordHint.style.display = 'block';
                    
                    // Fetch user data and populate form
                    fetch(`get_user.php?id=${userId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const user = data.data;
                                form.username.value = user.Username;
                                form.email.value = user.Email;
                                form.first_name.value = user.FirstName;
                                form.last_name.value = user.LastName;
                                form.role_id.value = user.RoleID;
                                form.is_active.checked = user.IsActive == 1;
                                form.password.value = '';
                            }
                        });
                } else {
                    title.textContent = 'Add New User';
                    form.action.value = 'create';
                    form.user_id.value = '';
                    form.reset();
                    passwordHint.style.display = 'none';
                    form.is_active.checked = true;
                }
            });

            // Handle delete user
            document.querySelectorAll('.delete-user').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const username = this.dataset.username;
                    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    const modalBody = document.querySelector('#deleteModal .modal-body');
                    const userIdInput = document.querySelector('#deleteModal input[name="user_id"]');
                    
                    modalBody.textContent = `Are you sure you want to delete the user "${username}"?`;
                    userIdInput.value = userId;
                    modal.show();
                });
            });
        });
    </script>
</body>
</html> 