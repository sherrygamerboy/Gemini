app.get('/check-status', (req, res) => {
    // Matches your style: fetch(req.query.site)
    const site = req.query.site;

    fetch(site)
        .then(response => {
            if (response.ok) {
                res.send(`The website ${site} is ONLINE. Status: ${response.status}`);
            } else {
                res.send(`The website ${site} is reachable but returned an error. Status: ${response.status}`);
            }
        })
        .catch(error => {
            res.status(500).send(`The website ${site} is OFFLINE or unreachable. Error: ${error.message}`);
        });
});