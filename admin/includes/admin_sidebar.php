<nav id="adminSidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo isCurrentPage('dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isCurrentPage('programmes.php') ? 'active' : ''; ?>" href="programmes.php">
                    <i class="fas fa-graduation-cap me-2"></i>Programmes
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isCurrentPage('modules.php') ? 'active' : ''; ?>" href="modules.php">
                    <i class="fas fa-book me-2"></i>Modules
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isCurrentPage('staff.php') ? 'active' : ''; ?>" href="staff.php">
                    <i class="fas fa-users me-2"></i>Staff
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isCurrentPage('levels.php') ? 'active' : ''; ?>" href="levels.php">
                    <i class="fas fa-layer-group me-2"></i>Levels
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isCurrentPage('interested_students.php') ? 'active' : ''; ?>" 
                   href="interested_students.php">
                    <i class="fas fa-user-graduate me-2"></i>Student Interests
                </a>
            </li>
        </ul>

        <?php if (Security::hasRole('Admin')): ?>
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>Administration</span>
            </h6>
            
            <ul class="nav flex-column mb-2">
                <li class="nav-item">
                    <a class="nav-link <?php echo isCurrentPage('users.php') ? 'active' : ''; ?>" href="users.php">
                        <i class="fas fa-user-shield me-2"></i>Users
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo isCurrentPage('activity_log.php') ? 'active' : ''; ?>" 
                       href="activity_log.php">
                        <i class="fas fa-history me-2"></i>Activity Log
                    </a>
                </li>
                
                
            </ul>
        <?php endif; ?>
    </div>
</nav>

<style>
.sidebar {
    position: fixed;
    top: 56px;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.sidebar .nav-link {
    font-weight: 500;
    color: #333;
    padding: 0.5rem 1rem;
}

.sidebar .nav-link.active {
    color: #2563eb;
    background-color: rgba(37, 99, 235, 0.1);
}

.sidebar .nav-link:hover {
    color: #1d4ed8;
    background-color: rgba(37, 99, 235, 0.05);
}

.sidebar-heading {
    font-size: .75rem;
    text-transform: uppercase;
}

@media (max-width: 767.98px) {
    .sidebar {
        position: static;
        height: auto;
        padding-top: 0;
    }
}
</style>
