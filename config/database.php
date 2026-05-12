<?php
/**
 * Database Configuration and Connection
 */

// Load .env if possible or use defaults
// For simplicity in this native project, we'll use a helper to read .env
function getEnvValue($key, $default = null) {
    static $env = null;
    if ($env === null) {
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                list($name, $value) = explode('=', $line, 2);
                $env[trim($name)] = trim($value);
            }
        } else {
            $env = [];
        }
    }
    return $env[$key] ?? $default;
}

$host = getEnvValue('DB_HOST', '127.0.0.1');
$db   = getEnvValue('DB_DATABASE', 'hr_management');
$user = getEnvValue('DB_USERNAME', 'hr_user');
$pass = getEnvValue('DB_PASSWORD', 'hr_password');
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
