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
        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    showToast('Action completed successfully!');
                });
            </script>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php
                switch ($_GET['error']) {
                    case 'email_exists':
                        echo 'Error: This email address is already registered.';
                        break;
                    case 'invalid_email':
                        echo 'Error: Please enter a valid email address.';
                        break;
                    case 'empty_fields':
                        echo 'Error: All required fields must be filled.';
                        break;
                    case 'self_delete':
                        echo 'Error: You cannot delete your own account.';
                        break;
                    case 'db_error':
                        echo 'Error: A database error occurred. Please try again.';
                        break;
                    default:
                        echo 'An unexpected error occurred.';
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">User Management</li>
            </ol>
        </nav>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="mb-0 fw-bold">System Users</h6>
                    </div>
                    <div class="col-auto">
                        <form class="d-flex gap-2" method="GET">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <button type="submit" class="btn btn-light btn-sm border">Search</button>
                            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="bi bi-plus-lg"></i> Add User
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    No users found matching your criteria.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark"><?php echo htmlspecialchars($user['name']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><span class="badge bg-primary-soft text-primary rounded-pill px-3"><?php echo htmlspecialchars($user['role']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <button class="btn btn-light btn-sm border edit-user-btn"
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
                                                <form action="do_delete.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                    <?php csrf_input(); ?>
                                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" class="btn btn-light btn-sm border text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                                </form>
                                            <?php else: ?>
                                                <button class="btn btn-light btn-sm border text-muted" title="You cannot delete yourself" disabled><i class="bi bi-trash"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <p class="mb-0 small text-muted">
                            Showing <?php echo $showing_from; ?> to <?php echo $showing_to; ?> of <?php echo $total_users; ?> users
                        </p>
                    </div>
                    <?php if ($total_pages > 1): ?>
                        <div class="col-auto">
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>"><i class="bi bi-chevron-left"></i></a>
                                    </li>
                                    <?php
                                    // Show max 5 page numbers with ellipsis
                                    $range = 2;
                                    $start_page = max(1, $page - $range);
                                    $end_page = min($total_pages, $page + $range);

                                    if ($start_page > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode($search) . '">1</a></li>';
                                        if ($start_page > 2) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                                    }

                                    for ($i = $start_page; $i <= $end_page; $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor;

                                    if ($end_page < $total_pages) {
                                        if ($end_page < $total_pages - 1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                                        echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '&search=' . urlencode($search) . '">' . $total_pages . '</a></li>';
                                    }
                                    ?>
                                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>"><i class="bi bi-chevron-right"></i></a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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