<nav class="navbar navbar-expand-lg top-navbar sticky-top">
    <div class="container-fluid">
        <button type="button" id="sidebarCollapse" class="btn btn-light d-lg-none">
            <i class="bi bi-list"></i>
        </button>
        
        <div class="d-none d-lg-block">
            <h5 class="mb-0 text-dark"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h5>
        </div>

        <div class="ms-auto d-flex align-items-center">
            <div class="dropdown me-3">
                <button class="btn btn-light position-relative rounded-circle p-2" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                        <span class="visually-hidden">New alerts</span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="width: 300px;">
                    <div class="px-3 py-2 border-bottom">
                        <h6 class="mb-0">Notifications</h6>
                    </div>
                    <div class="p-2">
                        <a href="#" class="dropdown-item rounded p-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary-soft text-primary rounded-circle p-2 me-3">
                                    <i class="bi bi-person-plus"></i>
                                </div>
                                <div>
                                    <p class="mb-0 small fw-medium">New employee registered</p>
                                    <small class="text-muted">2 minutes ago</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="text-center border-top p-2">
                        <a href="#" class="text-primary small text-decoration-none">View all</a>
                    </div>
                </div>
            </div>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'User'); ?>&background=0D6EFD&color=fff" alt="" class="avatar me-2">
                    <div class="d-none d-md-block">
                        <p class="mb-0 small fw-bold"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></p>
                        <p class="mb-0 text-muted" style="font-size: 0.7rem;"><?php echo ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'Guest')); ?></p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> My Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>auth/logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
