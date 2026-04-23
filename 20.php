<?php
// Retrieve the username from the URL (e.g., profile.php?username=Alex)
$user = $_GET['username'];

// Following your pattern: echo "<div>".$variable."</div>";
echo "<h1>Welcome to the profile of " . $user . "</h1>";
?>