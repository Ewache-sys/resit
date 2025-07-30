<?php
require_once 'config/config.php';

// Get programme ID from URL
$programmeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($programmeId <= 0) {
    showAlert('Programme not found.', 'danger');
    redirectTo('index.php');
}

// Get programme data
$programmeData = $programme->getProgrammeById($programmeId, true);
if (!$programmeData) {
    showAlert('Programme not found or not available.', 'danger');
    redirectTo('index.php');
}

// Get programme modules by year
$modulesByYear = $programme->getProgrammeModules($programmeId);

// Get programme statistics
$stats = $programme->getProgrammeStats($programmeId);

// Check if student just registered
$justRegistered = isset($_GET['registered']) && $_GET['registered'] === '1';

$pageTitle = htmlspecialchars($programmeData['ProgrammeName']) . ' - ' . SITE_NAME;
$pageDescription = htmlspecialchars(truncateText($programmeData['Description'], 160));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo $pageDescription; ?>">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Navigation -->
 <?php include 'includes/navbar.php'; ?>

    <!-- Main Content -->
    <main id="main-content" style="margin-top: 50px;">
        <!-- Alert Messages -->
        <div class="container mt-3">
            <?php displayAlert(); ?>

            <?php if ($justRegistered): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Thank you!</strong> Your interest has been registered successfully.
                    We'll contact you with programme updates and important information.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Programme Header -->
        <section class="programme-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <span class="badge bg-light text-dark fs-6 me-2">
                                <?php echo htmlspecialchars($programmeData['LevelName']); ?>
                            </span>
                            <span class="badge bg-success fs-6">
                                <?php echo htmlspecialchars($programmeData['ProgrammeCode']); ?>
                            </span>
                        </div>

                        <h1 class="display-5 fw-bold mb-3">
                            <?php echo htmlspecialchars($programmeData['ProgrammeName']); ?>
                        </h1>

                        <p class="lead mb-4">
                            <?php echo htmlspecialchars($programmeData['Description']); ?>
                        </p>

                        <div class="d-flex flex-wrap gap-3">
                            <button type="button" class="btn btn-light btn-lg"
                                    data-bs-toggle="modal" data-bs-target="#interestModal">
                                <i class="fas fa-heart me-2"></i>Register Interest
                            </button>
                            <a href="#modules" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-list me-2"></i>View Modules
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <?php if ($programmeData['Image']): ?>
                            <img src="<?php echo htmlspecialchars($programmeData['Image']); ?>"
                                 class="img-fluid rounded shadow"
                                 alt="<?php echo htmlspecialchars($programmeData['ProgrammeName']); ?>">
                        <?php else: ?>
                            <div class="text-center p-5 bg-light rounded shadow">
                                <i class="fas fa-graduation-cap fa-5x text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Programme Information -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Programme Details -->
                        <div class="card programme-info-card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Programme Information
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Duration</div>
                                            <div class="info-value">
                                                <?php echo htmlspecialchars($programmeData['Duration'] ?: 'Not specified'); ?>
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <div class="info-label">Level</div>
                                            <div class="info-value">
                                                <?php echo htmlspecialchars($programmeData['LevelName']); ?>
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <div class="info-label">Programme Code</div>
                                            <div class="info-value">
                                                <?php echo htmlspecialchars($programmeData['ProgrammeCode']); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Total Modules</div>
                                            <div class="info-value">
                                                <?php echo $stats['module_count']; ?> modules
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <div class="info-label">Interested Students</div>
                                            <div class="info-value">
                                                <?php echo $stats['interested_students']; ?> students
                                            </div>
                                        </div>

                                        <?php if ($programmeData['ProgrammeLeader']): ?>
                                        <div class="info-item">
                                            <div class="info-label">Programme Leader</div>
                                            <div class="info-value">
                                                <?php echo htmlspecialchars($programmeData['ProgrammeLeader']); ?>
                                                <?php if ($programmeData['LeaderTitle']): ?>
                                                    <br><small class="text-muted">
                                                        <?php echo htmlspecialchars($programmeData['LeaderTitle']); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Entry Requirements -->
                        <?php if ($programmeData['EntryRequirements']): ?>
                        <div class="card programme-info-card mb-4">
                            <div class="card-header bg-info text-white">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-clipboard-check me-2"></i>Entry Requirements
                                </h3>
                            </div>
                            <div class="card-body">
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($programmeData['EntryRequirements'])); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Career Prospects -->
                        <?php if ($programmeData['CareerProspects']): ?>
                        <div class="card programme-info-card mb-4">
                            <div class="card-header bg-success text-white">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-briefcase me-2"></i>Career Prospects
                                </h3>
                            </div>
                            <div class="card-body">
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($programmeData['CareerProspects'])); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Programme Modules -->
                        <div class="card programme-info-card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-book me-2"></i>Programme Structure
                                </h3>
                            </div>
                            <div class="card-body">
                                <?php if (empty($modulesByYear)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                        <h4>Module information coming soon</h4>
                                        <p class="text-muted">The module structure for this programme is currently being finalized.</p>
                                    </div>
                                <?php else: ?>
                                    <!-- Year Tabs -->
                                    <ul class="nav nav-tabs mb-4" role="tablist">
                                        <?php $firstYear = true; ?>
                                        <?php foreach ($modulesByYear as $year => $modules): ?>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link <?php echo $firstYear ? 'active' : ''; ?>"
                                                        id="year<?php echo $year; ?>-tab"
                                                        data-bs-toggle="tab"
                                                        data-bs-target="#year<?php echo $year; ?>"
                                                        type="button"
                                                        role="tab">
                                                    Year <?php echo $year; ?>
                                                </button>
                                            </li>
                                            <?php $firstYear = false; ?>
                                        <?php endforeach; ?>
                                    </ul>

                                    <!-- Year Content -->
                                    <div class="tab-content">
                                        <?php $firstYear = true; ?>
                                        <?php foreach ($modulesByYear as $year => $modules): ?>
                                            <div class="tab-pane fade <?php echo $firstYear ? 'show active' : ''; ?>"
                                                 id="year<?php echo $year; ?>"
                                                 role="tabpanel">
                                                
                                                <!-- Group modules by semester -->
                                                <?php
                                                $modulesBySemester = [];
                                                foreach ($modules as $module) {
                                                    $semester = $module['Semester'] ?: 'Full Year';
                                                    if (!isset($modulesBySemester[$semester])) {
                                                        $modulesBySemester[$semester] = [];
                                                    }
                                                    $modulesBySemester[$semester][] = $module;
                                                }
                                                ksort($modulesBySemester); // Sort semesters
                                                ?>

                                                <?php foreach ($modulesBySemester as $semester => $semesterModules): ?>
                                                    <h4 class="h5 mb-3">
                                                        <?php echo htmlspecialchars($semester); ?>
                                                        <small class="text-muted">
                                                            (<?php echo count($semesterModules); ?> modules)
                                                        </small>
                                                    </h4>
                                                    <div class="row g-4 mb-4">
                                                        <?php foreach ($semesterModules as $module): ?>
                                                            <div class="col-md-6">
                                                                <div class="card h-100 module-card">
                                                                    <div class="card-body">
                                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                                            <h5 class="card-title mb-0">
                                                                                <?php echo htmlspecialchars($module['ModuleCode']); ?>:
                                                                                <?php echo htmlspecialchars($module['ModuleName']); ?>
                                                                            </h5>
                                                                            <span class="badge bg-primary">
                                                                                <?php echo (int)$module['Credits']; ?> Credits
                                                                            </span>
                                                                        </div>

                                                                        <?php if (!empty($module['ModuleLeader'])): ?>
                                                                            <p class="text-muted small mb-2">
                                                                                <i class="fas fa-user-tie me-1"></i>
                                                                                <?php echo htmlspecialchars($module['ModuleLeader']); ?>
                                                                                <?php if (!empty($module['LeaderTitle'])): ?>
                                                                                    <br>
                                                                                    <small><?php echo htmlspecialchars($module['LeaderTitle']); ?></small>
                                                                                <?php endif; ?>
                                                                            </p>
                                                                        <?php endif; ?>

                                                                        <div class="mb-2">
                                                                            <?php if (!empty($module['IsCore'])): ?>
                                                                                <span class="badge bg-success">Core Module</span>
                                                                            <?php else: ?>
                                                                                <span class="badge bg-secondary">Optional Module</span>
                                                                            <?php endif; ?>
                                                                        </div>

                                                                        <?php if (!empty($module['Description'])): ?>
                                                                            <p class="card-text">
                                                                                <?php echo htmlspecialchars($module['Description']); ?>
                                                                            </p>
                                                                        <?php endif; ?>

                                                                        <?php if (!empty($module['LearningOutcomes'])): ?>
                                                                            <div class="mt-3">
                                                                                <strong class="d-block mb-2">Learning Outcomes:</strong>
                                                                                <p class="small text-muted mb-0">
                                                                                    <?php echo nl2br(htmlspecialchars($module['LearningOutcomes'])); ?>
                                                                                </p>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endforeach; ?>

                                                <div class="text-muted text-end">
                                                    Total Credits: 
                                                    <?php
                                                    $yearCredits = 0;
                                                    foreach ($modules as $module) {
                                                        $yearCredits += (int)$module['Credits'];
                                                    }
                                                    echo $yearCredits;
                                                    ?>
                                                </div>
                                            </div>
                                            <?php $firstYear = false; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Programme Leader Bio -->
                        <?php if ($programmeData['LeaderBio']): ?>
                        <div class="card programme-info-card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-user me-2"></i>Programme Leader
                                </h3>
                            </div>
                            <div class="card-body">
                                <h5><?php echo htmlspecialchars($programmeData['ProgrammeLeader']); ?></h5>
                                <?php if ($programmeData['LeaderTitle']): ?>
                                    <p class="text-muted mb-3"><?php echo htmlspecialchars($programmeData['LeaderTitle']); ?></p>
                                <?php endif; ?>
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($programmeData['LeaderBio'])); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Quick Registration -->
                        <div class="card mb-4 border-primary">
                            <div class="card-header bg-primary text-white text-center">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-heart me-2"></i>Interested?
                                </h4>
                            </div>
                            <div class="card-body text-center">
                                <p class="mb-3">Register your interest to receive updates about:</p>
                                <ul class="list-unstyled text-start mb-3">
                                    <li><i class="fas fa-check text-success me-2"></i>Open days</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Application deadlines</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Programme updates</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Scholarship opportunities</li>
                                </ul>
                                <button type="button" class="btn btn-primary btn-lg w-100"
                                        data-bs-toggle="modal" data-bs-target="#interestModal">
                                    <i class="fas fa-heart me-2"></i>Register Interest
                                </button>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-bar me-2"></i>Quick Facts
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <div class="h3 text-primary mb-1"><?php echo $stats['module_count']; ?></div>
                                        <small class="text-muted">Modules</small>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="h3 text-success mb-1"><?php echo $stats['interested_students']; ?></div>
                                        <small class="text-muted">Interested Students</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Programmes -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-graduation-cap me-2"></i>Related Programmes
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $relatedProgrammes = $programme->getPublishedProgrammes($programmeData['LevelID']);
                                $relatedCount = 0;
                                ?>
                                <?php foreach ($relatedProgrammes as $related): ?>
                                    <?php if ($related['ProgrammeID'] != $programmeId && $relatedCount < 3): ?>
                                        <div class="mb-3">
                                            <h6 class="mb-1">
                                                <a href="programme.php?id=<?php echo $related['ProgrammeID']; ?>"
                                                   class="text-decoration-none">
                                                    <?php echo htmlspecialchars($related['ProgrammeName']); ?>
                                                </a>
                                            </h6>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(truncateText($related['Description'], 80)); ?>
                                            </small>
                                        </div>
                                        <?php $relatedCount++; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <?php if ($relatedCount === 0): ?>
                                    <p class="text-muted mb-0">No related programmes found.</p>
                                <?php else: ?>
                                    <a href="index.php?level=<?php echo $programmeData['LevelID']; ?>"
                                       class="btn btn-outline-primary btn-sm w-100">
                                        View All <?php echo htmlspecialchars($programmeData['LevelName']); ?> Programmes
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Programme Modules -->
        <section id="modules" class="py-5 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2 class="display-6 fw-bold text-center mb-5 text-black">Programme Modules</h2>

                        <?php if (empty($modulesByYear)): ?>
                            <div class="text-center">
                                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                <h4>Module information coming soon</h4>
                                <p class="text-muted">Detailed module information will be available shortly.</p>
                            </div>
                        <?php else: ?>
                            <!-- Year Tabs -->
                            <ul class="nav nav-tabs year-tabs justify-content-center mb-4 text-black" role="tablist">
                                <?php $firstYear = true; ?>
                                <?php foreach ($modulesByYear as $year => $modules): ?>
                                    <li class="nav-item text-black" role="presentation">
                                        <button class="nav-link <?php echo $firstYear ? 'active' : ''; ?> text-black"
                                                id="year-<?php echo $year; ?>-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#year-<?php echo $year; ?>"
                                                type="button"
                                                role="tab"
                                                aria-controls="year-<?php echo $year; ?>"
                                                aria-selected="<?php echo $firstYear ? 'true' : 'false'; ?>">
                                         <span class="text-black"> Year <?php echo $year; ?></span>
                                            <span class="badge bg-secondary ms-2"><?php echo count($modules); ?></span>
                                        </button>
                                    </li>
                                    <?php $firstYear = false; ?>
                                <?php endforeach; ?>
                            </ul>

                            <!-- Year Content -->
                            <div class="tab-content">
                                <?php $firstYear = true; ?>
                                <?php foreach ($modulesByYear as $year => $modules): ?>
                                    <div class="tab-pane fade <?php echo $firstYear ? 'show active' : ''; ?>"
                                         id="year-<?php echo $year; ?>"
                                         role="tabpanel"
                                         aria-labelledby="year-<?php echo $year; ?>-tab">

                                        <div class="row">
                                            <?php foreach ($modules as $module): ?>
                                                <div class="col-lg-6 mb-4">
                                                    <div class="card module-card h-100">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <h5 class="card-title mb-0">
                                                                    <?php echo htmlspecialchars($module['ModuleName']); ?>
                                                                </h5>
                                                                <div class="text-end">
                                                                    <span class="badge bg-primary"><?php echo htmlspecialchars($module['ModuleCode']); ?></span>
                                                                    <?php if ($module['IsCore']): ?>
                                                                        <span class="badge bg-success">Core</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-secondary">Optional</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>

                                                            <p class="card-text mb-3">
                                                                <?php echo htmlspecialchars($module['Description']); ?>
                                                            </p>

                                                            <?php if ($module['LearningOutcomes']): ?>
                                                                <p class="card-text small text-muted mb-3">
                                                                    <strong>Learning Outcomes:</strong>
                                                                    <?php echo htmlspecialchars(truncateText($module['LearningOutcomes'], 100)); ?>
                                                                </p>
                                                            <?php endif; ?>

                                                            <div class="module-meta">
                                                                <div class="row align-items-center">
                                                                    <div class="col">
                                                                        <?php if ($module['ModuleLeader']): ?>
                                                                            <div class="module-leader">
                                                                                <i class="fas fa-user me-1"></i>
                                                                                <strong><?php echo htmlspecialchars($module['ModuleLeader']); ?></strong>
                                                                                <?php if ($module['LeaderTitle']): ?>
                                                                                    <br><small><?php echo htmlspecialchars($module['LeaderTitle']); ?></small>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="col-auto">
                                                                        <div class="text-end">
                                                                            <small class="text-muted">Credits</small>
                                                                            <div class="fw-bold text-primary"><?php echo $module['Credits']; ?></div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <?php if ($module['Semester'] !== 'Full Year'): ?>
                                                                    <div class="mt-2">
                                                                        <small class="text-muted">
                                                                            <i class="fas fa-calendar me-1"></i>
                                                                            <?php echo htmlspecialchars($module['Semester']); ?>
                                                                        </small>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php $firstYear = false; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Interest Registration Modal -->
    <div class="modal fade" id="interestModal" tabindex="-1" aria-labelledby="interestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="interestModalLabel">Register Your Interest</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="interestForm" method="POST" action="register_interest.php" class="text-black">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="programme_id" value="<?php echo $programmeId; ?>">
                        <input type="hidden" name="redirect_to" value="programme">

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You're registering interest for: <strong><?php echo htmlspecialchars($programmeData['ProgrammeName']); ?></strong>
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
   <?php include 'includes/footer.php'; ?>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
