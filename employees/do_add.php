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

    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = sanitize($_POST['phone'] ?? '');
    $department = $_POST['department'] ?? '';
    $position = sanitize($_POST['position'] ?? '');
    $joining_date = $_POST['joining_date'] ?? date('Y-m-d');
    $address = sanitize($_POST['address'] ?? '');
    $status = 'active'; // Default for new employee

    // Validate department whitelist
    $allowed_depts = ['Engineering', 'Design', 'Marketing', 'Finance', 'Human Resources'];
    if (!in_array($department, $allowed_depts)) {
        $department = 'Engineering'; // Fallback
    }

    if (empty($full_name) || empty($email)) {
        header("Location: add.php?error=empty_fields");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: add.php?error=invalid_email");
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
            header("Location: add.php?error=invalid_file");
            exit();
        }

        if ($file_size > $max_size) {
            header("Location: add.php?error=file_large");
            exit();
        }

        $new_file_name = 'emp_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $upload_dir = '../uploads/';
        $dest_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp, $dest_path)) {
            $photo_url = 'uploads/' . $new_file_name;
        }
    }

    // Generate a simple employee ID
    $employee_id = 'EMP-' . rand(10000, 99999);

    try {
        $sql = "INSERT INTO employees (employee_id, full_name, email, phone, position, department, status, address, joining_date, photo_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$employee_id, $full_name, $email, $phone, $position, $department, $status, $address, $joining_date, $photo_url]);

        $data = json_encode([
            'message' => 'New employee added',
            'type' => 'success'
        ]);

        $options = [
            'http' => [
                'header'  => "Content-type: application/json",
                'method'  => 'POST',
                'content' => $data,
            ],
        ];

        $context = stream_context_create($options);

        file_get_contents(
            'http://localhost:3001/notify',
            false,
            $context
        );

        header("Location: index.php?status=success");
        exit();
    } catch (PDOException $e) {
        // In real app, log the error
        header("Location: add.php?error=db_error");
        exit();
    }
} else {
    header("Location: add.php");
    exit();
}
