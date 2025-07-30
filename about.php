<?php
require_once 'config/config.php';

$pageTitle = 'About Us - ' . SITE_NAME;
$pageDescription = 'Learn about our university, our mission, and our commitment to excellence in education and research.';
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
                <div class="row align-items-center min-vh-50">
                    <div class="col-lg-6">
                        <h1 class="display-4 fw-bold mb-4">About Our University</h1>
                        <p class="lead mb-4">
                            We are a leading institution dedicated to excellence in education,
                            research, and innovation in the fields of computer science,
                            artificial intelligence, and technology.
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-center">
                            <i class="fas fa-university hero-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission & Vision -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100 border-0 shadow">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <div class="feature-icon bg-primary text-white rounded-circle mx-auto mb-3">
                                        <i class="fas fa-bullseye"></i>
                                    </div>
                                    <h3>Our Mission</h3>
                                </div>
                                <p class="text-muted mb-0">
                                    To provide world-class education and research opportunities that prepare
                                    students for successful careers in technology and innovation. We strive
                                    to foster critical thinking, creativity, and ethical leadership in our graduates.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100 border-0 shadow">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <div class="feature-icon bg-success text-white rounded-circle mx-auto mb-3">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                    <h3>Our Vision</h3>
                                </div>
                                <p class="text-muted mb-0">
                                    To be recognized globally as a premier institution for technology education
                                    and research, leading the way in artificial intelligence, cybersecurity,
                                    and sustainable computing solutions for the future.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center mb-5">
                        <h2 class="display-5 fw-bold">Why Choose Us?</h2>
                        <p class="lead text-muted">
                            We offer a unique combination of academic excellence, industry connections,
                            and cutting-edge research opportunities.
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="text-center">
                            <div class="feature-icon bg-primary text-white rounded-circle mx-auto mb-3">
                                <i class="fas fa-award"></i>
                            </div>
                            <h5>Excellence in Education</h5>
                            <p class="text-muted">
                                Top-ranked programmes with industry-relevant curriculum
                                and world-class faculty.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="text-center">
                            <div class="feature-icon bg-success text-white rounded-circle mx-auto mb-3">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <h5>Industry Partnerships</h5>
                            <p class="text-muted">
                                Strong connections with leading technology companies
                                for internships and career opportunities.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="text-center">
                            <div class="feature-icon bg-info text-white rounded-circle mx-auto mb-3">
                                <i class="fas fa-microscope"></i>
                            </div>
                            <h5>Cutting-edge Research</h5>
                            <p class="text-muted">
                                Access to state-of-the-art research facilities and
                                opportunities to work on groundbreaking projects.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="text-center">
                            <div class="feature-icon bg-warning text-white rounded-circle mx-auto mb-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5>Student Support</h5>
                            <p class="text-muted">
                                Comprehensive support services including career guidance,
                                academic support, and personal development.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics -->
        <section class="py-5">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-3 mb-4">
                        <div class="card border-0">
                            <div class="card-body">
                                <h2 class="display-4 text-primary fw-bold">95%</h2>
                                <p class="text-muted mb-0">Graduate Employment Rate</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0">
                            <div class="card-body">
                                <h2 class="display-4 text-success fw-bold">15:1</h2>
                                <p class="text-muted mb-0">Student to Faculty Ratio</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0">
                            <div class="card-body">
                                <h2 class="display-4 text-info fw-bold">50+</h2>
                                <p class="text-muted mb-0">Industry Partners</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0">
                            <div class="card-body">
                                <h2 class="display-4 text-warning fw-bold">£2M</h2>
                                <p class="text-muted mb-0">Annual Research Funding</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Call-to-Action -->
        <section class="py-5 bg-primary text-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h3 class="mb-2">Ready to Join Our Community?</h3>
                        <p class="mb-0">
                            Explore our programmes and take the first step towards your future in technology.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="index.php" class="btn btn-light btn-lg me-3">
                            <i class="fas fa-graduation-cap me-2"></i>View Programmes
                        </a>
                        <a href="contact.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-envelope me-2"></i>Contact Us
                        </a>
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
