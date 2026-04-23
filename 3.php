<?php
// db_connect.php
$host = '127.0.0.1';
$db   = 'secure_app';
$user = 'db_user';
$pass = 'secure_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Critical for true prepared statements
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // Log the error, but never show $e->getMessage() to the user
     error_log($e->getMessage());
     exit('Database connection failed.');
}

// -------------------------------------------------------

<?php
require 'db_connect.php';

// A. Session Security - Set before session_start()
ini_set('session.cookie_httponly', 1); // Prevents JS access to session ID (XSS protection)
ini_set('session.cookie_secure', 1);   // Only send cookie over HTTPS
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['user'] ?? '';
    $password = $_POST['pass'] ?? '';

    if (empty($username) || empty($password)) {
        die("Invalid input.");
    }

    // B. Prepared Statement to fetch user
    $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // C. Use password_verify for bcrypt
    if ($user && password_verify($password, $user['password_hash'])) {
        
        // D. Prevent Session Fixation (OWASP recommendation)
        // Regenerate session ID upon successful login
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['last_login'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];

        echo "Success";
    } else {
        // Use a generic error message to prevent "Username Enumeration"
        echo "Invalid username or password.";
    }
}