<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = "uploads/";
    
    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Get file info
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // 1. Basic validation
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // 2. Check if file already exists
            if (file_exists($targetFilePath)) {
                echo "File already exists.";
            } else {
                // 3. Move file to the uploads folder
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                    echo "The file " . htmlspecialchars($fileName) . " has been uploaded.";
                    echo "<br><img src='$targetFilePath' width='200' />";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        } else {
            echo "File is not an image.";
        }
    }
}
?>

<form action="upload.php" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="image" id="image">
    <input type="submit" value="Upload Image" name="submit">
</form>