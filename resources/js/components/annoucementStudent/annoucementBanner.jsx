import React, { useState, useEffect } from 'react';
import axios from 'axios';

const AnnouncementBanner = () => {
  // State to store announcements fetched from the backend
  const [announcements, setAnnouncements] = useState([]);
  
  // State to track which announcement is currently displayed
  const [currentIndex, setCurrentIndex] = useState(0);

  // useEffect runs when the component is mounted to fetch announcements
  useEffect(() => {
    const fetchAnnouncements = async () => {
      try {
        // Axios GET request to fetch announcements from the backend API
        const response = await axios.get('all/student/announcements/getBannerAnnouncement');
        setAnnouncements(response.data); // Save fetched announcements to state
      } catch (error) {
        // Log any errors encountered during the fetch operation
        console.error('Error fetching announcements:', error);
      }
    };

    fetchAnnouncements(); // Call the function to fetch announcements
  }, []); // Empty dependency array ensures this only runs once when the component mounts

  // Function to navigate to the next announcement
  const nextAnnouncement = () => {
    // The modulus operator ensures we loop back to the first announcement after the last one
    setCurrentIndex((prev) => (prev + 1) % announcements.length);
    /**
     * Explanation:
     * - `prev` is the current index before the update.
     * - `(prev + 1)` increments the index.
     * - `% announcements.length` ensures the index loops back to 0 when it reaches the end.
     * - For example:
     *    - If `prev = 2` and `announcements.length = 3`, `(2 + 1) % 3 = 0`.
     *    - This prevents an "out-of-bounds" error and creates a looping effect.
     */
  };

  // Function to navigate to the previous announcement
  const prevAnnouncement = () => {
    setCurrentIndex((prev) => (prev - 1 + announcements.length) % announcements.length);
    /**
     * Explanation:
     * - `(prev - 1)` decrements the current index.
     * - Adding `announcements.length` ensures the result doesn't become negative.
     * - `% announcements.length` ensures the index loops back to the last item if `prev` is 0.
     * - For example:
     *    - If `prev = 0` and `announcements.length = 3`, `(0 - 1 + 3) % 3 = 2`.
     *    - This moves the index to the last announcement in a circular manner.
     */
  };

  // Function to get dynamic styles based on the priority of an announcement
  const getPriorityStyles = (priority) => {
    switch (priority) {
      case 'high': // High-priority announcements
        return {
          backgroundColor: '#ffe5e5', // Light red background
          color: '#d32f2f', // Dark red text
          borderColor: '#f44336', // Red border
        };
      case 'medium': // Medium-priority announcements
        return {
          backgroundColor: '#fff7e5', // Light orange background
          color: '#f57c00', // Orange text
          borderColor: '#ff9800', // Orange border
        };
      case 'low': // Low-priority announcements
        return {
          backgroundColor: '#e5f7ff', // Light blue background
          color: '#0288d1', // Blue text
          borderColor: '#03a9f4', // Blue border
        };
      default: // Default styles for unknown priorities
        return {
          backgroundColor: '#f9f9f9', // Light gray background
          color: '#757575', // Gray text
          borderColor: '#bdbdbd', // Gray border
        };
    }
  };

  // Display a loading message while announcements are being fetched
  if (announcements.length === 0) {
    return <p>Loading announcements...</p>;
  }

  // Main component rendering
  return (
    <div
      style={{
        marginTop: '24px', // Space above the banner
        marginBottom: '24px', // Space below the banner
        backgroundColor: 'white', // White background
        borderRadius: '12px', // Rounded corners
        boxShadow: '0 4px 12px rgba(0, 0, 0, 0.1)', // Box shadow for depth
        overflow: 'hidden', // Prevent content overflow
      }}
    >
      {/* Header section with title and navigation buttons */}
      <div
        style={{
          display: 'flex', // Arrange items in a row
          justifyContent: 'space-between', // Space out items
          alignItems: 'center', // Align items vertically
          padding: '16px', // Padding inside the header
          backgroundColor: '#f4f4f4', // Light gray background
          borderBottom: '1px solid #e0e0e0', // Bottom border
        }}
      >
        {/* Title and icon */}
        <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
          <span style={{ fontSize: '20px' }}>📢</span> {/* Announcement icon */}
          <h3 style={{ margin: 0, fontWeight: '600', fontSize: '18px', color: '#333' }}>
            Announcements
          </h3>
        </div>
        {/* Navigation buttons */}
        <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
          <button
            onClick={prevAnnouncement} // Move to the previous announcement
            style={{
              padding: '8px', // Button size
              backgroundColor: '#e0e0e0', // Gray background
              borderRadius: '50%', // Circular button
              border: 'none', // Remove border
              cursor: 'pointer', // Change cursor to pointer
              transition: 'all 0.3s ease', // Smooth transition effect
            }}
            aria-label="Previous announcement"
          >
            ←
          </button>
          {/* Display the current announcement index */}
          <span style={{ fontSize: '14px', color: '#555' }}>
            {currentIndex + 1} / {announcements.length}
          </span>
          <button
            onClick={nextAnnouncement} // Move to the next announcement
            style={{
              padding: '8px',
              backgroundColor: '#e0e0e0',
              borderRadius: '50%',
              border: 'none',
              cursor: 'pointer',
              transition: 'all 0.3s ease',
            }}
            aria-label="Next announcement"
          >
            →
          </button>
        </div>
      </div>

      {/* Announcement content section */}
      <div
        style={{
          margin: '16px', // Space around the content
          padding: '16px', // Padding inside the content box
          borderRadius: '8px', // Rounded corners
          border: '1px solid', // Border style
          ...getPriorityStyles(announcements[currentIndex].priority), // Apply priority styles dynamically
        }}
      >
        {/* Announcement title */}
        <h4
          style={{
            fontWeight: '600', // Bold text
            fontSize: '16px', // Font size
            marginBottom: '8px', // Space below the title
          }}
        >
          {announcements[currentIndex].title}
        </h4>

        {/* Department Badge */}
        <span
          style={{
            display: 'inline-block', // Inline block for consistent spacing
            backgroundColor: '#e0f7fa', // Light teal background
            color: '#00796b', // Dark teal text
            fontSize: '12px', // Small font size
            fontWeight: '500', // Semi-bold text
            borderRadius: '12px', // Rounded corners
            padding: '4px 8px', // Padding inside the badge
            marginBottom: '12px', // Space below the badge
          }}
        >
          {announcements[currentIndex].department} {/* Dynamic department name */}
        </span>

        {/* Content section (supports HTML rendering) */}
        <div
          style={{
            fontSize: '14px', // Font size for content
            color: '#555', // Text color
            marginBottom: '12px', // Space below the content
          }}
          dangerouslySetInnerHTML={{
            __html: announcements[currentIndex].content, // Render HTML safely
          }}
        />

        {/* Posted date */}
        <p
          style={{
            fontSize: '12px', // Small text size
            color: '#888', // Light gray text
          }}
        >
          Posted: {new Date(announcements[currentIndex].created_at).toLocaleDateString()} {/* Format date */}
        </p>
      </div>
    </div>
  );
};

export default AnnouncementBanner;
