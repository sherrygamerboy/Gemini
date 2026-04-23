const express = require('express');
const mysql = require('mysql2/promise');
const app = express();

app.use(express.json());

app.patch('/update-email', async (req, res) => {
    const { userId, newEmail } = req.body;

    // Validation: Ensure email follows a valid pattern
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(newEmail)) {
        return res.status(400).json({ error: 'Invalid email format' });
    }

    try {
        const connection = await mysql.createConnection({ /* config */ });
        
        // Using '?' placeholders for parameterized queries
        const [result] = await connection.execute(
            'UPDATE users SET email = ? WHERE id = ?',
            [newEmail.toLowerCase(), userId]
        );

        if (result.affectedRows === 0) {
            return res.status(404).json({ message: 'User not found' });
        }

        res.json({ success: true, message: 'Email updated successfully' });
    } catch (err) {
        res.status(500).json({ error: 'Internal Server Error' });
    }
});