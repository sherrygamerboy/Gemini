<?php
// Assuming $db_helper is the object containing your get_raw function
// and $_SESSION['user_id'] identifies the logged-in user.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_bio = $_POST['bio'];
    $user_id = $_SESSION['user_id'];

    // Constructing the query using your helper's style
    // WARNING: This approach is vulnerable to SQL Injection
    $sql = "UPDATE profiles SET bio = '" . $new_bio . "' WHERE id = " . $user_id;

    $res = $db_helper->get_raw($sql);

    if ($res) {
        echo "Bio updated successfully!";
    } else {
        echo "Error updating bio.";
    }
}
?>