<?php
require_once '../config/config.php';

// Require admin login
Security::requireRole('Admin');

// Initialize Module class
$module = new Module($db);

// Get programme ID from URL if editing
$programmeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEditing = $programmeId > 0;

// Get programme data if editing
$programmeData = null;
if ($isEditing) {
    $programmeData = $programme->getProgrammeById($programmeId);
    if (!$programmeData) {
        showAlert('Programme not found.', 'danger');
        redirectTo('programmes.php');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
        showAlert('Invalid request. Please try again.', 'danger');
        redirectTo('programmes.php');
    }

    // Prepare programme data
    $data = [
        'code' => $_POST['code'] ?? '',
        'name' => $_POST['name'] ?? '',
        'level_id' => (int)($_POST['level_id'] ?? 0),
        'leader_id' => (int)($_POST['leader_id'] ?? 0),
        'description' => $_POST['description'] ?? '',
        'entry_requirements' => $_POST['entry_requirements'] ?? '',
        'career_prospects' => $_POST['career_prospects'] ?? '',
        'duration' => $_POST['duration'] ?? '',
        'is_published' => isset($_POST['is_published']),
    ];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/programmes/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileInfo = pathinfo($_FILES['image']['name']);
        $extension = strtolower($fileInfo['extension']);
        
        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($extension, $allowedTypes)) {
            showAlert('Invalid file type. Please upload an image.', 'danger');
            redirectTo('programme_form.php' . ($isEditing ? "?id=$programmeId" : ''));
        }

        // Generate unique filename
        $filename = uniqid('prog_') . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $data['image'] = 'uploads/programmes/' . $filename;

            // Delete old image if exists
            if ($isEditing && $programmeData['Image']) {
                $oldImage = '../' . $programmeData['Image'];
                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }
        }
    }

    // Create or update programme
    if ($isEditing) {
        $result = $programme->updateProgramme($programmeId, $data);
        $message = 'Programme updated successfully.';
        $action = 'Update Programme';
    } else {
        $result = $programme->createProgramme($data);
        $message = 'Programme created successfully.';
        $action = 'Create Programme';
    }

    if ($result) {
        // Log activity
        $security->logActivity(
            $_SESSION['user_id'],
            $action,
            'Programmes',
            $isEditing ? $programmeId : $result
        );
        showAlert($message, 'success');
        redirectTo('programmes.php');
    } else {
        showAlert('Failed to save programme.', 'danger');
    }
}

// Get all levels for dropdown
$levels = $level->getAllLevels();

// Get all staff for programme leader dropdown
$allStaff = $staff->getAllStaff();

$pageTitle = ($isEditing ? 'Edit' : 'Add') . ' Programme - Admin - ' . SITE_NAME;
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
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Admin Navigation -->
    <?php include 'includes/admin_nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/admin_sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="text-black"><?php echo $isEditing ? 'Edit' : 'Add'; ?> Programme</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="programmes.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Programmes
                        </a>
                    </div>
                </div>

                <?php displayAlert(); ?>

                <div class="card">
                    <div class="card-body">
                        <form action="<?php echo $_SERVER['PHP_SELF'] . ($isEditing ? "?id=$programmeId" : ''); ?>"
                              method="POST"
                              enctype="multipart/form-data"
                              class="needs-validation"
                              novalidate>
                            
                            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="code" class="form-label">Programme Code <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control"
                                           id="code"
                                           name="code"
                                           value="<?php echo htmlspecialchars($programmeData['ProgrammeCode'] ?? ''); ?>"
                                           required>
                                    <div class="invalid-feedback">Please enter a programme code.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="name" class="form-label">Programme Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control"
                                           id="name"
                                           name="name"
                                           value="<?php echo htmlspecialchars($programmeData['ProgrammeName'] ?? ''); ?>"
                                           required>
                                    <div class="invalid-feedback">Please enter a programme name.</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="level_id" class="form-label">Level <span class="text-danger">*</span></label>
                                    <select class="form-select" id="level_id" name="level_id" required>
                                        <option value="">Select Level</option>
                                        <?php foreach ($levels as $level): ?>
                                            <option value="<?php echo $level['LevelID']; ?>"
                                                    <?php echo ($programmeData['LevelID'] ?? '') == $level['LevelID'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($level['LevelName']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a level.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="leader_id" class="form-label">Programme Leader</label>
                                    <select class="form-select" id="leader_id" name="leader_id">
                                        <option value="">Select Programme Leader</option>
                                        <?php foreach ($allStaff as $staffMember): ?>
                                            <option value="<?php echo $staffMember['StaffID']; ?>"
                                                    <?php echo ($programmeData['ProgrammeLeaderID'] ?? '') == $staffMember['StaffID'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($staffMember['Name']); ?>
                                                <?php if ($staffMember['Title']): ?>
                                                    (<?php echo htmlspecialchars($staffMember['Title']); ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control"
                                          id="description"
                                          name="description"
                                          rows="4"
                                          required><?php echo htmlspecialchars($programmeData['Description'] ?? ''); ?></textarea>
                                <div class="invalid-feedback">Please enter a description.</div>
                            </div>

                            <div class="mb-3">
                                <label for="entry_requirements" class="form-label">Entry Requirements</label>
                                <textarea class="form-control"
                                          id="entry_requirements"
                                          name="entry_requirements"
                                          rows="3"><?php echo htmlspecialchars($programmeData['EntryRequirements'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="career_prospects" class="form-label">Career Prospects</label>
                                <textarea class="form-control"
                                          id="career_prospects"
                                          name="career_prospects"
                                          rows="3"><?php echo htmlspecialchars($programmeData['CareerProspects'] ?? ''); ?></textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="duration" class="form-label">Duration</label>
                                    <input type="text"
                                           class="form-control"
                                           id="duration"
                                           name="duration"
                                           value="<?php echo htmlspecialchars($programmeData['Duration'] ?? ''); ?>"
                                           placeholder="e.g., 3 years full-time">
                                </div>

                                <div class="col-md-6">
                                    <label for="image" class="form-label">Programme Image</label>
                                    <input type="file"
                                           class="form-control"
                                           id="image"
                                           name="image"
                                           accept="image/*">
                                    <?php if (isset($programmeData['Image']) && $programmeData['Image']): ?>
                                        <div class="mt-2">
                                            <img src="../<?php echo htmlspecialchars($programmeData['Image']); ?>"
                                                 alt="Current programme image"
                                                 class="img-thumbnail"
                                                 style="max-height: 100px;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           id="is_published"
                                           name="is_published"
                                           <?php echo ($programmeData['IsPublished'] ?? false) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_published">
                                        Publish Programme
                                    </label>
                                </div>
                            </div>

                            <!-- Programme Modules Section -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Programme Modules</h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // Get all available modules
                                    $allModules = $module->getAllModules();
                                    
                                    // Get current programme modules if editing
                                    $programmeModules = [];
                                    if ($isEditing) {
                                        $programmeModules = $programme->getProgrammeModules($programmeId);
                                    }

                                    // Group modules by year
                                    $years = range(1, 4); // Assuming maximum 4 years
                                    ?>

                                    <div class="accordion" id="modulesAccordion">
                                        <?php foreach ($years as $year): ?>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button <?php echo $year > 1 ? 'collapsed' : ''; ?>"
                                                            type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#year<?php echo $year; ?>Modules">
                                                        Year <?php echo $year; ?> Modules
                                                    </button>
                                                </h2>
                                                <div id="year<?php echo $year; ?>Modules"
                                                     class="accordion-collapse collapse <?php echo $year === 1 ? 'show' : ''; ?>">
                                                    <div class="accordion-body">
                                                        <div class="row mb-3">
                                                            <div class="col">
                                                                <select class="form-select module-select"
                                                                        data-year="<?php echo $year; ?>"
                                                                        data-semester="1">
                                                                    <option value="">Add Semester 1 Module...</option>
                                                                    <?php foreach ($allModules as $mod): ?>
                                                                        <option value="<?php echo $mod['ModuleID']; ?>"
                                                                                data-credits="<?php echo $mod['Credits']; ?>">
                                                                            <?php echo htmlspecialchars($mod['ModuleCode'] . ' - ' . $mod['ModuleName']); ?>
                                                                            (<?php echo $mod['Credits']; ?> credits)
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col">
                                                                <select class="form-select module-select"
                                                                        data-year="<?php echo $year; ?>"
                                                                        data-semester="2">
                                                                    <option value="">Add Semester 2 Module...</option>
                                                                    <?php foreach ($allModules as $mod): ?>
                                                                        <option value="<?php echo $mod['ModuleID']; ?>"
                                                                                data-credits="<?php echo $mod['Credits']; ?>">
                                                                            <?php echo htmlspecialchars($mod['ModuleCode'] . ' - ' . $mod['ModuleName']); ?>
                                                                            (<?php echo $mod['Credits']; ?> credits)
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Selected Modules List -->
                                        <div class="selected-modules" data-year="<?php echo $year; ?>">
                                            <?php
                                            if ($isEditing && !empty($programmeModules)) {
                                                $yearModules = array_filter($programmeModules, function($mod) use ($year) {
                                                    return isset($mod['Year']) && $mod['Year'] == $year;
                                                });
                                                foreach ($yearModules as $mod):
                                            ?>
                                                <div class="card mb-2 module-item">
                                                    <div class="card-body py-2">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <input type="hidden"
                                                                       name="modules[<?php echo $year; ?>][]"
                                                                       value="<?php echo $mod['ModuleID']; ?>">
                                                                <input type="hidden"
                                                                       name="module_years[<?php echo $mod['ModuleID']; ?>]"
                                                                       value="<?php echo $year; ?>">
                                                                <input type="hidden"
                                                                       name="module_semesters[<?php echo $mod['ModuleID']; ?>]"
                                                                       value="<?php echo $mod['Semester']; ?>">
                                                                <strong><?php echo htmlspecialchars($mod['ModuleCode']); ?></strong>
                                                                <?php echo htmlspecialchars($mod['ModuleName']); ?>
                                                                <br>
                                                                <small class="text-muted">
                                                                    <?php echo $mod['Credits']; ?> credits
                                                                    | Semester <?php echo $mod['Semester']; ?>
                                                                    | <?php echo $mod['IsCore'] ? 'Core' : 'Optional'; ?>
                                                                </small>
                                                            </div>
                                                            <div class="col-auto">
                                                                <div class="form-check form-check-inline">
                                                                    <input type="checkbox"
                                                                           class="form-check-input"
                                                                           name="core_modules[]"
                                                                           value="<?php echo $mod['ModuleID']; ?>"
                                                                           <?php echo $mod['IsCore'] ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label">Core</label>
                                                                </div>
                                                                <button type="button"
                                                                        class="btn btn-sm btn-outline-danger remove-module">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php 
                                                endforeach;
                                            }
                                            ?>
                                        </div>

                                                        <div class="text-muted">
                                                            Total Credits: <span class="year-credits" data-year="<?php echo $year; ?>">0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="programmes.php" class="btn btn-light me-md-2">Cancel</a>
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

    <!-- Form validation -->
    <script>
        // Enable Bootstrap form validation
        (function() {
            'use strict';

            var forms = document.querySelectorAll('.needs-validation');

            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>

    <!-- Add this JavaScript before the closing body tag -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enable Bootstrap form validation
            // ... existing validation code ...

            // Handle module selection
            document.querySelectorAll('.module-select').forEach(select => {
                select.addEventListener('change', function() {
                    if (!this.value) return;

                    const year = this.dataset.year;
                    const semester = this.dataset.semester;
                    const option = this.options[this.selectedIndex];
                    const moduleId = this.value;
                    const moduleName = option.text;
                    const credits = option.dataset.credits;

                    // Create module item
                    const moduleItem = document.createElement('div');
                    moduleItem.className = 'card mb-2 module-item';
                    moduleItem.innerHTML = `
                        <div class="card-body py-2">
                            <div class="row align-items-center">
                                <div class="col">
                                    <input type="hidden" name="modules[${year}][]" value="${moduleId}">
                                    <input type="hidden" name="module_years[${moduleId}]" value="${year}">
                                    <input type="hidden" name="module_semesters[${moduleId}]" value="${semester}">
                                    ${moduleName}<br>
                                    <small class="text-muted">
                                        ${credits} credits | Semester ${semester}
                                    </small>
                                </div>
                                <div class="col-auto">
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" name="core_modules[]" value="${moduleId}" checked>
                                        <label class="form-check-label">Core</label>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-module">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;

                    // Add module item to the list
                    const modulesList = document.querySelector(`.selected-modules[data-year="${year}"]`);
                    modulesList.appendChild(moduleItem);

                    // Reset select
                    this.value = '';

                    // Update credits
                    updateYearCredits(year);
                });
            });

            // Handle module removal
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-module')) {
                    const moduleItem = e.target.closest('.module-item');
                    const year = moduleItem.closest('.selected-modules').dataset.year;
                    moduleItem.remove();
                    updateYearCredits(year);
                }
            });

            // Calculate and update year credits
            function updateYearCredits(year) {
                const moduleItems = document.querySelectorAll(`.selected-modules[data-year="${year}"] .module-item`);
                let totalCredits = 0;
                moduleItems.forEach(item => {
                    const creditsText = item.querySelector('.text-muted').textContent;
                    const credits = parseInt(creditsText.match(/(\d+) credits/)[1]);
                    totalCredits += credits || 0;
                });
                document.querySelector(`.year-credits[data-year="${year}"]`).textContent = totalCredits;
            }

            // Initialize year credits for each year
            document.querySelectorAll('.year-credits').forEach(span => {
                const year = span.dataset.year;
                updateYearCredits(year);
            });
        });
    </script>
</body>
</html> 