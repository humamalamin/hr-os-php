<?php 
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Add New Employee';
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
                <li class="breadcrumb-item active" aria-current="page">Add Employee</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-12 col-xl-9">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger border-0 small mb-4" role="alert">
                        An error occurred while saving the employee. Please try again.
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Employee Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="do_add.php" method="POST" enctype="multipart/form-data" class="needs-validation" id="addEmployeeForm" novalidate>
                            <?php csrf_input(); ?>
                            <!-- Photo Upload -->
                            <div class="row mb-5 align-items-center">
                                <div class="col-auto">
                                    <div class="position-relative">
                                        <img src="https://ui-avatars.com/api/?name=New+Employee&background=F8F9FA&color=ADB5BD" id="photoPreview" class="rounded shadow-sm border" alt="" style="width: 120px; height: 120px; object-fit: cover;">
                                        <label for="photoInput" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle shadow-sm" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; margin-bottom: -5px; margin-right: -5px;">
                                            <i class="bi bi-camera-fill"></i>
                                            <input type="file" id="photoInput" name="photo" class="d-none" accept="image/jpeg, image/jpg">
                                        </label>
                                    </div>
                                </div>
                                <div class="col">
                                    <h6 class="fw-bold mb-1">Employee Photo</h6>
                                    <p class="text-muted small mb-0">Upload a professional headshot. <strong>JPEG/JPG only. Max 300KB.</strong></p>
                                </div>
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control" placeholder="e.g. John Doe" required>
                                    <div class="invalid-feedback">Please enter the employee's full name.</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="john.doe@company.com" required>
                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="+1 (555) 000-0000">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Department</label>
                                    <select name="department" class="form-select" required>
                                        <option selected disabled value="">Choose Department...</option>
                                        <option value="Engineering">Engineering</option>
                                        <option value="Design">Design</option>
                                        <option value="Marketing">Marketing</option>
                                        <option value="Finance">Finance</option>
                                        <option value="Human Resources">Human Resources</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a department.</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Position</label>
                                    <input type="text" name="position" class="form-control" placeholder="e.g. Senior Software Engineer">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Joining Date</label>
                                    <input type="date" name="joining_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">Home Address</label>
                                    <textarea name="address" class="form-control" rows="3" placeholder="Enter full address..."></textarea>
                                </div>
                            </div>

                            <div class="alert alert-info border-0 d-flex align-items-center mb-4" style="background-color: #e3f2fd; color: #0d47a1;">
                                <i class="bi bi-info-circle-fill me-3 fs-5"></i>
                                <div class="small">
                                    An onboarding email will be sent automatically to the provided email address once saved.
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 border-top pt-4">
                                <a href="index.php" class="btn btn-light border px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4" id="saveBtn">
                                    <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                                    Save Employee
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // Example of loading state for the save button
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        } else {
            const btn = document.getElementById('saveBtn');
            const spinner = btn.querySelector('.spinner-border');
            spinner.classList.remove('d-none');
            btn.classList.add('disabled');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...';
        }
        this.classList.add('was-validated');
    });
</script>

<?php include '../includes/footer.php'; ?>
