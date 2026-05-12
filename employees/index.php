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
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-800 text-dark mb-1">Employees</h2>
                <p class="text-muted mb-0">Manage your workforce and view detailed employee profiles.</p>
            </div>
            <a href="add.php" class="btn btn-primary fw-bold px-4" style="background-color: #2563eb; border-color: #2563eb;">
                <i class="bi bi-plus-lg me-2"></i>Add Employee
            </a>
        </div>

        <!-- Filter Bar Card -->
        <div class="card border-0 mb-4">
            <div class="card-body p-3">
                <form class="row g-3 align-items-center" method="GET">
                    <div class="col-12 col-lg-6">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" name="search" class="form-control employee-filter-input" placeholder="Search by name, email, or employee ID..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <select name="dept" class="form-select employee-filter-select">
                            <option value="">All Positions</option>
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
                        <select name="status_filter" class="form-select employee-filter-select">
                            <option value="">All Status</option>
                            <option value="active" <?php echo $status == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $status == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-2">
                        <button type="submit" class="btn btn-apply-filter w-100">Apply</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Employee Table Card -->
        <div class="card border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle employee-table">
                    <thead>
                        <tr>
                            <th>EMPLOYEE</th>
                            <th>CONTACT INFO</th>
                            <th>POSITION & DEPT</th>
                            <th>STATUS</th>
                            <th class="text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <p class="text-muted mb-0">No employees found matching your filters.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $employee['photo_url'] ? BASE_URL . $employee['photo_url'] : 'https://ui-avatars.com/api/?name='.urlencode($employee['full_name']).'&background=f1f5f9&color=475569'; ?>" class="avatar me-3" style="width: 36px; height: 36px; border-radius: 10px;">
                                            <div>
                                                <div class="employee-row-title"><?php echo htmlspecialchars($employee['full_name']); ?></div>
                                                <div class="employee-row-sub"><?php echo htmlspecialchars($employee['employee_id']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="employee-row-title" style="font-weight: 600; font-size: 0.85rem;"><?php echo htmlspecialchars($employee['email']); ?></div>
                                        <div class="employee-row-sub">+1 (555) 000-1234</div> <!-- Static phone for demo, could be from DB -->
                                    </td>
                                    <td>
                                        <div class="employee-row-title" style="font-weight: 600; font-size: 0.85rem;"><?php echo htmlspecialchars($employee['position']); ?></div>
                                        <div class="employee-row-sub"><?php echo htmlspecialchars($employee['department']); ?></div>
                                    </td>
                                    <td>
                                        <span class="status-badge-v2 active">Active</span>
                                    </td>
                                    <td>
                                        <div class="dropdown text-center">
                                            <button class="dots-menu-btn mx-auto" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2">
                                                <li><a class="dropdown-item rounded-2" href="edit.php?id=<?php echo $employee['id']; ?>"><i class="bi bi-pencil me-2 text-muted"></i> Edit</a></li>
                                                <li><a class="dropdown-item rounded-2" href="#"><i class="bi bi-eye me-2 text-muted"></i> View</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="do_delete.php" method="POST" onsubmit="return confirm('Delete this employee?');">
                                                        <?php csrf_input(); ?>
                                                        <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
                                                        <button type="submit" class="dropdown-item rounded-2 text-danger"><i class="bi bi-trash me-2"></i> Delete</button>
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
            
            <div class="card-footer bg-white border-0 py-3 px-4">
                <p class="text-muted small mb-0">Showing <b><?php echo count($employees); ?></b> of <b><?php echo $total_records; ?></b> employees</p>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
