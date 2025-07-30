<?php
require_once '../config/config.php';

// Require admin login
Security::requireRole('Admin');


// Get dashboard statistics
try {
    // Total programmes
    $totalProgrammes = count($programme->getAllProgrammes());
    $publishedProgrammes = count($programme->getPublishedProgrammes());

    // Total interested students
    $totalInterested = $studentInterest->getInterestedStudentsCount();
    $recentInterested = $studentInterest->getInterestedStudentsCount();

    // Total modules
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM Modules WHERE IsActive = 1");
    $stmt->execute();
    $totalModules = $stmt->fetch()['count'];

    // Total staff
    $totalStaff = count($staff->getAllStaff());

    // Recent registrations (last 30 days)
    $stmt = $db->prepare("
        SELECT COUNT(*) as count
        FROM InterestedStudents
        WHERE RegisteredAt >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        AND IsSubscribed = 1
    ");
    $stmt->execute();
    $recentRegistrations = $stmt->fetch()['count'];

    // Popular programmes
    $stmt = $db->prepare("
        SELECT p.ProgrammeName, p.ProgrammeID, COUNT(i.InterestID) as interest_count
        FROM Programmes p
        LEFT JOIN InterestedStudents i ON p.ProgrammeID = i.ProgrammeID AND i.IsSubscribed = 1
        WHERE p.IsPublished = 1 AND p.IsActive = 1
        GROUP BY p.ProgrammeID, p.ProgrammeName
        ORDER BY interest_count DESC
        LIMIT 5
    ");
    $stmt->execute();
    $popularProgrammes = $stmt->fetchAll();

    // Recent interested students
    $recentStudents = $studentInterest->getAllInterestedStudents(null, 5);

    // Interest statistics by programme
    $interestStats = $studentInterest->getInterestStatistics();

} catch (Exception $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    // Set default values
    $totalProgrammes = $publishedProgrammes = $totalInterested = $totalModules = $totalStaff = 0;
    $recentRegistrations = 0;
    $popularProgrammes = $recentStudents = $interestStats = [];
}

$pageTitle = 'Admin Dashboard - ' . SITE_NAME;
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Admin Navigation -->
    <?php include 'includes/admin_nav.php'; ?>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/admin_sidebar.php'; ?>

            <!-- Main Content Area -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2 text-black">
                        <i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="programmes.php" class="btn btn-outline-primary">
                                <i class="fas fa-graduation-cap me-1"></i>Manage Programmes
                            </a>
                            <a href="students.php" class="btn btn-outline-success">
                                <i class="fas fa-users me-1"></i>View Students
                            </a>
                        </div>
                        <a href="../index.php" class="btn btn-outline-secondary" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i>View Site
                        </a>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php displayAlert(); ?>

                <!-- Overview Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white bg-primary h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="mb-1"><?php echo $totalProgrammes; ?></h4>
                                    <p class="mb-0">Total Programmes</p>
                                    <small class="opacity-75"><?php echo $publishedProgrammes; ?> published</small>
                                </div>
                                <div class="stats-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white bg-success h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="mb-1"><?php echo $totalInterested; ?></h4>
                                    <p class="mb-0">Interested Students</p>
                                    <small class="opacity-75">+<?php echo $recentRegistrations; ?> this month</small>
                                </div>
                                <div class="stats-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white bg-info h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="mb-1"><?php echo $totalModules; ?></h4>
                                    <p class="mb-0">Active Modules</p>
                                    <small class="opacity-75">Across all programmes</small>
                                </div>
                                <div class="stats-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white bg-warning h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="mb-1"><?php echo $totalStaff; ?></h4>
                                    <p class="mb-0">Staff Members</p>
                                    <small class="opacity-75">Programme & module leaders</small>
                                </div>
                                <div class="stats-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Analytics -->
                <div class="row mb-4">
                    <!-- Popular Programmes Chart -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-bar me-2 text-primary"></i>Most Popular Programmes
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($popularProgrammes)): ?>
                                    <canvas id="popularProgrammesChart" height="300"></canvas>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No data available yet</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-clock me-2 text-success"></i>Recent Student Interests
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($recentStudents)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th class="text-white">Student</th>
                                                    <th class="text-white">Programme</th>
                                                    <th class="text-white">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recentStudents as $student): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($student['StudentName']); ?></strong>
                                                            <br><small class="text-muted"><?php echo htmlspecialchars($student['Email']); ?></small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($student['LevelName']); ?></span>
                                                            <br><small><?php echo htmlspecialchars(truncateText($student['ProgrammeName'], 30)); ?></small>
                                                        </td>
                                                        <td>
                                                            <small><?php echo formatDate($student['RegisteredAt']); ?></small>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="interested_students.php" class="btn btn-outline-primary btn-sm">
                                            View All Interests
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No recent registrations</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Programme Interest Overview -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-line me-2 text-info"></i>Programme Interest Overview
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($interestStats)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-white">Programme</th>
                                                    <th class="text-white">Level</th>
                                                    <th class="text-center text-white">Total Interest</th>
                                                    <th class="text-center text-white">Recent (30 days)</th>
                                                    <th class="text-center text-white">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($interestStats as $stat): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($stat['ProgrammeName']); ?></strong>
                                                            <br><small class="text-muted"><?php echo htmlspecialchars($stat['ProgrammeCode']); ?></small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary">
                                                                <?php echo htmlspecialchars($stat['LevelName']); ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <h5 class="text-primary mb-0"><?php echo $stat['interest_count']; ?></h5>
                                                        </td>
                                                        <td class="text-center">
                                                            <h6 class="text-success mb-0"><?php echo $stat['recent_count']; ?></h6>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="students.php?programme_id=<?php echo urlencode($stat['ProgrammeCode']); ?>"
                                                                   class="btn btn-outline-primary btn-sm"
                                                                   title="View Students">
                                                                    <i class="fas fa-users"></i>
                                                                </a>
                                                                <a href="export.php?type=mailing_list&programme_id=<?php echo urlencode($stat['ProgrammeCode']); ?>"
                                                                   class="btn btn-outline-success btn-sm"
                                                                   title="Export Mailing List">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No interest data available yet</p>
                                        <a href="programmes.php" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>Add First Programme
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bolt me-2 text-warning"></i>Quick Actions
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <a href="programmes.php?action=add" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center">
                                            <i class="fas fa-plus fa-2x mb-2"></i>
                                            <span>Add New Programme</span>
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="modules.php?action=add" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center">
                                            <i class="fas fa-book fa-2x mb-2"></i>
                                            <span>Add New Module</span>
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="staff.php?action=add" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center">
                                            <i class="fas fa-user-plus fa-2x mb-2"></i>
                                            <span>Add Staff Member</span>
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="export.php" class="btn btn-outline-warning w-100 h-100 d-flex flex-column justify-content-center">
                                            <i class="fas fa-download fa-2x mb-2"></i>
                                            <span>Export Data</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Popular Programmes Chart
            <?php if (!empty($popularProgrammes)): ?>
                const ctx = document.getElementById('popularProgrammesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [
                            <?php foreach ($popularProgrammes as $prog): ?>
                                '<?php echo addslashes(truncateText($prog['ProgrammeName'], 20)); ?>',
                            <?php endforeach; ?>
                        ],
                        datasets: [{
                            label: 'Interested Students',
                            data: [
                                <?php foreach ($popularProgrammes as $prog): ?>
                                    <?php echo $prog['interest_count']; ?>,
                                <?php endforeach; ?>
                            ],
                            backgroundColor: [
                                'rgba(37, 99, 235, 0.8)',
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(139, 92, 246, 0.8)'
                            ],
                            borderColor: [
                                'rgba(37, 99, 235, 1)',
                                'rgba(16, 185, 129, 1)',
                                'rgba(245, 158, 11, 1)',
                                'rgba(239, 68, 68, 1)',
                                'rgba(139, 92, 246, 1)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            <?php endif; ?>

            // Auto-refresh dashboard data every 5 minutes
            setInterval(function() {
                // You could implement AJAX refresh here
                // location.reload();
            }, 300000); // 5 minutes
        });
    </script>
</body>
</html>
