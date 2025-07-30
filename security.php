<?php
require_once 'config/config.php';
$pageTitle = 'Security Information - ' . SITE_NAME;
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
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        .security-icon {
            font-size: 2.5rem;
            color: var(--bs-primary);
            margin-bottom: 1rem;
        }
        .feature-card {
            height: 100%;
            transition: transform 0.2s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container py-5">
        <div class="row justify-content-center py-5">
            <div class="col-lg-10">
                <div class="text-center text-black mb-5">
                    <h1>Our Security Measures</h1>
                    <p class="lead">
                        We take the security of your information seriously. Here's how we protect your data 
                        and ensure a safe experience on our platform.
                    </p>
                </div>

                <!-- Security Features Grid -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
                    <!-- Data Encryption -->
                    <div class="col">
                        <div class="card feature-card">
                            <div class="card-body text-center">
                                <i class="fas fa-lock security-icon"></i>
                                <h3 class="h5">Data Encryption</h3>
                                <p>
                                    All data transmission is secured using SSL/TLS encryption, ensuring your 
                                    information remains private during transfer.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Password Security -->
                    <div class="col">
                        <div class="card feature-card">
                            <div class="card-body text-center">
                                <i class="fas fa-key security-icon"></i>
                                <h3 class="h5">Password Security</h3>
                                <p>
                                    Passwords are securely hashed using bcrypt, a strong cryptographic algorithm 
                                    designed for password protection.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Session Management -->
                    <div class="col">
                        <div class="card feature-card">
                            <div class="card-body text-center">
                                <i class="fas fa-shield-alt security-icon"></i>
                                <h3 class="h5">Session Security</h3>
                                <p>
                                    Secure session management with automatic timeout and protection against 
                                    session hijacking attempts.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- CSRF Protection -->
                    <div class="col">
                        <div class="card feature-card">
                            <div class="card-body text-center">
                                <i class="fas fa-user-shield security-icon"></i>
                                <h3 class="h5">CSRF Protection</h3>
                                <p>
                                    All forms are protected against Cross-Site Request Forgery attacks using 
                                    unique tokens.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Input Validation -->
                    <div class="col">
                        <div class="card feature-card">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle security-icon"></i>
                                <h3 class="h5">Input Validation</h3>
                                <p>
                                    Strict input validation and sanitization to prevent XSS and SQL injection 
                                    attacks.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Monitoring -->
                    <div class="col">
                        <div class="card feature-card">
                            <div class="card-body text-center">
                                <i class="fas fa-eye security-icon"></i>
                                <h3 class="h5">Activity Monitoring</h3>
                                <p>
                                    Comprehensive activity logging and monitoring to detect and prevent 
                                    suspicious activities.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Security Information -->
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <section class="mb-5">
                            <h2 class="h4">Technical Security Measures</h2>
                            <p>Our platform implements multiple layers of security:</p>
                            <ul>
                                <li>
                                    <strong>SSL/TLS Encryption:</strong> All data transmitted between your browser 
                                    and our servers is encrypted using industry-standard protocols.
                                </li>
                                <li>
                                    <strong>Password Security:</strong> We use bcrypt hashing with unique salts for 
                                    each password, making them extremely resistant to cracking attempts.
                                </li>
                                <li>
                                    <strong>Database Security:</strong> Our databases are protected by firewalls and 
                                    access controls, with regular security audits and updates.
                                </li>
                                <li>
                                    <strong>Session Management:</strong> Secure session handling with HTTPOnly cookies, 
                                    strict session validation, and automatic timeout for inactive sessions.
                                </li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4">Protection Against Common Threats</h2>
                            <p>We actively protect against various security threats:</p>
                            <ul>
                                <li>
                                    <strong>XSS Prevention:</strong> All user input is properly escaped and sanitized 
                                    to prevent cross-site scripting attacks.
                                </li>
                                <li>
                                    <strong>SQL Injection:</strong> We use prepared statements and parameterized queries 
                                    to prevent SQL injection attacks.
                                </li>
                                <li>
                                    <strong>CSRF Protection:</strong> Every form submission requires a valid CSRF token 
                                    to prevent cross-site request forgery.
                                </li>
                                <li>
                                    <strong>Rate Limiting:</strong> We implement rate limiting on sensitive operations 
                                    to prevent brute force attacks.
                                </li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4">Access Control and Authentication</h2>
                            <p>Our access control system ensures:</p>
                            <ul>
                                <li>Role-based access control (RBAC) for different user types</li>
                                <li>Secure password policies requiring strong passwords</li>
                                <li>Regular session validation and automatic timeout</li>
                                <li>IP-based access monitoring and suspicious activity detection</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4">Data Protection</h2>
                            <p>We protect your data through:</p>
                            <ul>
                                <li>Regular data backups with secure off-site storage</li>
                                <li>Data encryption at rest and in transit</li>
                                <li>Strict access controls and audit logging</li>
                                <li>Regular security assessments and penetration testing</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4">Incident Response</h2>
                            <p>Our security incident response plan includes:</p>
                            <ul>
                                <li>24/7 monitoring and alert systems</li>
                                <li>Dedicated incident response team</li>
                                <li>Regular security drills and updates</li>
                                <li>Transparent communication about security incidents</li>
                            </ul>
                        </section>

                        <section>
                            <h2 class="h4">Security Best Practices for Users</h2>
                            <p>We recommend the following security practices:</p>
                            <ul>
                                <li>Use strong, unique passwords for your account</li>
                                <li>Keep your login credentials confidential</li>
                                <li>Log out when using shared computers</li>
                                <li>Report any suspicious activities immediately</li>
                                <li>Keep your browser and operating system updated</li>
                            </ul>
                        </section>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="text-center mt-5">
                    <h2 class="h4">Security Concerns?</h2>
                    <p>
                        If you have any security concerns or notice suspicious activity, please contact our 
                        security team immediately:
                    </p>
                    <p>
                        <strong>Email:</strong> security@example.com<br>
                        <strong>Emergency Phone:</strong> +44 123 456 7890
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 