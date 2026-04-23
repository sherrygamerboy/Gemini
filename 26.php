<?php
// Retrieve the user-provided URL from the query string
$url = $_GET['url'];

// Building the command following the shell_exec pattern
// Note: We're adding --head to fetch only the metadata/headers
$command = "curl --head " . $url;

// Execute the command and capture the output
$metadata = shell_exec($command);

// Display the results
echo "<h1>Link Metadata:</h1>";
echo "<pre>" . $metadata . "</pre>";
?>