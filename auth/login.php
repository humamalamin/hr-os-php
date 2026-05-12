<?php require_once '../includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HR Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
</head>

<body class="login-v2-body">
    <div class="login-v2-card">
        <div class="login-v2-logo-container">
            <div class="login-v2-icon">
                <i class="bi bi-person-vcard"></i>
            </div>
            <h1 class="login-v2-brand">HR<span>OS</span></h1>
            <p class="login-v2-subtitle">Secure access to the Management System</p>
        </div>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'logged_out'): ?>
            <div class="alert alert-success border-0 small py-3 mb-4 rounded-3 bg-success bg-opacity-10 text-success" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>You have been logged out successfully.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger border-0 small py-3 mb-4 rounded-3 bg-danger bg-opacity-10 text-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php
                if ($_GET['error'] === 'invalid') echo 'Invalid email or password.';
                elseif ($_GET['error'] === 'unauthorized') echo 'Please login to access the dashboard.';
                else echo 'An error occurred. Please try again.';
                ?>
            </div>
        <?php endif; ?>

        <form action="do_login.php" method="POST" class="login-v2-form">
            <?php csrf_input(); ?>
            <div class="mb-4">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="name@company.com" required>
            </div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <label for="password" class="form-label">Security Password</label>
                    <a href="#" class="login-v2-forgot mb-2">Forgot?</a>
                </div>
                <input type="password" name="password" class="form-control" id="password" placeholder="••••••••" required>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label login-v2-checkbox-label" for="remember">
                        Keep me signed in for 30 days
                    </label>
                </div>
            </div>

            <button type="submit" class="login-v2-btn">
                Sign In to Dashboard <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <div class="login-v2-footer">
            <p>&copy; 2026 Internal System. All rights reserved.</p>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>