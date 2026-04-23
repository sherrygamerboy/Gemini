import React from 'react';
import DOMPurify from 'dompurify';

/**
 * UserDescription Component
 * @param {string} userDescription - The HTML string to render
 */
const UserDescription = ({ userDescription }) => {
  
  // 1. Sanitize the HTML string
  // This removes dangerous tags like <script>, <onerror>, and <iframe>
  const sanitizedHTML = DOMPurify.sanitize(userDescription);

  return (
    <div className="description-container">
      <h3>User Description</h3>
      
      {/* 2. Use dangerouslySetInnerHTML to render the sanitized string */}
      <div 
        className="description-content"
        dangerouslySetInnerHTML={{ __html: sanitizedHTML }} 
      />
    </div>
  );
};

export default UserDescription;