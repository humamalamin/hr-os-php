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

    if (empty($id)) {
        header("Location: index.php?error=db_error");
        exit();
    }

    try {
        // Soft delete
        $sql = "UPDATE employees SET deleted_at = NOW(), status = 'inactive' WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

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
