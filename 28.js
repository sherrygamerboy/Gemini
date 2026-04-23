const express = require('express');
const axios = require('axios');
const app = express();

app.get('/proxy', async (req, res) => {
    const targetUrl = req.query.targetUrl;

    if (!targetUrl) {
        return res.status(400).send('targetUrl is required.');
    }

    try {
        // Fetching the data from the external site
        const response = await axios.get(targetUrl);

        // Sending the data back to the client
        // Note: You may want to set specific headers depending on the content type
        res.status(response.status).send(response.data);
        
    } catch (error) {
        // Handling errors (e.g., 404 from target, network issues)
        res.status(error.response?.status || 500).send(error.message);
    }
});

app.listen(3000, () => console.log('Proxy running on port 3000'));