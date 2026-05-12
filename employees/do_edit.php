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
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = sanitize($_POST['phone'] ?? '');
    $department = $_POST['department'] ?? '';
    $position = sanitize($_POST['position'] ?? '');
    $joining_date = $_POST['joining_date'] ?? date('Y-m-d');
    $address = sanitize($_POST['address'] ?? '');
    $status = $_POST['status'] ?? 'active';

    // Validate department whitelist
    $allowed_depts = ['Engineering', 'Design', 'Marketing', 'Finance', 'Human Resources'];
    if (!in_array($department, $allowed_depts)) {
        $department = 'Engineering'; // Fallback
    }

    // Validate status whitelist
    $allowed_status = ['active', 'inactive', 'onboarding'];
    if (!in_array($status, $allowed_status)) {
        $status = 'active'; // Fallback
    }

    if (empty($id) || empty($full_name) || empty($email)) {
        header("Location: edit.php?id=$id&error=empty_fields");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: edit.php?id=$id&error=invalid_email");
        exit();
    }

    $photo_url = null;

    // Handle Photo Upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_name = $_FILES['photo']['name'];
        $file_size = $_FILES['photo']['size'];
        $file_type = $_FILES['photo']['type'];
        
        $allowed_types = ['image/jpeg', 'image/jpg'];
        $max_size = 300 * 1024; // 300KB

        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_type, $allowed_types) || !in_array($ext, ['jpg', 'jpeg'])) {
            header("Location: edit.php?id=$id&error=invalid_file");
            exit();
        }

        if ($file_size > $max_size) {
            header("Location: edit.php?id=$id&error=file_large");
            exit();
        }

        $new_file_name = 'emp_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $upload_dir = '../uploads/';
        $dest_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp, $dest_path)) {
            $photo_url = 'uploads/' . $new_file_name;
            
            // Delete old photo if exists
            $stmt = $pdo->prepare("SELECT photo_url FROM employees WHERE id = ?");
            $stmt->execute([$id]);
            $old_photo = $stmt->fetchColumn();
            if ($old_photo && file_exists('../' . $old_photo)) {
                unlink('../' . $old_photo);
            }
        }
    }

    try {
        if ($photo_url) {
            $sql = "UPDATE employees SET full_name = ?, email = ?, phone = ?, position = ?, department = ?, status = ?, address = ?, joining_date = ?, photo_url = ? WHERE id = ?";
            $params = [$full_name, $email, $phone, $position, $department, $status, $address, $joining_date, $photo_url, $id];
        } else {
            $sql = "UPDATE employees SET full_name = ?, email = ?, phone = ?, position = ?, department = ?, status = ?, address = ?, joining_date = ? WHERE id = ?";
            $params = [$full_name, $email, $phone, $position, $department, $status, $address, $joining_date, $id];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        header("Location: index.php?status=success");
        exit();
    } catch (PDOException $e) {
        header("Location: edit.php?id=$id&error=db_error");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
