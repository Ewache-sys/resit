<?php
require_once 'config/config.php';
$pageTitle = 'Privacy Policy - ' . SITE_NAME;
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
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h1 class="mb-4">Privacy Policy</h1>
                        <p class="text-muted">Last updated: <?php echo date('F j, Y'); ?></p>

                        <section class="mb-5">
                            <h2 class="h4">1. Introduction</h2>
                            <p>
                                <?php echo SITE_NAME; ?> ("we", "our", or "us") is committed to protecting your privacy. 
                                This Privacy Policy explains how we collect, use, disclose, and safeguard your information 
                                when you visit our website and use our services.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4">2. Information We Collect</h2>
                            <h3 class="h5 mb-3">2.1 Personal Information</h3>
                            <p>We collect personal information that you voluntarily provide to us when you:</p>
                            <ul>
                                <li>Register interest in our programmes</li>
                                <li>Create an account</li>
                                <li>Contact us through our website</li>
                                <li>Subscribe to our communications</li>
                            </ul>

                            <p>This information may include:</p>
                            <ul>
                                <li>Name and contact details</li>
                                <li>Email address</li>
                                <li>Phone number</li>
                                <li>Educational background</li>
                                <li>Country of residence</li>
                            </ul>

                            <h3 class="h5 mb-3">2.2 Automatically Collected Information</h3>
                            <p>When you visit our website, we automatically collect certain information, including:</p>
                            <ul>
                                <li>IP address</li>
                                <li>Browser type and version</li>
                                <li>Operating system</li>
                                <li>Access times and dates</li>
                                <li>Pages viewed</li>
                                <li>Referring website addresses</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4">3. How We Use Your Information</h2>
                            <p>We use the collected information for various purposes, including:</p>
                            <ul>
                                <li>Processing your programme interest registrations</li>
                                <li>Communicating with you about our programmes and services</li>
                                <li>Sending you important updates and administrative information</li>
                                <li>Improving our website and services</li>
                                <li>Analyzing usage patterns and trends</li>
                                <li>Preventing fraudulent activities</li>
                                <li>Complying with legal obligations</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4">4. Data Security</h2>
                            <p>
                                We implement appropriate technical and organizational security measures to protect your 
                                personal information, including:
                            </p>
                            <ul>
                                <li>Secure Socket Layer (SSL) encryption for data transmission</li>
                                <li>Password hashing using industry-standard algorithms</li>
                                <li>Regular security assessments and updates</li>
                                <li>Access controls and authentication mechanisms</li>
                                <li>Data backup and recovery procedures</li>
                                <li>Staff training on data protection and security</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4">5. Data Retention</h2>
                            <p>
                                We retain your personal information for as long as necessary to fulfill the purposes 
                                outlined in this Privacy Policy, unless a longer retention period is required by law.
                                When determining the retention period, we consider:
                            </p>
                            <ul>
                                <li>The amount, nature, and sensitivity of the information</li>
                                <li>The potential risk of harm from unauthorized use or disclosure</li>
                                <li>The purposes for which we process the information</li>
                                <li>Legal and regulatory requirements</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4">6. Your Rights</h2>
                            <p>You have the right to:</p>
                            <ul>
                                <li>Access your personal information</li>
                                <li>Correct inaccurate or incomplete information</li>
                                <li>Request deletion of your information</li>
                                <li>Object to or restrict processing of your information</li>
                                <li>Receive a copy of your information in a structured format</li>
                                <li>Withdraw consent at any time</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4">7. Third-Party Services</h2>
                            <p>
                                Our website may contain links to third-party websites or services. We are not responsible 
                                for the privacy practices or content of these third parties. We encourage you to review 
                                their privacy policies before providing any personal information.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4">8. Cookies</h2>
                            <p>
                                We use cookies and similar tracking technologies to enhance your browsing experience. 
                                You can control cookie preferences through your browser settings. However, disabling 
                                certain cookies may limit your ability to use some features of our website.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4">9. Children's Privacy</h2>
                            <p>
                                Our services are not intended for individuals under the age of 16. We do not knowingly 
                                collect personal information from children. If you believe we have collected information 
                                from a child, please contact us immediately.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4">10. Changes to This Policy</h2>
                            <p>
                                We may update this Privacy Policy from time to time. We will notify you of any material 
                                changes by posting the updated policy on our website and updating the "Last updated" date.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4">11. Contact Us</h2>
                            <p>
                                If you have questions, concerns, or requests related to this Privacy Policy or your 
                                personal information, please contact us at:
                            </p>
                            <address>
                                <strong><?php echo SITE_NAME; ?></strong><br>
                                Email: privacy@example.com<br>
                                Phone: +44 123 456 7890<br>
                                Address: 123 University Street<br>
                                City, Country, Postal Code
                            </address>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 