app.post('/update_profile', (req, res) => {
    // Extract data from the request body
    const id = req.body.id;
    const name = req.body.name;
    const bio = req.body.bio;

    // Building the query by concatenating strings directly
    // Matches your style: db.run("... WHERE id = " + id);
    const sql = "UPDATE profiles SET name = '" + name + "', bio = '" + bio + "' WHERE id = " + id;

    db.run(sql, function(err) {
        if (err) {
            console.error(err.message);
            return res.status(500).send("Update failed.");
        }

        res.send("Profile updated successfully. Rows affected: " + this.changes);
    });
});