<!-- Admin Navigation Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="fas fa-user-shield me-2"></i>
            <?php echo SITE_NAME; ?> Admin
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo isCurrentPage('dashboard.php') ? 'active' : ''; ?>"
                       href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isCurrentPage('programmes.php') ? 'active' : ''; ?>"
                       href="programmes.php">
                        <i class="fas fa-graduation-cap me-1"></i>Programmes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isCurrentPage('students.php') ? 'active' : ''; ?>"
                       href="students.php">
                        <i class="fas fa-users me-1"></i>Students
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="managementDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cogs me-1"></i>Management
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="modules.php">
                            <i class="fas fa-book me-2"></i>Modules
                        </a></li>
                        <li><a class="dropdown-item" href="staff.php">
                            <i class="fas fa-user-tie me-2"></i>Staff
                        </a></li>
                        <li><a class="dropdown-item" href="levels.php">
                            <i class="fas fa-layer-group me-2"></i>Levels
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="export.php">
                            <i class="fas fa-download me-2"></i>Export Data
                        </a></li>
                    </ul>
                </li>
            </ul>

            <ul class="navbar-nav">
                <!-- View Site -->
                <li class="nav-item">
                    <a class="nav-link" href="../index.php" target="_blank" title="View Public Site">
                        <i class="fas fa-external-link-alt me-1"></i>View Site
                    </a>
                </li>

                <!-- Admin User Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['username'] ?? 'Admin'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text">
                                <small class="text-muted">
                                    Role: <?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Admin'); ?>
                                </small>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <?php if (Security::hasRole('Admin')): ?>
                            <li><a class="dropdown-item" href="users.php">
                                <i class="fas fa-users-cog me-2"></i>Manage Users
                            </a></li>
                            
                            <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        
                        <li><a class="dropdown-item" href="activity_log.php">
                            <i class="fas fa-history me-2"></i>Activity Log
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content Wrapper with Top Margin for Fixed Navbar -->
<div style="margin-top: 76px;"></div>
