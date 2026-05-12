<aside id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo BASE_URL; ?>index.php" class="logo">
            <i class="bi bi-people-fill"></i>
            <span>HR-OS</span>
        </a>
    </div>
    
    <div class="py-3">
        <div class="px-4 mb-2">
            <small class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 0.05rem;">General</small>
        </div>
        <nav class="nav nav-pills flex-column">
            <a class="nav-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>index.php">
                <i class="bi bi-grid-1x2"></i>
                Dashboard
            </a>
            <a class="nav-link <?php echo ($current_page == 'employees') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>employees/index.php">
                <i class="bi bi-person-badge"></i>
                Employees
            </a>
            <a class="nav-link <?php echo ($current_page == 'users') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>users/index.php">
                <i class="bi bi-shield-lock"></i>
                User Access
            </a>
        </nav>

        <div class="px-4 mb-2 mt-4">
            <small class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 0.05rem;">Organization</small>
        </div>
        <nav class="nav nav-pills flex-column">
            <a class="nav-link" href="#">
                <i class="bi bi-building"></i>
                Departments
            </a>
            <a class="nav-link" href="#">
                <i class="bi bi-briefcase"></i>
                Positions
            </a>
            <a class="nav-link" href="#">
                <i class="bi bi-calendar-event"></i>
                Attendance
            </a>
            <a class="nav-link" href="#">
                <i class="bi bi-cash-stack"></i>
                Payroll
            </a>
        </nav>

        <div class="px-4 mb-2 mt-4">
            <small class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 0.05rem;">System</small>
        </div>
        <nav class="nav nav-pills flex-column">
            <a class="nav-link" href="#">
                <i class="bi bi-gear"></i>
                Settings
            </a>
            <a class="nav-link text-danger mt-4" href="<?php echo BASE_URL; ?>auth/logout.php">
                <i class="bi bi-box-arrow-left"></i>
                Logout
            </a>
        </nav>
    </div>
</aside>
