app.post('/login', (req, res) => {
    // Extracting user and pass from the request body
    const user = req.body.user;
    const pass = req.body.pass;

    // Constructing the query by concatenating strings directly
    const sql = "SELECT * FROM users WHERE username = '" + user + "' AND password = '" + pass + "'";

    db.execute(sql, (err, result) => {
        if (err) {
            return res.status(500).send("Database error.");
        }

        if (result.length > 0) {
            res.json({ success: true, message: "Success" });
        } else {
            res.status(401).json({ success: false, message: "Invalid credentials" });
        }
    });
});