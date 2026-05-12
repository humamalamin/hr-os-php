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

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Employee Directory</li>
            </ol>
        </nav>

        <div class="row mb-4">
            <div class="col">
                <h4 class="fw-bold mb-0">Employee Directory</h4>
                <p class="text-muted small">Manage and view all employee information in one place.</p>
            </div>
            <div class="col-auto">
                <a href="add.php" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-person-plus"></i> Add Employee
                </a>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form class="row g-3" method="GET">
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold">Search Name or Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold">Department</label>
                        <select name="dept" class="form-select">
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
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status_filter" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" <?php echo $status == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $status == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            <option value="onboarding" <?php echo $status == 'onboarding' ? 'selected' : ''; ?>>Onboarding</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-light border w-100 fw-semibold">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Employee Table Card -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Full Name</th>
                            <th>Contact Info</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    No employees found matching your criteria. <a href="add.php">Add new employee</a>.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td style="width: 80px;">
                                        <img src="<?php echo $employee['photo_url'] ? BASE_URL . $employee['photo_url'] : 'https://ui-avatars.com/api/?name='.urlencode($employee['full_name']).'&background=random'; ?>" class="rounded shadow-sm" alt="" style="width: 48px; height: 48px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($employee['full_name']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($employee['employee_id']); ?></div>
                                    </td>
                                    <td>
                                        <div class="small fw-medium"><?php echo htmlspecialchars($employee['email']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($employee['phone']); ?></div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold"><?php echo htmlspecialchars($employee['position']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($employee['department']); ?></div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($employee['status']); ?>">
                                            <?php echo ucfirst($employee['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm border" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                <li><a class="dropdown-item" href="edit.php?id=<?php echo $employee['id']; ?>"><i class="bi bi-pencil me-2"></i> Edit Profile</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i> View Details</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="do_delete.php" method="POST" onsubmit="return confirm('Are you sure you want to terminate this employee?');">
                                                        <?php csrf_input(); ?>
                                                        <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
                                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i> Terminate</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_records > 0): ?>
            <div class="card-footer bg-white py-3">
                <div class="row align-items-center">
                    <div class="col text-muted small">
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total_records); ?> of <?php echo $total_records; ?> employees
                    </div>
                    <div class="col-auto">
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&dept=<?php echo urlencode($dept); ?>&status_filter=<?php echo urlencode($status); ?>"><i class="bi bi-chevron-left"></i></a>
                                </li>
                                
                                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                    <?php if ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&dept=<?php echo urlencode($dept); ?>&status_filter=<?php echo urlencode($status); ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&dept=<?php echo urlencode($dept); ?>&status_filter=<?php echo urlencode($status); ?>"><i class="bi bi-chevron-right"></i></a>
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
