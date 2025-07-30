<?php
require_once __DIR__ . '/../config/config.php';

// Require admin privileges
Security::requireRole('Admin');

// Initialize classes
$studentInterest = new StudentInterest($db);
$programme = new Programme($db);

// Handle actions
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'remove':
                $interestId = (int)$_POST['interest_id'];
                if ($studentInterest->removeInterest($interestId)) {
                    $success = 'Student interest record removed successfully.';
                } else {
                    $error = 'Failed to remove student interest record.';
                }
                break;

            case 'export':
                $format = $_POST['format'] ?? 'csv';
                $programmeId = !empty($_POST['programme_id']) ? (int)$_POST['programme_id'] : null;
                
                $data = $studentInterest->exportMailingList($programmeId, $format);
                
                if ($data) {
                    $filename = 'interested_students_' . date('Y-m-d');
                    if ($format === 'csv') {
                        header('Content-Type: text/csv');
                        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
                        echo $data;
                        exit;
                    } elseif ($format === 'json') {
                        header('Content-Type: application/json');
                        header('Content-Disposition: attachment; filename="' . $filename . '.json"');
                        echo $data;
                        exit;
                    }
                } else {
                    $error = 'Failed to export data.';
                }
                break;
        }
    }
}

// Get filter parameters
$programmeId = isset($_GET['programme']) ? (int)$_GET['programme'] : null;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;

// Get total count for pagination
$totalStudents = $studentInterest->getInterestedStudentsCount($programmeId);
$totalPages = ceil($totalStudents / $perPage);
$offset = ($page - 1) * $perPage;

// Get students with pagination
$students = $studentInterest->getAllInterestedStudents($programmeId, $perPage, $offset);

// Get all programmes for filter
$programmes = $programme->getAllProgrammes();

// Get interest statistics
$statistics = $studentInterest->getInterestStatistics();

$pageTitle = 'Student Interests - ' . SITE_NAME;
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
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="admin-body">
    <?php include 'includes/admin_nav.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/admin_sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="text-black">Student Interests</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="fas fa-download me-2"></i>Export Data
                        </button>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Interests</h5>
                                <h2 class="card-text"><?php echo $totalStudents; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Top Programmes</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th class="text-white">Programme</th>
                                                <th class="text-white">Level</th>
                                                <th class="text-white">Total</th>
                                                <th class="text-white">Last 30 Days</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_slice($statistics, 0, 5) as $stat): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($stat['ProgrammeName']); ?></td>
                                                    <td><?php echo htmlspecialchars($stat['LevelName']); ?></td>
                                                    <td><?php echo $stat['interest_count']; ?></td>
                                                    <td><?php echo $stat['recent_count']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Filter by Programme</label>
                                <select name="programme" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Programmes</option>
                                    <?php foreach ($programmes as $prog): ?>
                                        <option value="<?php echo $prog['ProgrammeID']; ?>"
                                                <?php echo $programmeId == $prog['ProgrammeID'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($prog['ProgrammeName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-white">Name</th>
                                        <th class="text-white">Email</th>
                                        <th class="text-white">Programme</th>
                                        <th class="text-white">Country</th>
                                        <th class="text-white">Registered</th>
                                        <th class="text-white">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($student['StudentName']); ?></td>
                                            <td><?php echo htmlspecialchars($student['Email']); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($student['ProgrammeName']); ?>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($student['LevelName']); ?>
                                                </small>
                                            </td>
                                            <td><?php echo htmlspecialchars($student['Country'] ?? 'N/A'); ?></td>
                                            <td>
                                                <?php echo formatDate($student['RegisteredAt']); ?>
                                            </td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-sm btn-info view-details"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#detailsModal"
                                                        data-student='<?php echo htmlspecialchars(json_encode($student)); ?>'>
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-danger remove-interest"
                                                        data-interest-id="<?php echo $student['InterestID']; ?>"
                                                        data-student-name="<?php echo htmlspecialchars($student['StudentName']); ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&programme=<?php echo $programmeId; ?>">
                                            Previous
                                        </a>
                                    </li>
                                    
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&programme=<?php echo $programmeId; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&programme=<?php echo $programmeId; ?>">
                                            Next
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Export Modal -->
                <div class="modal fade" id="exportModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Export Data</h5>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-dark">
                                <form method="POST" id="exportForm">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="export">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Programme</label>
                                        <select name="programme_id" class="form-select">
                                            <option value="">All Programmes</option>
                                            <?php foreach ($programmes as $prog): ?>
                                                <option value="<?php echo $prog['ProgrammeID']; ?>">
                                                    <?php echo htmlspecialchars($prog['ProgrammeName']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Format</label>
                                        <select name="format" class="form-select">
                                            <option value="csv">CSV</option>
                                            <option value="json">JSON</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" form="exportForm" class="btn btn-primary">Export</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details Modal -->
                <div class="modal fade" id="detailsModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Student Details</h5>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-dark">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Personal Information</h6>
                                        <p><strong>Name:</strong> <span id="detailName"></span></p>
                                        <p><strong>Email:</strong> <span id="detailEmail"></span></p>
                                        <p><strong>Phone:</strong> <span id="detailPhone"></span></p>
                                        <p><strong>Country:</strong> <span id="detailCountry"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Programme Information</h6>
                                        <p><strong>Programme:</strong> <span id="detailProgramme"></span></p>
                                        <p><strong>Level:</strong> <span id="detailLevel"></span></p>
                                        <p><strong>Current Education:</strong> <span id="detailEducation"></span></p>
                                        <p><strong>Registered:</strong> <span id="detailDate"></span></p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>Message to University</h6>
                                        <p id="detailMessage"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remove Confirmation Modal -->
                <div class="modal fade" id="removeModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirm Remove</h5>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-dark">
                                Are you sure you want to remove this student's interest record?
                            </div>
                            <div class="modal-footer">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="interest_id" value="">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle view details
            document.querySelectorAll('.view-details').forEach(button => {
                button.addEventListener('click', function() {
                    const student = JSON.parse(this.dataset.student);
                    document.getElementById('detailName').textContent = student.StudentName;
                    document.getElementById('detailEmail').textContent = student.Email;
                    document.getElementById('detailPhone').textContent = student.Phone || 'N/A';
                    document.getElementById('detailCountry').textContent = student.Country || 'N/A';
                    document.getElementById('detailProgramme').textContent = student.ProgrammeName;
                    document.getElementById('detailLevel').textContent = student.LevelName;
                    document.getElementById('detailEducation').textContent = student.CurrentEducation || 'N/A';
                    document.getElementById('detailDate').textContent = new Date(student.RegisteredAt).toLocaleDateString();
                    document.getElementById('detailMessage').textContent = student.MessageToUniversity || 'No message';
                });
            });

            // Handle remove interest
            document.querySelectorAll('.remove-interest').forEach(button => {
                button.addEventListener('click', function() {
                    const interestId = this.dataset.interestId;
                    const studentName = this.dataset.studentName;
                    const modal = new bootstrap.Modal(document.getElementById('removeModal'));
                    document.querySelector('#removeModal .modal-body').textContent = 
                        `Are you sure you want to remove the interest record for ${studentName}?`;
                    document.querySelector('#removeModal input[name="interest_id"]').value = interestId;
                    modal.show();
                });
            });
        });
    </script>
</body>
</html> 