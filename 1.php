<?php
// Database configuration
$host     = 'localhost';
$db_name  = 'your_database';
$db_user  = 'your_username';
$db_pass  = 'your_password';

// 1. Establish the connection using MySQLi
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Retrieve credentials from POST request
$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';

if (!empty($user) && !empty($pass)) {
    
    // 3. Prepare the SQL statement to find the user
    // We only select by username first to verify the hashed password later
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // 4. Verify the password against the hash stored in the DB
        // Assumes you stored passwords using password_hash()
        if (password_verify($pass, $row['password'])) {
            echo "Success";
        } else {
            echo "Invalid credentials.";
        }
    } else {
        echo "Invalid credentials.";
    }

    $stmt->close();
} else {
    echo "Please provide both username and password.";
}

$conn->close();
?>