<nav class="navbar navbar-expand-lg top-navbar sticky-top">
    <div class="container-fluid">
        <div class="d-flex align-items-center flex-grow-1">
            <button type="button" id="sidebarCollapse" class="btn btn-light border-0 d-lg-none me-3">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div class="search-container d-none d-md-block" style="width: 300px;">
                <div class="input-group bg-light rounded-3 px-2">
                    <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control bg-transparent border-0 small" placeholder="Search...">
                </div>
            </div>
        </div>

        <div class="ms-auto d-flex align-items-center gap-3">
            <!-- Notifications -->
            <button class="btn btn-light border-0 position-relative rounded-circle p-2" type="button">
                <i class="bi bi-bell text-muted fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-white rounded-circle"></span>
            </button>

            <!-- Profile Info -->
            <div class="d-flex align-items-center border-start ps-3 ms-2">
                <div class="text-end me-3 d-none d-sm-block">
                    <p class="mb-0 small fw-800 text-dark"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin User'); ?></p>
                    <p class="mb-0 text-muted" style="font-size: 0.65rem;"><?php echo ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'Admin')); ?></p>
                </div>
                <div class="bg-primary-soft rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #f3e8ff; color: #7e22ce;">
                    <span class="fw-bold small"><?php echo substr($_SESSION['user_name'] ?? 'AU', 0, 1) . substr(explode(' ', $_SESSION['user_name'] ?? 'User')[1] ?? 'U', 0, 1); ?></span>
                </div>
                <i class="bi bi-chevron-down small text-muted ms-2"></i>
            </div>
        </div>
    </div>
</nav>
