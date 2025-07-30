<?php
require_once 'config/config.php';

$pageTitle = 'Contact Us - ' . SITE_NAME;
$pageDescription = 'Get in touch with our admissions team for more information about our programmes and application process.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Main Content -->
    <main style="margin-top: 50px;">
        <!-- Hero Section -->
        <section class="bg-gradient-primary text-white py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="display-4 fw-bold mb-4">Contact Us</h1>
                        <p class="lead mb-4">
                            Have questions about our programmes? Our admissions team is here to help
                            you find the right path for your educational journey.
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-center">
                            <i class="fas fa-envelope hero-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Information -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <!-- Contact Cards -->
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow">
                                    <div class="card-body text-center p-4">
                                        <div class="feature-icon bg-primary text-white rounded-circle mx-auto mb-3">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <h5>Email Us</h5>
                                        <p class="text-muted mb-3">Get in touch with our admissions team</p>
                                        <a href="mailto:admissions@university.ac.uk" class="btn btn-outline-primary">
                                            admissions@university.ac.uk
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow">
                                    <div class="card-body text-center p-4">
                                        <div class="feature-icon bg-success text-white rounded-circle mx-auto mb-3">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <h5>Call Us</h5>
                                        <p class="text-muted mb-3">Speak directly with our team</p>
                                        <a href="tel:+441234567890" class="btn btn-outline-success">
                                            +44 123 456 7890
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow">
                                    <div class="card-body text-center p-4">
                                        <div class="feature-icon bg-info text-white rounded-circle mx-auto mb-3">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <h5>Visit Us</h5>
                                        <p class="text-muted mb-3">Come to our campus for a tour</p>
                                        <address class="mb-0">
                                            University Campus<br>
                                            Technology Building<br>
                                            City, Postcode, UK
                                        </address>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow">
                                    <div class="card-body text-center p-4">
                                        <div class="feature-icon bg-warning text-white rounded-circle mx-auto mb-3">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <h5>Office Hours</h5>
                                        <p class="text-muted mb-3">When you can reach us</p>
                                        <div class="text-start">
                                            <strong>Monday - Friday:</strong> 9:00 AM - 5:00 PM<br>
                                            <strong>Saturday:</strong> 10:00 AM - 2:00 PM<br>
                                            <strong>Sunday:</strong> Closed
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Contact Form -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-paper-plane me-2"></i>Quick Contact
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-4">
                                    Send us a quick message and we'll get back to you within 24 hours.
                                </p>

                                <form>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Your Name</label>
                                        <input type="text" class="form-control" id="name" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="subject" class="form-label">Subject</label>
                                        <select class="form-select" id="subject" required>
                                            <option value="">Choose a topic...</option>
                                            <option value="general">General Enquiry</option>
                                            <option value="admissions">Admissions Information</option>
                                            <option value="programmes">Programme Details</option>
                                            <option value="fees">Fees and Funding</option>
                                            <option value="visit">Campus Visit</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" id="message" rows="4" required></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </form>

                                <div class="alert alert-info mt-3 mb-0">
                                    <small>
                                        <i class="fas fa-info-circle me-1"></i>
                                        For immediate assistance, please call us during office hours.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Departments -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center mb-5">
                        <h2 class="display-5 fw-bold">Department Contacts</h2>
                        <p class="lead text-muted">
                            For specific enquiries, you can contact our departments directly.
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-user-graduate me-2 text-primary"></i>
                                    Admissions Office
                                </h5>
                                <p class="card-text text-muted">
                                    Application process, entry requirements, and programme information.
                                </p>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-envelope me-2"></i>admissions@university.ac.uk</li>
                                    <li><i class="fas fa-phone me-2"></i>+44 123 456 7891</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-pound-sign me-2 text-success"></i>
                                    Finance Office
                                </h5>
                                <p class="card-text text-muted">
                                    Tuition fees, payment plans, scholarships, and financial aid.
                                </p>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-envelope me-2"></i>finance@university.ac.uk</li>
                                    <li><i class="fas fa-phone me-2"></i>+44 123 456 7892</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-users me-2 text-info"></i>
                                    Student Services
                                </h5>
                                <p class="card-text text-muted">
                                    Accommodation, support services, and student life information.
                                </p>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-envelope me-2"></i>student.services@university.ac.uk</li>
                                    <li><i class="fas fa-phone me-2"></i>+44 123 456 7893</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <h2 class="text-center mb-5">Frequently Asked Questions</h2>

                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        How do I apply for a programme?
                                    </button>
                                </h3>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        You can register your interest through our website, and our admissions team will
                                        contact you with detailed application information and guidance through the process.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        What are the entry requirements?
                                    </button>
                                </h3>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Entry requirements vary by programme. You can find specific requirements on each
                                        programme's detail page, or contact our admissions team for personalized guidance.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Can I visit the campus?
                                    </button>
                                </h3>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Yes! We offer campus tours and open days throughout the year. Contact us to
                                        schedule a visit or check our events calendar for upcoming open days.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                        What support is available for international students?
                                    </button>
                                </h3>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        We provide comprehensive support for international students including visa guidance,
                                        accommodation assistance, orientation programmes, and ongoing academic and personal support.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
  <?php include 'includes/footer.php'; ?> 
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
