<?php
require_once 'config/config.php';

$message = '';
$messageType = '';
$token = isset($_GET['token']) ? sanitize($_GET['token']) : '';

// Handle unsubscribe request
if (!empty($token)) {
    $result = $studentInterest->unsubscribe($token);
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'danger';
}

$pageTitle = 'Unsubscribe - ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="Manage your email preferences and unsubscribe from programme updates.">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-graduation-cap me-2"></i>
                <?php echo SITE_NAME; ?>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">
                            <i class="fas fa-envelope-open-text me-2"></i>
                            Email Preferences
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $messageType; ?> mb-4" role="alert">
                                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($token)): ?>
                            <!-- No token provided -->
                            <div class="text-center">
                                <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                                <h4>Invalid Unsubscribe Link</h4>
                                <p class="text-muted mb-4">
                                    The unsubscribe link you clicked is invalid or has expired.
                                    Please use the unsubscribe link from a recent email.
                                </p>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                    <a href="index.php" class="btn btn-primary">
                                        <i class="fas fa-home me-2"></i>Back to Homepage
                                    </a>
                                    <a href="contact.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-envelope me-2"></i>Contact Support
                                    </a>
                                </div>
                            </div>
                        <?php elseif ($messageType === 'success'): ?>
                            <!-- Successful unsubscribe -->
                            <div class="text-center">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h4>Successfully Unsubscribed</h4>
                                <p class="text-muted mb-4">
                                    You have been successfully removed from our mailing list.
                                    You will no longer receive programme updates and notifications.
                                </p>

                                <div class="alert alert-info mb-4">
                                    <h6><i class="fas fa-info-circle me-2"></i>What happens next?</h6>
                                    <ul class="list-unstyled mb-0 text-start">
                                        <li>• You will stop receiving programme updates</li>
                                        <li>• Your registration data remains secure with us</li>
                                        <li>• You can re-register interest anytime</li>
                                        <li>• Important notifications may still be sent as required by law</li>
                                    </ul>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                    <a href="index.php" class="btn btn-primary">
                                        <i class="fas fa-graduation-cap me-2"></i>Browse Programmes
                                    </a>
                                    <a href="contact.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-envelope me-2"></i>Contact Us
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Failed unsubscribe -->
                            <div class="text-center">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h4>Unsubscribe Failed</h4>
                                <p class="text-muted mb-4">
                                    We were unable to process your unsubscribe request.
                                    This could be because you've already unsubscribed or the link has expired.
                                </p>

                                <div class="alert alert-warning mb-4">
                                    <h6><i class="fas fa-lightbulb me-2"></i>Need help?</h6>
                                    <p class="mb-0">
                                        If you continue to receive emails or need assistance,
                                        please contact our support team with your email address.
                                    </p>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                    <a href="contact.php" class="btn btn-primary">
                                        <i class="fas fa-headset me-2"></i>Contact Support
                                    </a>
                                    <a href="index.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-home me-2"></i>Back to Homepage
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-shield-alt me-2 text-primary"></i>
                            Your Privacy Matters
                        </h5>
                        <p class="card-text text-muted">
                            We respect your privacy and your choice to unsubscribe.
                            Your personal information remains secure and is handled in accordance with our
                            <a href="privacy.php" class="text-decoration-none">Privacy Policy</a>.
                            We only send relevant programme information and never share your data with third parties.
                        </p>

                        <div class="row text-center mt-4">
                            <div class="col-md-4 mb-3">
                                <i class="fas fa-lock fa-2x text-primary mb-2"></i>
                                <h6>Secure Data</h6>
                                <small class="text-muted">Your information is encrypted and protected</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <i class="fas fa-user-shield fa-2x text-success mb-2"></i>
                                <h6>No Sharing</h6>
                                <small class="text-muted">We never share your data with third parties</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <i class="fas fa-heart fa-2x text-danger mb-2"></i>
                                <h6>Easy Re-subscribe</h6>
                                <small class="text-muted">You can register interest again anytime</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="privacy.php" class="text-muted text-decoration-none me-3">Privacy Policy</a>
                    <a href="contact.php" class="text-muted text-decoration-none">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
