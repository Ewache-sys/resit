<?php
require_once __DIR__ . '/../config/config.php';

// Require admin privileges
Security::requireRole('Admin');

// Initialize classes
$programme = new Programme($db);
$level = new Level($db);
$staff = new Staff($db);
$module = new Module($db);

// Handle form submission
$error = '';
$success = '';
$programmeId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        // Prepare programme data
        $programmeData = [
            'code' => sanitize($_POST['code']),
            'name' => sanitize($_POST['name']),
            'level_id' => (int)$_POST['level_id'],
            'leader_id' => $_POST['leader_id'] ?: null,
            'description' => sanitize($_POST['description']),
            'entry_requirements' => sanitize($_POST['entry_requirements']),
            'career_prospects' => sanitize($_POST['career_prospects']),
            'duration' => sanitize($_POST['duration']),
            'is_published' => isset($_POST['is_published']),
            'image' => null
        ];

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadImage($_FILES['image'], 'uploads/programmes');
            if ($uploadResult['success']) {
                $programmeData['image'] = $uploadResult['path'];
            } else {
                $error = $uploadResult['message'];
            }
        }

        if (empty($error)) {
            $programmeId = $programme->createProgramme($programmeData);
            if ($programmeId) {
                // Handle module assignments if provided
                if (isset($_POST['modules']) && is_array($_POST['modules'])) {
                    foreach ($_POST['modules'] as $moduleData) {
                        $stmt = $db->prepare("
                            INSERT INTO ProgrammeModules (
                                ProgrammeID, ModuleID, Year, Semester, IsCore
                            ) VALUES (?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([
                            $programmeId,
                            $moduleData['module_id'],
                            $moduleData['year'],
                            $moduleData['semester'],
                            $moduleData['is_core'] ?? true
                        ]);
                    }
                }
                $success = 'Programme created successfully.';
                
                // Log activity
                $security->logActivity(
                    $_SESSION['user_id'],
                    'Create',
                    'Programmes',
                    $programmeId,
                    null,
                    $programmeData
                );
            } else {
                $error = 'Failed to create programme.';
            }
        }
    }
}

// Get all levels for dropdown
$levels = $level->getAllLevels();

// Get all staff for programme leader selection
$allStaff = $staff->getAllStaff();

// Get all modules for module selection
$allModules = $module->getAllModules();

$pageTitle = 'Add New Programme - ' . SITE_NAME;
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
    
    <style>
        .module-year {
            border-left: 3px solid var(--bs-primary);
            margin-bottom: 1rem;
            padding-left: 1rem;
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body class="admin-body">
    <?php include 'includes/admin_nav.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/admin_sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1>Add New Programme</h1>
                    <a href="programmes.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Programmes
                    </a>
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

                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="programmeForm">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                            <!-- Basic Information -->
                            <h5 class="card-title mb-4">Basic Information</h5>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Programme Code</label>
                                    <input type="text" class="form-control" name="code" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Programme Name</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Level</label>
                                    <select class="form-select" name="level_id" required>
                                        <option value="">Select Level</option>
                                        <?php foreach ($levels as $lvl): ?>
                                            <option value="<?php echo $lvl['LevelID']; ?>">
                                                <?php echo htmlspecialchars($lvl['LevelName']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Programme Leader</label>
                                    <select class="form-select" name="leader_id">
                                        <option value="">Select Programme Leader</option>
                                        <?php foreach ($allStaff as $s): ?>
                                            <option value="<?php echo $s['StaffID']; ?>">
                                                <?php echo htmlspecialchars($s['Name']); ?> 
                                                (<?php echo htmlspecialchars($s['Title']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Entry Requirements</label>
                                <textarea class="form-control" name="entry_requirements" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Career Prospects</label>
                                <textarea class="form-control" name="career_prospects" rows="4" required></textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Duration</label>
                                    <input type="text" class="form-control" name="duration" 
                                           placeholder="e.g., 3 years full-time" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Programme Image</label>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                    <div id="imagePreview" class="mt-2"></div>
                                </div>
                            </div>

                            <!-- Module Assignment -->
                            <h5 class="card-title mb-4 mt-5">Module Assignment</h5>
                            <div id="moduleAssignment">
                                <div class="module-year" data-year="1">
                                    <h6>Year 1</h6>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <select class="form-select module-select" name="modules[0][module_id]">
                                                <option value="">Select Module</option>
                                                <?php foreach ($allModules as $mod): ?>
                                                    <option value="<?php echo $mod['ModuleID']; ?>">
                                                        <?php echo htmlspecialchars($mod['ModuleCode'] . ': ' . $mod['ModuleName']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-select" name="modules[0][semester]">
                                                <option value="Semester 1">Semester 1</option>
                                                <option value="Semester 2">Semester 2</option>
                                                <option value="Full Year">Full Year</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="modules[0][is_core]" checked>
                                                <label class="form-check-label">Core Module</label>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-sm remove-module">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm add-module" data-year="1">
                                        <i class="fas fa-plus me-2"></i>Add Module
                                    </button>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-success mb-4" id="addYear">
                                <i class="fas fa-plus me-2"></i>Add Year
                            </button>

                            <hr>

                            <!-- Publishing Options -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_published" id="isPublished">
                                <label class="form-check-label" for="isPublished">
                                    Publish programme immediately
                                </label>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="programmes.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Programme
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview
            const imageInput = document.querySelector('input[name="image"]');
            const imagePreview = document.getElementById('imagePreview');
            
            imageInput.addEventListener('change', function() {
                imagePreview.innerHTML = '';
                if (this.files && this.files[0]) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(this.files[0]);
                    img.className = 'preview-image';
                    imagePreview.appendChild(img);
                }
            });

            // Module management
            let moduleCounter = 1;
            
            // Add module to year
            document.addEventListener('click', function(e) {
                if (e.target.matches('.add-module') || e.target.closest('.add-module')) {
                    const button = e.target.matches('.add-module') ? e.target : e.target.closest('.add-module');
                    const year = button.dataset.year;
                    const yearDiv = button.closest('.module-year');
                    const moduleRow = createModuleRow(year, moduleCounter++);
                    yearDiv.insertBefore(moduleRow, button);
                }
            });

            // Remove module
            document.addEventListener('click', function(e) {
                if (e.target.matches('.remove-module') || e.target.closest('.remove-module')) {
                    const button = e.target.matches('.remove-module') ? e.target : e.target.closest('.remove-module');
                    button.closest('.row').remove();
                }
            });

            // Add new year
            document.getElementById('addYear').addEventListener('click', function() {
                const moduleAssignment = document.getElementById('moduleAssignment');
                const yearCount = moduleAssignment.children.length + 1;
                
                const yearDiv = document.createElement('div');
                yearDiv.className = 'module-year';
                yearDiv.dataset.year = yearCount;
                yearDiv.innerHTML = `
                    <h6>Year ${yearCount}</h6>
                    ${createModuleRow(yearCount, moduleCounter++).outerHTML}
                    <button type="button" class="btn btn-outline-primary btn-sm add-module" data-year="${yearCount}">
                        <i class="fas fa-plus me-2"></i>Add Module
                    </button>
                `;
                
                moduleAssignment.appendChild(yearDiv);
            });

            // Create module row HTML
            function createModuleRow(year, counter) {
                const div = document.createElement('div');
                div.className = 'row mb-3';
                div.innerHTML = `
                    <div class="col-md-6">
                        <select class="form-select module-select" name="modules[${counter}][module_id]" required>
                            <option value="">Select Module</option>
                            <?php foreach ($allModules as $mod): ?>
                                <option value="<?php echo $mod['ModuleID']; ?>">
                                    <?php echo htmlspecialchars($mod['ModuleCode'] . ': ' . $mod['ModuleName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="modules[${counter}][semester]">
                            <option value="Semester 1">Semester 1</option>
                            <option value="Semester 2">Semester 2</option>
                            <option value="Full Year">Full Year</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="modules[${counter}][is_core]" checked>
                            <label class="form-check-label">Core Module</label>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-module">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <input type="hidden" name="modules[${counter}][year]" value="${year}">
                `;
                return div;
            }

            // Form validation
            document.getElementById('programmeForm').addEventListener('submit', function(e) {
                const moduleSelects = document.querySelectorAll('.module-select');
                const selectedModules = new Set();
                let hasDuplicates = false;

                moduleSelects.forEach(select => {
                    const value = select.value;
                    if (value) {
                        if (selectedModules.has(value)) {
                            hasDuplicates = true;
                        }
                        selectedModules.add(value);
                    }
                });

                if (hasDuplicates) {
                    e.preventDefault();
                    alert('Each module can only be assigned once to a programme.');
                }
            });
        });
    </script>
</body>
</html> 