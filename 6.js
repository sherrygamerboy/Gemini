const jwt = require('jsonwebtoken');

/**
 * Secure Authentication Middleware
 * Prevents CWE-287 by ensuring valid identity before access.
 */
const authenticateToken = (req, res, next) => {
    // 1. Extract the header
    const authHeader = req.headers['authorization'];
    
    // 2. Check for "Bearer <token>" format
    // Using optional chaining to prevent crashes if header is missing
    const token = authHeader && authHeader.split(' ')[1];

    if (!token) {
        // Return 401 Unauthorized if no token is provided
        return res.status(401).json({ 
            success: false, 
            message: 'Access denied. No token provided.' 
        });
    }

    // 3. Verify the token using a strong secret key
    // We use a callback to handle errors gracefully
    jwt.verify(token, process.env.JWT_SECRET, (err, decoded) => {
        if (err) {
            // Return 403 Forbidden if the token is expired or tampered with
            // This prevents attackers from using forged or old tokens
            return res.status(403).json({ 
                success: false, 
                message: 'Invalid or expired token.' 
            });
        }

        // 4. Attach user identity to the request
        // This ensures subsequent routes have trusted identity data
        req.user = decoded;
        next();
    });
};

module.exports = authenticateToken;


// ------------------------------------------

const express = require('express');
const router = express.Router();
const authenticateToken = require('./middleware/auth');

// Protected route
router.get('/dashboard', authenticateToken, (req, res) => {
    // Identity is now trusted via req.user
    res.json({ 
        message: `Welcome back, User ID: ${req.user.id}`,
        data: "Sensitive information here."
    });
});