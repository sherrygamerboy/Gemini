<?php
// pdo_config.php
$dsn = "mysql:host=localhost;dbname=app_db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, "db_user", "db_pass", $options);
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("Server Error");
}

//---------------------------------------------------

<?php
require 'pdo_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Identification (Assume user is logged in via session)
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) die("Unauthorized");

    // 2. Input Collection & Sanitization (CWE-20)
    // Trim whitespace to prevent "empty" strings consisting only of spaces
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $bio   = isset($_POST['bio'])   ? trim($_POST['bio'])   : '';

    $errors = [];

    // 3. Strict Validation
    // Validate Email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate Bio length (Business logic constraint)
    if (strlen($bio) > 500) {
        $errors[] = "Bio must be under 500 characters.";
    }

    // 4. Database Sink (Secure Update)
    if (empty($errors)) {
        try {
            $sql = "UPDATE users SET email = :email, bio = :bio WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            
            // Binding values ensures input is treated as data, not code
            $stmt->execute([
                'email' => $email,
                'bio'   => $bio,
                'id'    => $userId
            ]);

            echo "Profile updated successfully.";
        } catch (PDOException $e) {
            error_log($e->getMessage());
            echo "An error occurred during the update.";
        }
    } else {
        // Output errors
        foreach ($errors as $error) {
            echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
        }
    }
}
?>