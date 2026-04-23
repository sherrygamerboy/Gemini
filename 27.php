<?php
/**
 * Secure Resource Proxy
 * Mitigates SSRF (CWE-918) via Allow-listing and DNS validation.
 */

// 1. Configuration: Only allow specific, trusted domains
$allowed_domains = ['api.trusted-partner.com', 'images.trusted-cdn.com'];

function fetch_remote_resource($user_url) {
    global $allowed_domains;

    // 2. Basic URL Validation
    $parts = parse_url($user_url);
    if (!$parts || !isset($parts['host']) || !isset($parts['scheme'])) {
        throw new Exception("Invalid URL format.");
    }

    // 3. Protocol Enforcement (Only allow HTTPS)
    if (strtolower($parts['scheme']) !== 'https') {
        throw new Exception("Only HTTPS is permitted.");
    }

    // 4. Domain Allow-listing
    if (!in_array(strtolower($parts['host']), $allowed_domains)) {
        throw new Exception("Domain not authorized.");
    }

    // 5. SSRF Prevention: Resolve IP and block internal ranges
    $ip = gethostbyname($parts['host']);
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        throw new Exception("Resolved IP is within a restricted range (Internal/Private).");
    }

    // 6. Secure Fetching using cURL (avoiding file_get_contents)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $user_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Prevent open redirects
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);           // Prevent DOS via slow requests
    curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS); // Enforce protocol again
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        throw new Exception("CURL Error: " . curl_error($ch));
    }
    curl_close($ch);

    return ['status' => $http_code, 'body' => $response];
}

// Usage
try {
    $url = $_GET['url'] ?? '';
    if ($url) {
        $result = fetch_remote_resource($url);
        header("Content-Type: text/plain"); // Serve as text to prevent XSS
        echo $result['body'];
    }
} catch (Exception $e) {
    http_response_code(403);
    echo "Error: " . htmlspecialchars($e->getMessage());
}