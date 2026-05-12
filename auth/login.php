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
        <div class="login-aside d-none d-lg-flex" style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); overflow: hidden;">
            <!-- Abstract background elements -->
            <div style="position: absolute; top: -10%; right: -10%; width: 400px; height: 400px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -5%; left: -5%; width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            
            <div class="text-center px-5 position-relative">
                <div class="mb-5">
                    <img src="../assets/images/login-illustration.png" alt="Office Illustration" class="login-illustration shadow-lg rounded-4" style="max-height: 400px; border: 12px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                </div>
                <h2 class="fw-800 text-white mb-3">Optimize Your Workforce</h2>
                <p class="text-white-50 fs-5 px-4">Experience a seamless management experience with HR-OS. Modern, fast, and secure.</p>
            </div>
        </div>
        <div class="login-form-container bg-white">
            <div class="mb-5">
                <div class="d-flex align-items-center gap-3 mb-5">
                    <div class="bg-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-people-fill text-white fs-4"></i>
                    </div>
                    <span class="fs-3 fw-800 text-dark">HR-OS</span>
                </div>
                <h3 class="fw-800 mb-2">Welcome Back</h3>
                <p class="text-muted">Enter your credentials to manage your team.</p>

                <?php if (isset($_GET['status']) && $_GET['status'] === 'logged_out'): ?>
                    <div class="alert alert-success border-0 small py-3 mt-4 rounded-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>You have been logged out successfully.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger border-0 small py-3 mt-4 rounded-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
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
                <div class="mb-4">
                    <label for="email" class="form-label small fw-700 text-uppercase tracking-wider text-muted mb-2">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control form-control-lg" id="email" placeholder="name@company.com" required>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="password" class="form-label small fw-700 text-uppercase tracking-wider text-muted mb-0">Password</label>
                        <a href="#" class="small text-decoration-none fw-600">Forgot password?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control form-control-lg" id="password" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember">
                        <label class="form-check-label small text-muted fw-500" for="remember">
                            Remember me for 30 days
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-sm mb-4">
                    Sign In to Dashboard
                </button>

                <div class="text-center">
                    <p class="small text-muted mb-0">Need assistance? <a href="#" class="text-decoration-none fw-600">Contact IT Support</a></p>
                </div>
            </form>

            <div class="mt-auto pt-5 border-top border-light">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="small text-muted mb-0">&copy; 2024 HR-OS Enterprise</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-muted small text-decoration-none">Privacy</a>
                        <a href="#" class="text-muted small text-decoration-none">Terms</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>