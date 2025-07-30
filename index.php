<?php
require_once 'config/config.php';
// Get filter parameters
$levelFilter = isset($_GET['level']) ? (int)$_GET['level'] : null;
$searchQuery = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Get all levels for filter
$levels = $level->getAllLevels();

// Get programmes based on filters
if (!empty($searchQuery)) {
    $programmes = $programme->searchProgrammes($searchQuery, $levelFilter);
} else {
    $programmes = $programme->getPublishedProgrammes($levelFilter);
}

// Get programme statistics for each
$programmeStats = [];
foreach ($programmes as $prog) {
    $programmeStats[$prog['ProgrammeID']] = $programme->getProgrammeStats($prog['ProgrammeID']);
}

$pageTitle = 'Discover Your Future - ' . SITE_NAME;
$pageDescription = 'Explore our comprehensive range of undergraduate and postgraduate programmes in Computer Science, AI, Cybersecurity, and Data Science.';
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
    <!-- Hero Section -->
     
    <section class="hero-section bg-gradient-primary text-white py-5" style="margin-top: 50px;">
        <div class="container">
            <div class="row align-items-center min-vh-50">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Discover Your Future</h1>
                    <p class="lead mb-4">
                        Explore our comprehensive range of undergraduate and postgraduate programmes
                        designed to prepare you for the digital future. From Computer Science to
                        Artificial Intelligence, find your path to success.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#programmes" class="btn btn-light btn-lg">
                            <i class="fas fa-search me-2"></i>Browse Programmes
                        </a>
                        <a href="about.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-info-circle me-2"></i>Learn More
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="text-center">
                        <i class="fas fa-graduation-cap hero-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search and Filter Section -->
    <section id="programmes" class="py-5 bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col-lg-8 mx-auto">
                    <form method="GET" action="index.php" class="card shadow-sm">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label for="search" class="form-label visually-hidden">Search programmes</label>
                                    <input type="text" class="form-control form-control-lg" id="search" name="search"
                                           placeholder="Search programmes, modules, or keywords..."
                                           value="<?php echo htmlspecialchars($searchQuery); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="level" class="form-label visually-hidden">Filter by level</label>
                                    <select class="form-select form-select-lg" id="level" name="level">
                                        <option value="">All Levels</option>
                                        <?php foreach ($levels as $lvl): ?>
                                            <option value="<?php echo $lvl['LevelID']; ?>"
                                                    <?php echo $levelFilter == $lvl['LevelID'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($lvl['LevelName']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-search me-2"></i>Search Programmes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Summary -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="h3 mb-0">
                            <?php if (!empty($searchQuery)): ?>
                                Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"
                            <?php else: ?>
                                Available Programmes
                            <?php endif; ?>
                        </h2>

                        <?php if (!empty($searchQuery) || $levelFilter): ?>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear Filters
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Programmes Grid -->
            <div class="row">
                <?php if (empty($programmes)): ?>
                    <div class="col-12">
                        <div class="card text-center py-5">
                            <div class="card-body">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h3>No programmes found</h3>
                                <p class="text-muted">Try adjusting your search criteria or browse all programmes.</p>
                                <a href="index.php" class="btn btn-primary">Browse All Programmes</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($programmes as $prog): ?>
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card h-100 shadow-sm programme-card">
                               
                            <?php if ($prog['Image']): ?>
                                    <img src="<?php echo htmlspecialchars($prog['Image']); ?>"
                                         class="card-img-top programme-image"
                                         alt="<?php echo htmlspecialchars($prog['ProgrammeName']); ?>">
                                <?php else: ?>
                                    <div class="card-img-top programme-image-placeholder d-flex align-items-center justify-content-center">
                                        <i class="fas fa-graduation-cap fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($prog['LevelName']); ?></span>
                                    </div>

                                    <h5 class="card-title">
                                        <a href="programme.php?id=<?php echo $prog['ProgrammeID']; ?>"
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($prog['ProgrammeName']); ?>
                                        </a>
                                    </h5>

                                    <p class="card-text text-muted flex-grow-1">
                                        <?php echo htmlspecialchars(truncateText($prog['Description'], 120)); ?>
                                    </p>

                                    <div class="programme-meta">
                                        <div class="row text-center border-top pt-3">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Modules</small>
                                                <strong><?php echo $programmeStats[$prog['ProgrammeID']]['module_count']; ?></strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Interested Students</small>
                                                <strong><?php echo $programmeStats[$prog['ProgrammeID']]['interested_students']; ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer bg-transparent">
                                    <div class="d-grid gap-2">
                                        <a href="programme.php?id=<?php echo $prog['ProgrammeID']; ?>"
                                           class="btn btn-primary">
                                            <i class="fas fa-eye me-2"></i>View Programme
                                        </a>
                                        <button type="button" class="btn btn-outline-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#interestModal"
                                                data-programme-id="<?php echo $prog['ProgrammeID']; ?>"
                                                data-programme-name="<?php echo htmlspecialchars($prog['ProgrammeName']); ?>">
                                            <i class="fas fa-heart me-2"></i>Register Interest
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="display-5 fw-bold text-black">Why Choose Our Programmes?</h2>
                    <p class="lead text-muted">
                        Our programmes are designed with industry needs in mind,
                        offering cutting-edge curriculum and excellent career prospects.
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="text-center">
                        <div class="feature-icon bg-primary text-white rounded-circle mx-auto mb-3">
                            <i class="fas fa-industry"></i>
                        </div>
                        <h5>Industry-Focused</h5>
                        <p class="text-muted">Curriculum designed with industry partners to ensure relevance and employability.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="text-center">
                        <div class="feature-icon bg-success text-white rounded-circle mx-auto mb-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5>Expert Faculty</h5>
                        <p class="text-muted">Learn from experienced academics and industry professionals.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="text-center">
                        <div class="feature-icon bg-info text-white rounded-circle mx-auto mb-3">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h5>Modern Facilities</h5>
                        <p class="text-muted">State-of-the-art labs and equipment for hands-on learning experience.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="text-center">
                        <div class="feature-icon bg-warning text-white rounded-circle mx-auto mb-3">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h5>Career Support</h5>
                        <p class="text-muted">Comprehensive career services and industry placement opportunities.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interest Registration Modal -->
    <div class="modal fade" id="interestModal" tabindex="-1" aria-labelledby="interestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="interestModalLabel">Register Your Interest</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="interestForm" method="POST" action="register_interest.php">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="programme_id" id="modalProgrammeId">

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You're registering interest for: <strong id="modalProgrammeName"></strong>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="studentName" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="studentName" name="student_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" name="country">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="currentEducation" class="form-label">Current Education Background</label>
                            <input type="text" class="form-control" id="currentEducation" name="current_education"
                                   placeholder="e.g., A-levels: Mathematics (A), Physics (B)">
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message to University (Optional)</label>
                            <textarea class="form-control" id="message" name="message" rows="3"
                                      placeholder="Tell us why you're interested in this programme..."></textarea>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="consent" name="consent" required>
                            <label class="form-check-label" for="consent">
                                I consent to receiving updates about this programme and related university information.
                                <small class="text-muted">(You can unsubscribe at any time)</small>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-heart me-2"></i>Register Interest
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once "includes/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Interest Modal Handler
        document.addEventListener('DOMContentLoaded', function() {
            const interestModal = document.getElementById('interestModal');
            if (interestModal) {
                interestModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const programmeId = button.getAttribute('data-programme-id');
                    const programmeName = button.getAttribute('data-programme-name');

                    document.getElementById('modalProgrammeId').value = programmeId;
                    document.getElementById('modalProgrammeName').textContent = programmeName;
                });
            }

            // Form submission with loading state
            const interestForm = document.getElementById('interestForm');
            if (interestForm) {
                interestForm.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registering...';
                    submitBtn.disabled = true;
                });
            }

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
