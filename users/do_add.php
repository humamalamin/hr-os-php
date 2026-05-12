<?php
require_once '../includes/auth.php';
require_once '../config/database.php';
checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        header("Location: index.php?error=db_error");
        exit();
    }

    $name = sanitize($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $role = $_POST['role'] ?? 'viewer';
    $password = $_POST['password'] ?? '';

    // Validate role whitelist
    $allowed_roles = ['admin', 'manager', 'editor', 'viewer'];
    if (!in_array($role, $allowed_roles)) {
        $role = 'viewer';
    }

    // Simple validation
    if (empty($name) || empty($email) || empty($password)) {
        header("Location: index.php?error=empty_fields");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?error=invalid_email");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO users (name, email, role, password) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $role, $hashed_password]);

        header("Location: index.php?status=success");
        exit();
    } catch (PDOException $e) {
        // Check for duplicate email
        if ($e->getCode() == 23000) {
            header("Location: index.php?error=email_exists");
        } else {
            header("Location: index.php?error=db_error");
        }
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
