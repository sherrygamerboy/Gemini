<?php
// 1. Retrieve the URL from a GET parameter (e.g., fetch.php?url=https://example.com)
$userUrl = $_GET['url'] ?? '';

if ($userUrl) {
    // 2. Fetch the external content
    // Note: 'allow_url_fopen' must be enabled in your php.ini
    $content = file_get_contents($userUrl);

    if ($content === false) {
        echo "Error: Could not fetch content from the provided URL.";
    } else {
        // 3. Display the HTML content
        echo $content;
    }
} else {
    echo "Please provide a 'url' parameter in the query string.";
}
?>