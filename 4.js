const express = require('express');
const router = express.Router();
const bcrypt = require('bcrypt');
const db = require('./db'); // Your database connection module

router.post('/login', async (req, res) => {
    try {
        const { email, password } = req.body;

        // 1. Basic Validation
        if (!email || !password) {
            return res.status(400).json({ 
                success: false, 
                message: 'Email and password are required.' 
            });
        }

        // 2. Lookup user by email
        // We use parameterized queries to prevent SQL Injection
        const userQuery = 'SELECT * FROM users WHERE email = $1';
        const result = await db.query(userQuery, [email.toLowerCase()]);
        const user = result.rows[0];

        if (!user) {
            // Generic message to prevent account enumeration
            return res.status(401).json({ 
                success: false, 
                message: 'Invalid credentials.' 
            });
        }

        // 3. Compare the provided password with the hashed password in the DB
        const isMatch = await bcrypt.compare(password, user.password_hash);

        if (!isMatch) {
            return res.status(401).json({ 
                success: false, 
                message: 'Invalid credentials.' 
            });
        }

        // 4. Success! (Usually you would sign a JWT or create a session here)
        return res.status(200).json({ 
            success: true, 
            message: 'Login successful',
            user: { id: user.id, email: user.email } 
        });

    } catch (error) {
        console.error('Login error:', error);
        return res.status(500).json({ 
            success: false, 
            message: 'An internal server error occurred.' 
        });
    }
});

module.exports = router;