<?php
session_start();

// Define Base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$base_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// If we are in a subdirectory like /auth or /users, remove the last segment
if (preg_match('/\/(auth|users|employees)$/', $base_dir)) {
    $base_url = $protocol . "://" . $host . preg_replace('/\/(auth|users|employees)$/', '', $base_dir) . "/";
} else {
    $base_url = $protocol . "://" . $host . rtrim($base_dir, '/') . "/";
}
define('BASE_URL', $base_url);

/**
 * Simple Authentication Check
 * 
 * In a real production environment with Redis:
 * ini_set('session.save_handler', 'redis');
 * ini_set('session.save_path', 'tcp://127.0.0.1:6379');
 */

// Define protected pages logic
function checkAuth() {
    // If user is not logged in, redirect to login page
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "auth/login.php?error=unauthorized");
        exit();
    }
}

// Enhanced Function to handle login (using database)
function login($email, $password) {
    global $pdo;
    
    if (!isset($pdo)) {
        require_once __DIR__ . '/../config/database.php';
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
    } catch (PDOException $e) {
        return false;
    }
    
    return false;
}

// Enhanced Function to logout
function logout() {
    // Unset all session variables
    $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finally, destroy the session.
    session_destroy();
    
    header("Location: " . BASE_URL . "auth/login.php?status=logged_out");
    exit();
}

/**
 * CSRF Protection
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_input() {
    $token = generate_csrf_token();
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Input Sanitization
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}
?>
