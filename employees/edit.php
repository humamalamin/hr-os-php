<?php 
require_once '../includes/auth.php';
require_once '../config/database.php';
checkAuth();

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->execute([$id]);
$employee = $stmt->fetch();

if (!$employee) {
    header("Location: index.php");
    exit();
}

$page_title = 'Edit Employee';
$current_page = 'employees';
include '../includes/header.php'; 
include '../includes/sidebar.php';
?>

<div id="content">
    <?php include '../includes/navbar.php'; ?>

    <main class="main-content">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Employee Directory</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Employee</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-12 col-xl-9">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php 
                            switch($_GET['error']) {
                                case 'invalid_file': echo 'Error: Invalid file format. Only JPG/JPEG allowed.'; break;
                                case 'file_large': echo 'Error: File size too large. Max 300KB allowed.'; break;
                                case 'db_error': echo 'Error: A database error occurred. Please try again.'; break;
                                default: echo 'An error occurred while saving. Please check your input.';
                            }
                        ?>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Edit Employee Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="do_edit.php" method="POST" enctype="multipart/form-data" class="needs-validation" id="editEmployeeForm" novalidate>
                            <?php csrf_input(); ?>
                            <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
                            
                            <!-- Photo Upload -->
                            <div class="row mb-5 align-items-center">
                                <div class="col-auto">
                                    <div class="position-relative">
                                        <img src="<?php echo $employee['photo_url'] ? BASE_URL . $employee['photo_url'] : 'https://ui-avatars.com/api/?name='.urlencode($employee['full_name']).'&background=F8F9FA&color=ADB5BD'; ?>" id="photoPreview" class="rounded shadow-sm border" alt="" style="width: 120px; height: 120px; object-fit: cover;">
                                        <label for="photoInput" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle shadow-sm" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; margin-bottom: -5px; margin-right: -5px;">
                                            <i class="bi bi-camera-fill"></i>
                                            <input type="file" id="photoInput" name="photo" class="d-none" accept="image/jpeg, image/jpg">
                                        </label>
                                    </div>
                                </div>
                                <div class="col">
                                    <h6 class="fw-bold mb-1">Employee Photo</h6>
                                    <p class="text-muted small mb-0">Update headshot. <strong>JPEG/JPG only. Max 300KB.</strong></p>
                                    <small class="text-muted">Leave empty to keep current photo.</small>
                                </div>
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($employee['full_name']); ?>" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($employee['phone']); ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Department</label>
                                    <select name="department" class="form-select" required>
                                        <option disabled value="">Choose Department...</option>
                                        <?php 
                                            $depts = ['Engineering', 'Design', 'Marketing', 'Finance', 'Human Resources'];
                                            foreach($depts as $dept) {
                                                $selected = ($employee['department'] == $dept) ? 'selected' : '';
                                                echo "<option value=\"$dept\" $selected>$dept</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Position</label>
                                    <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($employee['position']); ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Joining Date</label>
                                    <input type="date" name="joining_date" class="form-control" value="<?php echo $employee['joining_date']; ?>">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label small fw-semibold">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active" <?php echo $employee['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo $employee['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                        <option value="onboarding" <?php echo $employee['status'] == 'onboarding' ? 'selected' : ''; ?>>Onboarding</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">Home Address</label>
                                    <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($employee['address']); ?></textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 border-top pt-4">
                                <a href="index.php" class="btn btn-light border px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4" id="saveBtn">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
