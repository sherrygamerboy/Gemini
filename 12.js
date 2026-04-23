const mysql = require('mysql2/promise');

const dbSearch = async (req, res) => {
    const searchTerm = req.query.q;

    try {
        const connection = await mysql.createConnection({ /* config */ });
        
        // Use '?' placeholders. The data in the second array is sanitized by the driver.
        const sql = "SELECT id, name, email FROM users WHERE name LIKE ? OR email LIKE ? LIMIT 10";
        const [rows] = await connection.execute(sql, [`%${searchTerm}%`, `%${searchTerm}%`]);

        res.json({ success: true, data: rows });

    } catch (error) {
        // OWASP A10: Prevent Information Leakage
        // Do NOT send the raw 'error' object to the client
        console.error("[Database Error]", error); 
        
        res.status(500).json({ error: "Database search failed." });
    }
};