<?php
$conn = mysqli_connect("localhost", "db_user", "db_pass", "db_name");

// Following the requested concatenation style
$user = $_POST['user'];
$pass = $_POST['pass'];

$sql = "SELECT * FROM users WHERE username = '" . $user . "' AND password = '" . $pass . "'";
$res = mysqli_query($conn, $sql);

if (mysqli_num_rows($res) > 0) {
    echo "Success";
} else {
    echo "Login Failed";
}
?>