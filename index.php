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
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </nav>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body stat-card">
                        <div class="stat-icon primary">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($total_employees); ?></div>
                        <div class="stat-label">Total Employees</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body stat-card">
                        <div class="stat-icon success">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($active_employees); ?></div>
                        <div class="stat-label">Active Employees</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body stat-card">
                        <div class="stat-icon warning">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($total_users); ?></div>
                        <div class="stat-label">System Users</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body stat-card">
                        <div class="stat-icon info">
                            <i class="bi bi-person-plus"></i>
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
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Recent Employees</span>
                        <div class="d-flex gap-2">
                            <a href="employees/index.php" class="btn btn-sm btn-light border fw-semibold">View All</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Position</th>
                                    <th>Joined Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_employees)): ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No recent employees.</td></tr>
                                <?php else: ?>
                                    <?php foreach($recent_employees as $emp): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $emp['photo_url'] ? BASE_URL . $emp['photo_url'] : 'https://ui-avatars.com/api/?name='.urlencode($emp['full_name']).'&background=random'; ?>" class="avatar me-3" alt="" style="width: 32px; height: 32px; object-fit: cover; border-radius: 50%;">
                                                <div>
                                                    <div class="fw-bold text-dark small"><?php echo htmlspecialchars($emp['full_name']); ?></div>
                                                    <div class="small text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($emp['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="small"><?php echo htmlspecialchars($emp['position']); ?></td>
                                        <td class="small"><?php echo date('M d, Y', strtotime($emp['joining_date'])); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($emp['status']); ?>" style="font-size: 0.7rem;">
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
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header">
                        Recent Activity
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="d-flex mb-4">
                                <div class="bg-primary-soft text-primary rounded-circle p-2 me-3 flex-shrink-0" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-person-plus-fill"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold small">New employee onboarded</p>
                                    <p class="mb-1 text-muted small">John Doe has been added to the Engineering department.</p>
                                    <small class="text-muted" style="font-size: 0.7rem;">2 hours ago</small>
                                </div>
                            </div>
                            <div class="d-flex mb-4">
                                <div class="bg-light text-secondary rounded-circle p-2 me-3 flex-shrink-0" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-shield-lock-fill"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold small">User permissions updated</p>
                                    <p class="mb-1 text-muted small">Admin updated roles for 3 users in the Finance department.</p>
                                    <small class="text-muted" style="font-size: 0.7rem;">5 hours ago</small>
                                </div>
                            </div>
                            <div class="d-flex mb-0">
                                <div class="bg-info-soft text-info rounded-circle p-2 me-3 flex-shrink-0" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #e1f5fe; color: #0277bd;">
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold small">Payroll generated</p>
                                    <p class="mb-1 text-muted small">Monthly payroll reports for May 2024 are ready for review.</p>
                                    <small class="text-muted" style="font-size: 0.7rem;">Yesterday</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
