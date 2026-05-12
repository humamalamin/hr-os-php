<?php
require_once '../includes/auth.php';
require_once '../config/database.php';
checkAuth();

$page_title = 'User Management';
$current_page = 'users';

// Search logic
$search = sanitize($_GET['search'] ?? '');
$where_sql = "WHERE deleted_at IS NULL";
$params = [];

if (!empty($search)) {
    $where_sql .= " AND (name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Pagination config
$per_page = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

// Count total users
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM users $where_sql");
$count_stmt->execute($params);
$total_users = (int) $count_stmt->fetchColumn();
$total_pages = max(1, (int) ceil($total_users / $per_page));

// Clamp page to valid range
if ($page > $total_pages) {
    $page = $total_pages;
    $offset = ($page - 1) * $per_page;
}

// Fetch current page
$stmt = $pdo->prepare("SELECT * FROM users $where_sql ORDER BY created_at DESC LIMIT ? OFFSET ?");
foreach ($params as $key => $val) {
    $stmt->bindValue($key + 1, $val);
}
$stmt->bindValue(count($params) + 1, $per_page, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();

// Display range
$showing_from = $total_users > 0 ? $offset + 1 : 0;
$showing_to = min($offset + $per_page, $total_users);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div id="content">
    <?php include '../includes/navbar.php'; ?>

    <main class="main-content">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-800 text-dark mb-1">User Management</h2>
                <p class="text-muted mb-0">Manage system access levels and administrative accounts.</p>
            </div>
            <button type="button" class="btn btn-primary fw-bold px-4" style="background-color: #2563eb; border-color: #2563eb;" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-plus-lg me-2"></i>Add New User
            </button>
        </div>

        <!-- Filter Bar Card -->
        <div class="card border-0 mb-4">
            <div class="card-body p-3">
                <form class="row g-3 align-items-center" method="GET">
                    <div class="col-12 col-lg-8">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" name="search" class="form-control employee-filter-input" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <a href="index.php" class="btn btn-light border bg-white w-100 fw-bold py-2" style="border-radius: 10px;">Reset</a>
                    </div>
                    <div class="col-6 col-lg-2">
                        <button type="submit" class="btn btn-apply-filter w-100 py-2">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- User Table Card -->
        <div class="card border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle employee-table">
                    <thead>
                        <tr>
                            <th>USER</th>
                            <th>EMAIL ADDRESS</th>
                            <th>ACCESS LEVEL</th>
                            <th>ACCOUNT STATUS</th>
                            <th class="text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <p class="text-muted mb-0">No users found matching your search.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-initial-avatar me-3">
                                                <?php 
                                                    $names = explode(' ', $user['name']);
                                                    echo strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                                                ?>
                                            </div>
                                            <div>
                                                <div class="employee-row-title"><?php echo htmlspecialchars($user['name']); ?></div>
                                                <div class="employee-row-sub">ID: #<?php echo $user['id']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="employee-row-title" style="font-weight: 600; font-size: 0.85rem;"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </td>
                                    <td>
                                        <span class="role-badge <?php echo strtolower($user['role']); ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge-v2 active">Active</span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn-action-light edit-user-btn"
                                                title="Edit"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editUserModal"
                                                data-id="<?php echo $user['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                                data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                data-role="<?php echo $user['role']; ?>">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <form action="do_delete.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this user account?');">
                                                    <?php csrf_input(); ?>
                                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" class="btn-action-light delete" title="Delete"><i class="bi bi-trash"></i></button>
                                                </form>
                                            <?php else: ?>
                                                <button class="btn-action-light text-muted opacity-50" title="You cannot delete yourself" disabled><i class="bi bi-trash"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white border-0 py-3 px-4">
                <p class="text-muted small mb-0">Showing <b><?php echo count($users); ?></b> of <b><?php echo $total_users; ?></b> users</p>
            </div>
        </div>
    </main>
iv>
    </main>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <form action="do_add.php" method="POST">
                    <?php csrf_input(); ?>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="name@company.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">System Role</label>
                        <select name="role" class="form-select" required>
                            <option selected disabled value="">Select a role</option>
                            <option value="admin">Administrator</option>
                            <option value="manager">HR Manager</option>
                            <option value="editor">Finance Editor</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Temporary Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        <div class="form-text small">User will be prompted to change this on first login.</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light border w-100" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary w-100">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <form action="do_edit.php" method="POST">
                    <?php csrf_input(); ?>
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Full Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control" placeholder="Enter name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Address</label>
                        <input type="email" name="email" id="edit-email" class="form-control" placeholder="name@company.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">System Role</label>
                        <select name="role" id="edit-role" class="form-select" required>
                            <option value="admin">Administrator</option>
                            <option value="manager">HR Manager</option>
                            <option value="editor">Finance Editor</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">New Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light border w-100" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary w-100">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-user-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit-id').value = this.dataset.id;
                document.getElementById('edit-name').value = this.dataset.name;
                document.getElementById('edit-email').value = this.dataset.email;
                document.getElementById('edit-role').value = this.dataset.role;
            });
        });
    });
</script>

<?php include '../includes/footer.php'; ?>