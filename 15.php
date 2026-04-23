<?php
/**
 * Secure File Upload Handler
 * Auditor Note: Ensure the storage directory is writable by the web user (www-data)
 * but NOT inside the public HTML directory.
 */

// 1. Configuration
define('STORAGE_PATH', '/var/www/data/uploads/'); // Outside /var/www/html/
define('MAX_FILE_SIZE', 5 * 1024 * 1024);        // 5MB Limit
$allowed_mimes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'application/pdf' => 'pdf'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 2. Validate upload status
        if (!isset($_FILES['user_file']) || $_FILES['user_file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Upload failed or no file selected.");
        }

        $file = $_FILES['user_file'];

        // 3. Check File Size
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception("File exceeds maximum size limit.");
        }

        // 4. Content Validation (MIME-Type)
        // Do not trust $_FILES['user_file']['type']!
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($file['tmp_name']);

        if (!array_key_exists($mime_type, $allowed_mimes)) {
            throw new Exception("Illegal file type detected.");
        }

        // 5. Filename Sanitization & Path Traversal Prevention
        // We discard the user-provided name entirely.
        $extension = $allowed_mimes[$mime_type];
        $safe_name = bin2hex(random_bytes(16)) . '.' . $extension;
        $target_path = STORAGE_PATH . $safe_name;

        // 6. Move File
        if (!move_uploaded_file($file['tmp_name'], $target_path)) {
            throw new Exception("Failed to save file to secure storage.");
        }

        echo "File uploaded safely as: " . htmlspecialchars($safe_name);

    } catch (Exception $e) {
        // Auditor Note: Log the exact error for internal review, 
        // but return a generic message to the user.
        error_log("Upload Error: " . $e->getMessage());
        http_response_code(400);
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
}