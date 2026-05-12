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
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="h3 fw-800 mb-1">Welcome back, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>!</h2>
                <p class="text-muted mb-0">Here's what's happening with your workforce today.</p>
            </div>
            <div class="d-none d-md-block">
                <a href="employees/add.php" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Add Employee
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0">
                    <div class="card-body stat-card">
                        <div class="stat-icon primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($total_employees); ?></div>
                        <div class="stat-label">Total Workforce</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0">
                    <div class="card-body stat-card">
                        <div class="stat-icon success">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($active_employees); ?></div>
                        <div class="stat-label">Active Status</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0">
                    <div class="card-body stat-card">
                        <div class="stat-icon warning">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($total_users); ?></div>
                        <div class="stat-label">System Admins</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0">
                    <div class="card-body stat-card">
                        <div class="stat-icon info">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($new_employees_count); ?></div>
                        <div class="stat-label">New Hires (30d)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Employees Table -->
            <div class="col-12 col-xl-8">
                <div class="card border-0 h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fs-5">Recent Talent</span>
                        <a href="employees/index.php" class="btn btn-sm btn-light border text-primary fw-bold px-3">View Directory</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department/Role</th>
                                    <th>Joined</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_employees)): ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted">No recent records found.</td></tr>
                                <?php else: ?>
                                    <?php foreach($recent_employees as $emp): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $emp['photo_url'] ? BASE_URL . $emp['photo_url'] : 'https://ui-avatars.com/api/?name='.urlencode($emp['full_name']).'&background=4f46e5&color=fff'; ?>" class="avatar me-3" alt="">
                                                <div>
                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($emp['full_name']); ?></div>
                                                    <div class="small text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($emp['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold"><?php echo htmlspecialchars($emp['position']); ?></div>
                                            <div class="text-muted small" style="font-size: 0.7rem;">Engineering</div>
                                        </td>
                                        <td class="small text-muted"><?php echo date('M d, Y', strtotime($emp['joining_date'])); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($emp['status']); ?>">
                                                <i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i>
                                                <?php echo ucfirst($emp['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-12 col-xl-4">
                <div class="card border-0 h-100">
                    <div class="card-header">
                        Updates & Activity
                    </div>
                    <div class="card-body">
                        <div class="timeline mt-2">
                            <div class="timeline-item">
                                <div class="d-flex">
                                    <div>
                                        <p class="mb-0 fw-bold small">New employee onboarded</p>
                                        <p class="mb-1 text-muted small">John Doe joined the Engineering team.</p>
                                        <small class="text-light" style="font-size: 0.7rem;">2 hours ago</small>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="d-flex">
                                    <div>
                                        <p class="mb-0 fw-bold small">User permissions updated</p>
                                        <p class="mb-1 text-muted small">Admin updated roles for 3 users.</p>
                                        <small class="text-light" style="font-size: 0.7rem;">5 hours ago</small>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="d-flex">
                                    <div>
                                        <p class="mb-0 fw-bold small">Payroll generated</p>
                                        <p class="mb-1 text-muted small">Reports for May 2024 are ready.</p>
                                        <small class="text-light" style="font-size: 0.7rem;">Yesterday</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button class="btn btn-sm text-primary fw-bold">View Audit Logs <i class="bi bi-arrow-right ms-1"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
