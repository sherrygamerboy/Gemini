const fs = require('fs');

app.get('/view/:name', (req, res) => {
    // Building the path using the requested pattern
    const p = __dirname + '/text_files/' + req.params.name;

    // Reading the file from the generated path
    fs.readFile(p, 'utf8', (err, data) => {
        if (err) {
            console.error(err);
            return res.status(404).send("File not found or error reading file.");
        }

        // Setting content type to plain text and sending data
        res.header('Content-Type', 'text/plain');
        res.send(data);
    });
});