const axios = require('axios');
const dns = require('dns').promises;
const { ipaddr } = require('ipaddr.js'); // Common library for IP validation

/**
 * Secure Fetcher
 * Implements defenses against SSRF and Malicious Redirects.
 */
async function secureApiFetch(targetUrl) {
    const url = new URL(targetUrl);

    // 1. Protocol Validation
    // OWASP: Always enforce HTTPS to prevent protocol smuggling.
    if (url.protocol !== 'https:') {
        throw new Error('Insecure protocol. Only HTTPS is permitted.');
    }

    // 2. DNS Resolution & Internal IP Restriction
    // We resolve the hostname to an IP before making the request.
    const { address } = await dns.lookup(url.hostname);
    
    if (isInternalIP(address)) {
        throw new Error('Access to internal network addresses is prohibited.');
    }

    // 3. Request Configuration
    const response = await axios({
        method: 'get',
        url: targetUrl,
        timeout: 5000, // Prevent DoS via slow loris attacks
        maxContentLength: 10 * 1024 * 1024, // 10MB limit
        
        // 4. Prevent Malicious Redirects (OWASP Top 10:2025)
        // By setting maxRedirects to 0, we prevent the attacker from 
        // redirecting a "safe" URL to an internal one (e.g., localhost).
        maxRedirects: 0, 
        
        // Ensure the underlying request uses the validated IP
        // (Advanced: Use a custom http agent to pin the resolved IP)
    });

    return response.data;
}

/**
 * Helper to check if an IP is private/internal
 */
function isInternalIP(ip) {
    // Blocks 127.0.0.1, 10.x.x.x, 172.16.x.x, 192.168.x.x, 
    // and Cloud Metadata IPs (169.254.169.254)
    const privateRanges = [
        /^127\./, /^10\./, /^172\.(1[6-9]|2[0-9]|3[0-1])\./, 
        /^192\.168\./, /^169\.254\./, /^::1$/, /^fe80:/
    ];
    return privateRanges.some(range => range.test(ip));
}

module.exports = { secureApiFetch };