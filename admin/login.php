<?php
require_once '../config/config.php';

// Redirect if already logged in
if (Security::isLoggedIn()) {
    redirectTo('dashboard.php');
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            $loginResult = $security->login($username, $password);

            if ($loginResult) {
                if (Security::hasRole('Admin')) {
                    redirectTo('dashboard.php');
                } else {
                    Security::logout();
                    $error = 'Access denied. Admin privileges required.';
                }
            } else {
                $error = 'Invalid username or password.';
            }
        }
    }
}

$pageTitle = 'Admin Login - ' . SITE_NAME;
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

    <script>
        // Show password functionality
        function initializeShowPassword() {
            const showPasswordCheckbox = document.getElementById('showPassword');
            const passwordInput = document.getElementById('password');
            
            if (!showPasswordCheckbox || !passwordInput) {
                console.error('Show password elements not found:', {
                    checkbox: !!showPasswordCheckbox,
                    input: !!passwordInput
                });
                return;
            }

            showPasswordCheckbox.addEventListener('change', function() {
                passwordInput.type = this.checked ? 'text' : 'password';
            });
        }

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeShowPassword);
        } else {
            initializeShowPassword();
        }
    </script>

    <style>
        body {
            background: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-card {
            max-width: 600px;
            width: 100%;
            box-shadow: var(--box-shadow-lg);
            border: none;
            border-radius: 12px;
            overflow: hidden;
           
        }

        .login-header {
            color: black;
            padding: 2rem;
            text-align: center;
        }

        .login-body {
            padding: 2rem;
        }

        .form-floating {
            margin-bottom: 1rem;
        }

        .form-floating input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .form-floating label {
            color: var(--muted-color);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: var(--box-shadow-lg);
        }

        .security-info {
            background-color: #f8f9fa;
            border-left: 4px solid var(--info-color);
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
        }

        .back-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
        }

        .back-link:hover {
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <!-- Back to Site Link -->
                <div class="text-center mb-4">
                    <a href="../index.php" class="back-link">
                        <i class="fas fa-arrow-left me-2"></i>Back to <?php echo SITE_NAME; ?>
                    </a>
                </div>

                <!-- Login Card -->
                <div class="card login-card">
                    <div class="login-header">
                        <i class="fas fa-user-shield fa-3x mb-3"></i>
                        <h3 class="mb-0">Admin Login</h3>
                        <p class="mb-0 opacity-75">Access the administration panel</p>
                    </div>

                    <div class="login-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="login.php" id="loginForm">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                            <div class="form-floating">
                                <input type="text"
                                       class="form-control"
                                       id="username"
                                       name="username"
                                       placeholder="Username"
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                       required
                                       autocomplete="username"
                                       autofocus>
                                <label for="username">
                                    <i class="fas fa-user me-2"></i>Username
                                </label>
                            </div>

                            <div class="form-floating">
                                <input type="password"
                                       class="form-control"
                                       id="password"
                                       name="password"
                                       placeholder="Password"
                                       required
                                       autocomplete="current-password">
                                <label for="password">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="showPassword">
                                <label class="form-check-label" for="showPassword">
                                    Show password
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-login w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Admin Panel
                            </button>
                        </form>

                        <div class="security-info">
                            <h6 class="mb-2">
                                <i class="fas fa-shield-alt text-info me-2"></i>Security Notice
                            </h6>
                            <small class="text-muted">
                                This is a secure area. All login attempts are logged and monitored.
                                Unauthorized access is prohibited.
                            </small>
                        </div>

                        
                    </div>
                </div>

                <div class="text-center mt-4 mb-4">
                    <small class="text-black opacity-75">
                        <?php echo SITE_NAME; ?> &copy; <?php echo date('Y'); ?> |
                        <a href="../privacy.php" class="text-black opacity-75">Privacy Policy</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form submission loading state
            const loginForm = document.getElementById('loginForm');
            loginForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing in...';
                submitBtn.disabled = true;
            });

            // Clear error message when typing
            const inputs = document.querySelectorAll('input[type="text"], input[type="password"]');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    const alert = document.querySelector('.alert-danger');
                    if (alert) {
                        alert.style.transition = 'opacity 0.3s ease';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 300);
                    }
                });
            });

            // Auto-focus on first empty field
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');

            if (!usernameInput.value) {
                usernameInput.focus();
            } else if (!passwordInput.value) {
                passwordInput.focus();
            }
        });

        // Security: Clear form data on page unload
        window.addEventListener('beforeunload', function() {
            document.getElementById('password').value = '';
        });

        // Prevent form resubmission on back button
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>
