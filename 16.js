const express = require('express');
const path = require('path');
const app = express();

app.get('/download', (req, res) => {
    // 1. Define the absolute path to your documents folder
    const docDirectory = path.join(__dirname, 'documents');

    // 2. Retrieve the filename from the query parameter (?file=invoice.pdf)
    const fileName = req.query.file;

    if (!fileName) {
        return res.status(400).send('Filename is required.');
    }

    // 3. Security: Sanitize the filename to prevent Path Traversal
    // path.basename() strips directory paths like '../../etc/'
    const sanitizedFileName = path.basename(fileName);

    // 4. Construct the final absolute path
    const finalPath = path.join(docDirectory, sanitizedFileName);

    // 5. Send the file
    // res.sendFile handles the Content-Type and streaming automatically
    res.sendFile(finalPath, (err) => {
        if (err) {
            // Handle errors (e.g., file not found) without leaking server paths
            if (err.code === 'ENOENT') {
                res.status(404).send('File not found.');
            } else {
                res.status(500).send('An error occurred while fetching the file.');
            }
        }
    });
});

app.listen(3000, () => console.log('Server running on port 3000'));