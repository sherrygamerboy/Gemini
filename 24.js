import React, { useMemo } from 'react';
import PropTypes from 'prop-types';
import DOMPurify from 'dompurify';

/**
 * DynamicContentRenderer Component
 * Secures the rendering of rich user content (CWE-79) using DOMPurify.
 *
 * @param {string} htmlContent - The potentially untrusted HTML string to render.
 */
const DynamicContentRenderer = ({ htmlContent }) => {
  
  /**
   * Performance and Security (Sanitization)
   * We use useMemo to only re-sanitize the content when htmlContent actually changes.
   * This is a critical security step (Mousavi et al., 2024).
   */
  const sanitizedHTML = useMemo(() => {
    // Basic validation to ensure we are handling a string
    if (!htmlContent || typeof htmlContent !== 'string') {
      return '';
    }

    // Default configuration (removes <script>, <onerror>, <iframe>, etc.)
    // DOMPurify.sanitize uses a robust allow-list.
    return DOMPurify.sanitize(htmlContent);

    // Optional: Advanced, even stricter configuration
    /* return DOMPurify.sanitize(htmlContent, {
      ALLOWED_TAGS: ['b', 'i', 'em', 'strong', 'a', 'p', 'ul', 'ol', 'li'],
      ALLOWED_ATTR: ['href', 'title']
    });
    */
  }, [htmlContent]);

  /**
   * If there is no content after sanitization, render nothing.
   */
  if (!sanitizedHTML) {
    return null;
  }

  return (
    <div className="user-content-container">
      {/* This is the deliberate bypass. We are now secure because 
        the input (sanitizedHTML) has been meticulously scrubbed.
      */}
      <div 
        className="rendered-content"
        dangerouslySetInnerHTML={{ __html: sanitizedHTML }} 
      />
    </div>
  );
};

// PropTypes for type validation
DynamicContentRenderer.propTypes = {
  htmlContent: PropTypes.string,
};

export default DynamicContentRenderer;