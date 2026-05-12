<?php
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        header("Location: login.php?error=unauthorized");
        exit();
    }
    
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header("Location: login.php?error=empty_fields");
        exit();
    }

    if (login($email, $password)) {
        header("Location: ../index.php");
        exit();
    } else {
        header("Location: login.php?error=invalid");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
