<?php require_once '../includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HR Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-aside d-none d-lg-flex">
            <div class="text-center">
                <img src="../assets/images/login-illustration.png" alt="Office Illustration" class="login-illustration mb-4" style="max-height: 400px;">
                <h4 class="fw-bold">Welcome to HR-OS</h4>
                <p class="text-muted">The internal operating system for our workforce.</p>
            </div>
        </div>
        <div class="login-form-container">
            <div class="mb-5">
                <h5 class="fw-bold mb-1">Sign In</h5>
                <p class="text-muted small">Enter your credentials to access the dashboard.</p>

                <?php if (isset($_GET['status']) && $_GET['status'] === 'logged_out'): ?>
                    <div class="alert alert-success border-0 small py-2 mt-3" role="alert">
                        You have been logged out successfully.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger border-0 small py-2 mt-3" role="alert">
                        <?php
                        if ($_GET['error'] === 'invalid') echo 'Invalid email or password.';
                        elseif ($_GET['error'] === 'unauthorized') echo 'Please login to access the dashboard.';
                        else echo 'An error occurred. Please try again.';
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <form action="do_login.php" method="POST">
                <?php csrf_input(); ?>
                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control form-control-lg" id="email" placeholder="name@company.com" required>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label small fw-semibold">Password</label>
                        <a href="#" class="small text-decoration-none">Forgot password?</a>
                    </div>
                    <input type="password" name="password" class="form-control form-control-lg" id="password" placeholder="••••••••" required>
                </div>
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember">
                        <label class="form-check-label small text-muted" for="remember">
                            Remember me on this device
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100 mb-4">
                    Sign In
                </button>

                <div class="text-center">
                    <p class="small text-muted mb-0">Need help? <a href="#" class="text-decoration-none">Contact IT Support</a></p>
                </div>
            </form>

            <div class="mt-auto pt-5">
                <p class="small text-muted mb-0">&copy; 2024 Enterprise Solutions Inc.</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>