<?php 
require_once 'includes/auth.php';
require_once 'config/database.php';
checkAuth();

// Fetch stats
$total_employees = $pdo->query("SELECT COUNT(*) FROM employees WHERE deleted_at IS NULL")->fetchColumn();
$active_employees = $pdo->query("SELECT COUNT(*) FROM employees WHERE deleted_at IS NULL AND status = 'active'")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL")->fetchColumn();

// New employees (joined in the last 30 days)
$new_employees_stmt = $pdo->query("SELECT COUNT(*) FROM employees WHERE deleted_at IS NULL AND joining_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
$new_employees_count = $new_employees_stmt->fetchColumn();

// Recent employees list
$recent_employees_stmt = $pdo->query("SELECT * FROM employees WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 4");
$recent_employees = $recent_employees_stmt->fetchAll();

$page_title = 'Dashboard';
$current_page = 'dashboard';
include 'includes/header.php'; 
include 'includes/sidebar.php';
?>

<div id="content">
    <?php include 'includes/navbar.php'; ?>

    <main class="main-content">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-800 text-dark mb-1">Dashboard Overview</h2>
                <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin User'); ?>. Here's what's happening today.</p>
            </div>
            <div class="d-flex gap-3">
                <button class="btn btn-light border bg-white fw-bold px-4">
                    <i class="bi bi-file-earmark-arrow-down me-2"></i>Export Report
                </button>
                <a href="employees/add.php" class="btn btn-primary fw-bold px-4" style="background-color: #2563eb; border-color: #2563eb;">
                    <i class="bi bi-plus-lg me-2"></i>Add Employee
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 position-relative">
                    <div class="card-body stat-card">
                        <div class="stat-icon primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <span class="stat-badge success">+12%</span>
                        <div class="stat-label">Total Employees</div>
                        <div class="stat-value"><?php echo number_format($total_employees); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 position-relative">
                    <div class="card-body stat-card">
                        <div class="stat-icon success">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <span class="stat-badge success" style="background-color: #dcfce7; color: #16a34a;">+5%</span>
                        <div class="stat-label">Active Now</div>
                        <div class="stat-value"><?php echo number_format($active_employees); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 position-relative">
                    <div class="card-body stat-card">
                        <div class="stat-icon warning">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div class="stat-label">System Users</div>
                        <div class="stat-value"><?php echo number_format($total_users); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 position-relative">
                    <div class="card-body stat-card">
                        <div class="stat-icon info">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="stat-label">Recent Activities</div>
                        <div class="stat-value">24</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recently Joined Employees -->
            <div class="col-12 col-xl-8">
                <div class="card border-0 h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Recently Joined Employees</span>
                        <a href="employees/index.php" class="text-primary fw-bold text-decoration-none small">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_employees as $emp): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $emp['photo_url'] ? BASE_URL . $emp['photo_url'] : 'https://ui-avatars.com/api/?name='.urlencode($emp['full_name']).'&background=4f46e5&color=fff'; ?>" class="avatar me-3" style="width: 32px; height: 32px; border-radius: 8px;">
                                            <div>
                                                <div class="fw-bold text-dark small"><?php echo htmlspecialchars($emp['full_name']); ?></div>
                                                <div class="text-muted" style="font-size: 0.7rem;"><?php echo htmlspecialchars($emp['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold"><?php echo htmlspecialchars($emp['position']); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2" style="font-size: 0.7rem;">Active</span>
                                    </td>
                                    <td>
                                        <button class="action-btn"><i class="bi bi-arrow-right"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="col-12 col-xl-4">
                <div class="card border-0 h-100 text-white" style="background-color: #2563eb; border-radius: 20px; overflow: hidden; position: relative;">
                    <div class="card-body p-4 position-relative" style="z-index: 2;">
                        <h5 class="fw-800 mb-3">System Information</h5>
                        <p class="small opacity-75 mb-4">You are running HR-OS v1.0.0. All systems are operational and connected to the MySQL cluster.</p>
                        
                        <div class="bg-white bg-opacity-10 rounded-3 p-3 mb-3 border border-white border-opacity-10">
                            <div class="d-flex align-items-center">
                                <div class="me-3"><i class="bi bi-database-fill fs-4"></i></div>
                                <div>
                                    <p class="mb-0 fw-bold small">Database Cluster</p>
                                    <p class="mb-0 small opacity-75">Healthy / 127.0.0.1</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white bg-opacity-10 rounded-3 p-3 mb-3 border border-white border-opacity-10">
                            <div class="d-flex align-items-center">
                                <div class="me-3"><i class="bi bi-lightning-fill fs-4"></i></div>
                                <div>
                                    <p class="mb-0 fw-bold small">Redis Cache</p>
                                    <p class="mb-0 small opacity-75">Connected / Port 6379</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Decorative large check icon -->
                    <div style="position: absolute; bottom: -20px; right: -20px; font-size: 10rem; opacity: 0.15; color: white;">
                        <i class="bi bi-check-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
