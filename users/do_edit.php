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

    $id = $_POST['id'] ?? '';
    $name = sanitize($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $role = $_POST['role'] ?? 'viewer';
    $password = $_POST['password'] ?? '';

    // Validate role whitelist
    $allowed_roles = ['admin', 'manager', 'editor', 'viewer'];
    if (!in_array($role, $allowed_roles)) {
        $role = 'viewer';
    }

    if (empty($id) || empty($name) || empty($email)) {
        header("Location: index.php?error=empty_fields");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?error=invalid_email");
        exit();
    }

    try {
        // Check if email is already taken by ANOTHER user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ? AND deleted_at IS NULL");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            header("Location: index.php?error=email_exists");
            exit();
        }

        if (!empty($password)) {
            // Update with password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name = ?, email = ?, role = ?, password = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $email, $role, $hashed_password, $id]);
        } else {
            // Update without password
            $sql = "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $email, $role, $id]);
        }

        header("Location: index.php?status=success");
        exit();
    } catch (PDOException $e) {
        header("Location: index.php?error=db_error");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
