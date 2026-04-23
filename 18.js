const express = require('express');
const path = require('path');
const fs = require('fs');

const app = express();

// 1. Define the strictly bounded sandbox directory
const SAFE_ROOT = path.resolve(__dirname, 'protected_docs');

app.get('/fetch-doc', (req, res) => {
    const userInput = req.query.file;

    if (!userInput) {
        return res.status(400).send('File parameter is required.');
    }

    // 2. Resolve the absolute path
    // path.join combines the root with user input
    // path.resolve resolves all '..' or '.' segments into a final absolute path
    const targetPath = path.resolve(SAFE_ROOT, userInput);

    // 3. The Sandbox Check (Critical Security Step)
    // We check if the resolved path actually starts with our SAFE_ROOT.
    // This prevents "..\..\etc\passwd" from resolving to a location outside the sandbox.
    if (!targetPath.startsWith(SAFE_ROOT)) {
        console.error(`Access Denied: Attempted path traversal to ${targetPath}`);
        return res.status(403).send('Access Denied: You cannot access files outside the allowed directory.');
    }

    // 4. Verify existence and serve
    fs.stat(targetPath, (err, stats) => {
        if (err || !stats.isFile()) {
            return res.status(404).send('Document not found.');
        }

        // 5. Use res.sendFile for secure streaming
        res.sendFile(targetPath, (err) => {
            if (err) {
                console.error(err);
                res.status(500).send('Error retrieving file.');
            }
        });
    });
});

app.listen(3000, () => console.log('Secure File Service running on port 3000'));