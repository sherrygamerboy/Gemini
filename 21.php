<?php
// 1. Implementation of a Content Security Policy (CSP) Header
// This policy tells the browser: 
// - default-src 'self': Only allow content from our own domain.
// - script-src 'self': Disable inline scripts and only allow .js files from our domain.
// - object-src 'none': Disable plugins like Flash.
header("Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none';");

// Mock database results for demonstration
$comments = [
    ['username' => 'Alice', 'body' => 'Great post!'],
    ['username' => 'BadActor', 'body' => "<script>alert('XSS Success')</script> I am a hacker!"],
    ['username' => 'Bob', 'body' => 'Check out my site <a href="http://example.com">here</a>']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Comment Section</title>
    <style>
        .comment { border-bottom: 1px solid #ccc; padding: 10px; margin-bottom: 10px; }
        .user { font-weight: bold; color: #2c3e50; }
    </style>
</head>
<body>

    <h1>User Comments</h1>

    <div id="comment-section">
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <span class="user">
                    <?php echo htmlspecialchars($comment['username'], ENT_QUOTES, 'UTF-8'); ?>
                </span>:
                <p>
                    <?php 
                        // htmlspecialchars converts < into &lt; and > into &gt;
                        // ENT_QUOTES ensures both single and double quotes are escaped.
                        echo htmlspecialchars($comment['body'], ENT_QUOTES, 'UTF-8'); 
                    ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>