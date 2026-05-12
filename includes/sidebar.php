<aside id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo BASE_URL; ?>index.php" class="logo">
            <div class="sidebar-logo-icon">
                <i class="bi bi-person-vcard"></i>
            </div>
            <span>HR<b>OS</b></span>
        </a>
    </div>
    
    <div class="sidebar-content flex-grow-1 overflow-auto">
        <div class="sidebar-section-label">Operations</div>
        <nav class="nav nav-pills flex-column">
            <a class="nav-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>index.php">
                <i class="bi bi-grid-fill"></i>
                Dashboard
            </a>
            <a class="nav-link <?php echo ($current_page == 'employees') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>employees/index.php">
                <i class="bi bi-people-fill"></i>
                Employees
            </a>
        </nav>

        <div class="sidebar-section-label">System Settings</div>
        <nav class="nav nav-pills flex-column">
            <a class="nav-link <?php echo ($current_page == 'users') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>users/index.php">
                <i class="bi bi-shield-lock-fill"></i>
                User Management
            </a>
            <a class="nav-link" href="#">
                <i class="bi bi-gear-fill"></i>
                Settings
            </a>
        </nav>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'User'); ?>&background=4f46e5&color=fff" alt="">
            <div class="sidebar-user-info">
                <p><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin User'); ?></p>
                <span><?php echo ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'Admin')); ?></span>
            </div>
        </div>
        <a href="<?php echo BASE_URL; ?>auth/logout.php" class="btn btn-signout text-decoration-none">
            <i class="bi bi-box-arrow-left me-2"></i>Sign Out
        </a>
    </div>
</aside>
