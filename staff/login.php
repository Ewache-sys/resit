<?php
require_once '../config/config.php';

// Redirect if already logged in
if (Security::isLoggedIn() && Security::hasRole('Staff')) {
    redirectTo('dashboard.php');
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Verify CSRF token
    if (!Security::verifyCSRFToken($csrf_token)) {
        showAlert('Invalid request. Please try again.', 'danger');
        redirectTo('login.php');
    }

    // Attempt staff authentication
    $staffData = $staff->authenticate($username, $password);
    if ($staffData) {
        // Set session data
        $_SESSION['user_id'] = $staffData['StaffID'];
        $_SESSION['username'] = $staffData['Username'];
        $_SESSION['user_email'] = $staffData['Email'];
        $_SESSION['user_name'] = $staffData['Name'];
        $_SESSION['user_role'] = 'Staff';
        $_SESSION['last_activity'] = time();

        // Log activity
        $security->logActivity(
            $staffData['StaffID'],
            'Staff Login',
            'Staff',
            $staffData['StaffID']
        );

        redirectTo('dashboard.php');
    } else {
        showAlert('Invalid username or password.', 'danger');
    }
}

$pageTitle = 'Staff Login - ' . SITE_NAME;
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

    <style>
        body {
            background: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-icon {
            font-size: 3rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
        }
        .input-group-text {
            border-radius: 0.5rem 0 0 0.5rem;
            background-color: #f8f9fa;
        }
        .form-control:not(:first-child) {
            border-radius: 0 0.5rem 0.5rem 0;
        }
        .btn-login {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            color: #6c757d;
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <h1 class="h3 mb-3 text-black">Staff Login</h1>
            <p class="text-muted">Access your staff dashboard</p>
        </div>

        <?php displayAlert(); ?>

        <form method="POST" class="needs-validation text-black" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text"
                           class="form-control"
                           id="username"
                           name="username"
                           placeholder="Username"
                           required
                           autofocus>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password"
                           class="form-control"
                           id="password"
                           name="password"
                           placeholder="Password"
                           required>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="showPassword">
                    <label class="form-check-label" for="showPassword">
                        Show password
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 btn-login mb-3">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </button>

            <div class="text-center">
                <a href="../" class="back-link">
                    <i class="fas fa-arrow-left me-2"></i>Back to Website
                </a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show password functionality
            const showPasswordCheckbox = document.getElementById('showPassword');
            const passwordInput = document.getElementById('password');

            if (showPasswordCheckbox && passwordInput) {
                showPasswordCheckbox.addEventListener('change', function() {
                    passwordInput.type = this.checked ? 'text' : 'password';
                });
            }

            // Form validation
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
</body>
</html> 