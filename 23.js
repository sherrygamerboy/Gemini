/**
 * RichText Component
 * Renders raw HTML strings into the DOM.
 */
const RichText = ({ content }) => {
  return (
    <div className="blog-post-content">
      {/* Matches the dangerouslySetInnerHTML requirement 
        to render user-provided HTML formatting.
      */}
      <div dangerouslySetInnerHTML={{ __html: content }} />
    </div>
  );
};

export default RichText;