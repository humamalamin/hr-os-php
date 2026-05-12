<nav class="navbar navbar-expand-lg top-navbar sticky-top">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <button type="button" id="sidebarCollapse" class="btn btn-light border-0 d-lg-none me-3">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div class="d-none d-lg-block">
                <h5 class="mb-0 fw-800 text-dark"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h5>
            </div>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2">
            <!-- Search Toggle (Mobile) -->
            <button class="btn btn-light border-0 d-md-none rounded-circle p-2">
                <i class="bi bi-search"></i>
            </button>

            <!-- Notifications -->
            <div class="dropdown">
                <button class="btn btn-light border-0 position-relative rounded-circle p-2" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-1 start-100 translate-middle p-1 bg-danger border border-white rounded-circle">
                        <span class="visually-hidden">New alerts</span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-0 overflow-hidden" style="width: 320px; border-radius: 16px;">
                    <div class="px-3 py-3 border-bottom bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-800">Notifications</h6>
                            <span class="badge bg-primary-soft text-primary rounded-pill">2 New</span>
                        </div>
                    </div>
                    <div class="p-2">
                        <a href="#" class="dropdown-item rounded-3 p-3 mb-1">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary-soft text-primary rounded-circle p-2 me-3">
                                    <i class="bi bi-person-plus-fill"></i>
                                </div>
                                <div>
                                    <p class="mb-0 small fw-700">New employee registered</p>
                                    <p class="mb-1 text-muted smaller">Sarah Connor joined the team.</p>
                                    <small class="text-light" style="font-size: 0.65rem;">2 minutes ago</small>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="dropdown-item rounded-3 p-3">
                            <div class="d-flex align-items-start">
                                <div class="bg-warning-soft text-warning rounded-circle p-2 me-3" style="background-color: #fffbeb; color: #d97706;">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                                <div>
                                    <p class="mb-0 small fw-700">Storage limit reached</p>
                                    <p class="mb-1 text-muted smaller">Employee docs folder is 90% full.</p>
                                    <small class="text-light" style="font-size: 0.65rem;">1 hour ago</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="text-center bg-light p-2">
                        <a href="#" class="text-primary smaller fw-700 text-decoration-none">View all notifications</a>
                    </div>
                </div>
            </div>

            <!-- Profile -->
            <div class="dropdown ms-2">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark no-caret" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'User'); ?>&background=4f46e5&color=fff" alt="" class="avatar">
                    <div class="d-none d-md-block ms-2 me-1">
                        <p class="mb-0 small fw-800 line-height-1"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></p>
                        <p class="mb-0 text-muted" style="font-size: 0.65rem;"><?php echo ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'Guest')); ?></p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2" style="border-radius: 16px; min-width: 200px;">
                    <div class="px-3 py-2 mb-2 border-bottom d-md-none">
                        <p class="mb-0 small fw-800"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></p>
                        <small class="text-muted"><?php echo ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'Guest')); ?></small>
                    </div>
                    <li><a class="dropdown-item rounded-3 py-2" href="#"><i class="bi bi-person me-2 text-muted"></i> My Profile</a></li>
                    <li><a class="dropdown-item rounded-3 py-2" href="#"><i class="bi bi-gear me-2 text-muted"></i> Settings</a></li>
                    <li><a class="dropdown-item rounded-3 py-2" href="#"><i class="bi bi-activity me-2 text-muted"></i> Activity Log</a></li>
                    <li><hr class="dropdown-divider opacity-50"></li>
                    <li><a class="dropdown-item rounded-3 py-2 text-danger" href="<?php echo BASE_URL; ?>auth/logout.php"><i class="bi bi-box-arrow-left me-2"></i> Sign Out</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
