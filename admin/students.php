<?php
require_once __DIR__ . '/../config/config.php';

// Require admin privileges
Security::requireRole('Admin');

// Initialize classes
$student = new Student($db);
$programme = new Programme($db);

// Handle form submissions
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create':
            case 'update':
                $studentData = [
                    'student_number' => sanitize($_POST['student_number']),
                    'first_name' => sanitize($_POST['first_name']),
                    'last_name' => sanitize($_POST['last_name']),
                    'email' => sanitize($_POST['email']),
                    'phone' => sanitize($_POST['phone']),
                    'date_of_birth' => $_POST['date_of_birth'],
                    'address' => sanitize($_POST['address']),
                    'city' => sanitize($_POST['city']),
                    'country' => sanitize($_POST['country']),
                    'postal_code' => sanitize($_POST['postal_code']),
                    'programme_id' => $_POST['programme_id'] ?: null,
                    'enrollment_date' => $_POST['enrollment_date'],
                    'status' => $_POST['status']
                ];

                if ($action === 'create') {
                    // Generate student number if not provided
                    if (empty($studentData['student_number'])) {
                        $studentData['student_number'] = $student->generateStudentNumber();
                    }
                    
                    if ($student->createStudent($studentData)) {
                        $success = 'Student created successfully.';
                    } else {
                        $error = 'Failed to create student.';
                    }
                } else {
                    $studentId = (int)$_POST['student_id'];
                    if ($student->updateStudent($studentId, $studentData)) {
                        $success = 'Student updated successfully.';
                    } else {
                        $error = 'Failed to update student.';
                    }
                }
                break;

            case 'delete':
                $studentId = (int)$_POST['student_id'];
                if ($student->deleteStudent($studentId)) {
                    $success = 'Student deleted successfully.';
                } else {
                    $error = 'Failed to delete student.';
                }
                break;
        }
    }
}

// Get filter parameters
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;

// Get total count for pagination
$totalStudents = $student->getStudentCount($search);
$totalPages = ceil($totalStudents / $perPage);
$offset = ($page - 1) * $perPage;

// Get students with pagination
$students = $student->getAllStudents($search, $perPage, $offset);

// Get all programmes for dropdown
$programmes = $programme->getAllProgrammes();

// Get student statistics
$statistics = $student->getStudentStatistics();

$pageTitle = 'Manage Students - ' . SITE_NAME;
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
                    <h1>Manage Students</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#studentModal">
                        <i class="fas fa-plus me-2"></i>Add New Student
                    </button>
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
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Students</h5>
                                <h2 class="card-text"><?php echo $statistics['total']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Students by Status</h5>
                                <div class="row">
                                    <?php foreach ($statistics['by_status'] as $status): ?>
                                        <div class="col-sm-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><?php echo htmlspecialchars($status['Status']); ?></span>
                                                <span class="badge bg-primary"><?php echo $status['count']; ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           name="search" 
                                           value="<?php echo htmlspecialchars($search); ?>"
                                           placeholder="Search by name, email, or student number...">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <?php if ($search): ?>
                                <div class="col-md-4">
                                    <a href="students.php" class="btn btn-outline-secondary">
                                        Clear Search
                                    </a>
                                </div>
                            <?php endif; ?>
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
                                        <th>Student Number</th>
                                        <th>Name</th>
                                        <th>Programme</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Enrolled</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $s): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($s['StudentNumber']); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($s['FirstName'] . ' ' . $s['LastName']); ?>
                                            </td>
                                            <td>
                                                <?php if ($s['ProgrammeName']): ?>
                                                    <?php echo htmlspecialchars($s['ProgrammeName']); ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($s['LevelName']); ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">Not assigned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($s['Email']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $s['Status'] === 'Active' ? 'success' : 'secondary'; ?>">
                                                    <?php echo htmlspecialchars($s['Status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatDate($s['EnrollmentDate']); ?></td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-sm btn-info view-student"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#studentModal"
                                                        data-student-id="<?php echo $s['StudentID']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-danger delete-student"
                                                        data-student-id="<?php echo $s['StudentID']; ?>"
                                                        data-student-name="<?php echo htmlspecialchars($s['FirstName'] . ' ' . $s['LastName']); ?>">
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
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">
                                            Previous
                                        </a>
                                    </li>
                                    
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">
                                            Next
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Student Modal -->
                <div class="modal fade" id="studentModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add/Edit Student</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="studentForm" method="POST" class="text-black">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="create">
                                    <input type="hidden" name="student_id" value="">

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="first_name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="last_name" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <input type="tel" class="form-control" name="phone">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Student Number</label>
                                            <input type="text" class="form-control" name="student_number" 
                                                   placeholder="Leave blank to auto-generate">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Date of Birth</label>
                                            <input type="date" class="form-control" name="date_of_birth">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address">
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="city">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Country</label>
                                            <input type="text" class="form-control" name="country">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Postal Code</label>
                                            <input type="text" class="form-control" name="postal_code">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Programme</label>
                                            <select class="form-select" name="programme_id">
                                                <option value="">Select Programme</option>
                                                <?php foreach ($programmes as $prog): ?>
                                                    <option value="<?php echo $prog['ProgrammeID']; ?>">
                                                        <?php echo htmlspecialchars($prog['ProgrammeName']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Enrollment Date</label>
                                            <input type="date" class="form-control" name="enrollment_date" 
                                                   value="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status" required>
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Inactive</option>
                                                <option value="Graduated">Graduated</option>
                                                <option value="Withdrawn">Withdrawn</option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" form="studentForm" class="btn btn-primary">Save Student</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this student?
                            </div>
                            <div class="modal-footer">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="student_id" value="">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Delete Student</button>
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
            // Handle edit student
            const studentModal = document.getElementById('studentModal');
            studentModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const isEdit = button.classList.contains('view-student');
                const form = this.querySelector('#studentForm');
                const title = this.querySelector('.modal-title');
                
                if (isEdit) {
                    const studentId = button.dataset.studentId;
                    title.textContent = 'Edit Student';
                    form.action.value = 'update';
                    form.student_id.value = studentId;
                    
                    // Fetch student data and populate form
                    fetch(`get_student.php?id=${studentId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const student = data.data;
                                form.first_name.value = student.FirstName;
                                form.last_name.value = student.LastName;
                                form.email.value = student.Email;
                                form.phone.value = student.Phone || '';
                                form.student_number.value = student.StudentNumber;
                                form.date_of_birth.value = student.DateOfBirth || '';
                                form.address.value = student.Address || '';
                                form.city.value = student.City || '';
                                form.country.value = student.Country || '';
                                form.postal_code.value = student.PostalCode || '';
                                form.programme_id.value = student.ProgrammeID || '';
                                form.enrollment_date.value = student.EnrollmentDate;
                                form.status.value = student.Status;
                            }
                        });
                } else {
                    title.textContent = 'Add New Student';
                    form.action.value = 'create';
                    form.student_id.value = '';
                    form.reset();
                    form.enrollment_date.value = new Date().toISOString().split('T')[0];
                }
            });

            // Handle delete student
            document.querySelectorAll('.delete-student').forEach(button => {
                button.addEventListener('click', function() {
                    const studentId = this.dataset.studentId;
                    const studentName = this.dataset.studentName;
                    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    const modalBody = document.querySelector('#deleteModal .modal-body');
                    const studentIdInput = document.querySelector('#deleteModal input[name="student_id"]');
                    
                    modalBody.textContent = `Are you sure you want to delete ${studentName}?`;
                    studentIdInput.value = studentId;
                    modal.show();
                });
            });
        });
    </script>
</body>
</html> 