<?php 
require_once '../includes/auth.php';
require_once '../config/database.php';
checkAuth();

$page_title = 'Employee Management';
$current_page = 'employees';

// Pagination settings
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Filtering
$search = sanitize($_GET['search'] ?? '');
$dept = sanitize($_GET['dept'] ?? '');
$status = sanitize($_GET['status_filter'] ?? ''); // Use status_filter to avoid conflict with status badges

$where_clauses = ["deleted_at IS NULL"];
$params = [];

if (!empty($search)) {
    $where_clauses[] = "(full_name LIKE ? OR email LIKE ? OR employee_id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($dept) && $dept !== 'All Departments') {
    $where_clauses[] = "department = ?";
    $params[] = $dept;
}

if (!empty($status) && $status !== 'All Status') {
    $where_clauses[] = "status = ?";
    $params[] = strtolower($status);
}

$where_sql = implode(" AND ", $where_clauses);

// Count total records
$count_sql = "SELECT COUNT(*) FROM employees WHERE $where_sql";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $per_page);

// Clamp page
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $per_page;

// Fetch employees
$sql = "SELECT * FROM employees WHERE $where_sql ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$employees = $stmt->fetchAll();

include '../includes/header.php'; 
include '../includes/sidebar.php';
?>

<div id="content">
    <?php include '../includes/navbar.php'; ?>

    <main class="main-content">
        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <script>document.addEventListener('DOMContentLoaded', function() { showToast('Action completed successfully!'); });</script>
        <?php endif; ?>

        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 fw-800 mb-1">Employee Directory</h2>
                <p class="text-muted mb-0">Total records: <span class="badge bg-primary-soft text-primary rounded-pill"><?php echo number_format($total_records); ?></span></p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-light border px-3"><i class="bi bi-download me-2"></i>Export</button>
                <a href="add.php" class="btn btn-primary px-4 shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i>Add Talent
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card border-0 mb-4 overflow-visible">
            <div class="card-body p-3">
                <form class="row g-2 align-items-center" method="GET">
                    <div class="col-12 col-lg-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-0 py-2" placeholder="Search name, ID or email..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <select name="dept" class="form-select bg-light border-0 py-2">
                            <option value="">All Departments</option>
                            <?php 
                                $depts = ['Engineering', 'Design', 'Marketing', 'Finance', 'Human Resources'];
                                foreach($depts as $d) {
                                    $selected = ($dept == $d) ? 'selected' : '';
                                    echo "<option value=\"$d\" $selected>$d</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-6 col-lg-2">
                        <select name="status_filter" class="form-select bg-light border-0 py-2">
                            <option value="">All Status</option>
                            <option value="active" <?php echo $status == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $status == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            <option value="onboarding" <?php echo $status == 'onboarding' ? 'selected' : ''; ?>>Onboarding</option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-2 ms-lg-auto">
                        <button type="submit" class="btn btn-dark w-100 py-2 fw-700">Filter Results</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Employee Table Card -->
        <div class="card border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Team Member</th>
                            <th>Contact & ID</th>
                            <th>Position & Dept</th>
                            <th>Current Status</th>
                            <th class="text-end pe-4">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-person-x fs-1 text-light mb-3"></i>
                                        <p class="text-muted">No talent matches your current filters.</p>
                                        <a href="index.php" class="btn btn-sm btn-outline-primary rounded-pill px-4">Clear All Filters</a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="position-relative me-3">
                                                <img src="<?php echo $employee['photo_url'] ? BASE_URL . $employee['photo_url'] : 'https://ui-avatars.com/api/?name='.urlencode($employee['full_name']).'&background=4f46e5&color=fff'; ?>" class="avatar" alt="" style="width: 48px; height: 48px;">
                                                <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white border-2 rounded-circle"></span>
                                            </div>
                                            <div>
                                                <div class="fw-800 text-dark"><?php echo htmlspecialchars($employee['full_name']); ?></div>
                                                <div class="small text-muted" style="font-size: 0.75rem;">Joined <?php echo date('M Y', strtotime($employee['joining_date'])); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-700 small mb-1"><?php echo htmlspecialchars($employee['employee_id']); ?></div>
                                        <div class="text-muted small" style="font-size: 0.75rem;"><i class="bi bi-envelope-at me-1"></i><?php echo htmlspecialchars($employee['email']); ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-700 small mb-1"><?php echo htmlspecialchars($employee['position']); ?></div>
                                        <div class="text-muted small" style="font-size: 0.75rem;"><i class="bi bi-building me-1"></i><?php echo htmlspecialchars($employee['department']); ?></div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($employee['status']); ?>">
                                            <i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i>
                                            <?php echo ucfirst($employee['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="edit.php?id=<?php echo $employee['id']; ?>" class="btn btn-icon btn-light border btn-sm rounded-3" title="Edit Profile">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <div class="dropdown">
                                                <button class="btn btn-icon btn-light border btn-sm rounded-3" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-md border-0 p-2">
                                                    <li><a class="dropdown-item rounded-2" href="#"><i class="bi bi-eye me-2"></i> View Profile</a></li>
                                                    <li><a class="dropdown-item rounded-2" href="#"><i class="bi bi-calendar-check me-2"></i> Attendance</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="do_delete.php" method="POST" onsubmit="return confirm('Are you sure you want to terminate this employee?');">
                                                            <?php csrf_input(); ?>
                                                            <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
                                                            <button type="submit" class="dropdown-item rounded-2 text-danger"><i class="bi bi-trash me-2"></i> Terminate</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_records > 0): ?>
            <div class="card-footer bg-white border-0 py-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small fw-500">
                        Showing <span class="text-dark fw-700"><?php echo $offset + 1; ?>-<?php echo min($offset + $per_page, $total_records); ?></span> of <span class="text-dark fw-700"><?php echo $total_records; ?></span> members
                    </div>
                    <div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0 gap-1">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link rounded-2 border-0 bg-light px-3" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&dept=<?php echo urlencode($dept); ?>&status_filter=<?php echo urlencode($status); ?>"><i class="bi bi-chevron-left"></i></a>
                                </li>
                                
                                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                    <?php if ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                            <a class="page-link rounded-2 border-0 mx-1 px-3" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&dept=<?php echo urlencode($dept); ?>&status_filter=<?php echo urlencode($status); ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                                        <li class="page-item disabled"><span class="page-link border-0">...</span></li>
                                    <?php endif; ?>
                                <?php endfor; ?>
 
                                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link rounded-2 border-0 bg-light px-3" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&dept=<?php echo urlencode($dept); ?>&status_filter=<?php echo urlencode($status); ?>"><i class="bi bi-chevron-right"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
