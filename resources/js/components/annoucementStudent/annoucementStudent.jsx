import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { Editor } from '@tinymce/tinymce-react';

const AnnouncementManagement = () => {
  // Get the user's role from the DOM element data attribute
  const userRole = document.getElementById('announcement-management').dataset.userRole;

  // Determine the department based on the user's role
  let type;
  // if (userRole === 'ADM') {
    type = userRole;
  // } else if (userRole === 'FN') {
  //   type = 'Finance';
  // } else if (userRole === 'AR') {
  //   type = 'Pendaftar Akademik';
  // } else if (userRole === 'RGS') {
  //   type = 'Pendaftar';
  // }

  // State variables
  const [announcements, setAnnouncements] = useState([]); // Stores the list of announcements
  const [showForm, setShowForm] = useState(false); // Controls the visibility of the form
  const [showEditModal, setShowEditModal] = useState(false); // Controls the visibility of the edit modal
  const [newAnnouncement, setNewAnnouncement] = useState({
    title: '',
    content: '',
    start_date: '',
    end_date: '',
    department: type, // Default department set based on user role
    priority: 'low', // Default priority
  });
  const [editAnnouncement, setEditAnnouncement] = useState(null); // Stores the announcement being edited
  const [isLoading, setIsLoading] = useState(false); // Tracks loading state
  const [errorMessage, setErrorMessage] = useState(''); // Stores error messages

  // Fetch announcements from the backend when the component is mounted
  useEffect(() => {
    const fetchAnnouncements = async () => {
      setIsLoading(true); // Set loading to true while fetching data
      try {
        const response = await axios.get('/all/student/announcements/getannoucement'); // API call to fetch announcements
        setAnnouncements(response.data); // Store fetched announcements in state
        setIsLoading(false); // Set loading to false when done
      } catch (error) {
        console.error('Error fetching announcements:', error);
        setErrorMessage('Failed to load announcements.'); // Set error message if API call fails
        setIsLoading(false);
      }
    };
    fetchAnnouncements();
  }, []); // Empty dependency array ensures this runs only once when the component mounts

  // Add a new announcement
  const handleSubmit = async (e) => {
    e.preventDefault(); // Prevent default form submission behavior
    try {
      const response = await axios.post(`/all/student/announcements/post`, newAnnouncement); // API call to create a new announcement
      setAnnouncements([...announcements, { ...newAnnouncement, id: response.data.id }]); // Add the new announcement to the state
      setNewAnnouncement({
        title: '',
        content: '',
        start_date: '',
        end_date: '',
        department: type,
        priority: 'low',
      }); // Reset the form fields
      setShowForm(false); // Hide the form after submission
    } catch (error) {
      console.error('Error creating announcement:', error);
    }
  };

  // Update an existing announcement
  const handleUpdate = async (id, updatedData) => {
    setIsLoading(true); // Set loading to true while updating
    try {
      await axios.put(`/all/student/announcements/put/${id}`, updatedData); // API call to update the announcement
      setAnnouncements(
        announcements.map((announcement) =>
          announcement.id === id ? updatedData : announcement
        )
      ); // Update the announcement in state
      setShowEditModal(false); // Hide the edit modal
      setIsLoading(false); // Set loading to false
    } catch (error) {
      console.error('Error updating announcement:', error);
      setErrorMessage('Failed to update announcement.'); // Set error message if API call fails
      setIsLoading(false);
    }
  };

  // Delete an announcement
  const handleDelete = async (id) => {
    if (window.confirm('Are you sure you want to delete this announcement?')) {
      setIsLoading(true); // Set loading to true while deleting
      try {
        await axios.delete(`/all/student/announcements/delete/${id}`); // API call to delete the announcement
        setAnnouncements(announcements.filter((announcement) => announcement.id !== id)); // Remove the announcement from state
        setIsLoading(false); // Set loading to false
      } catch (error) {
        console.error('Error deleting announcement:', error);
        setErrorMessage('Failed to delete announcement.'); // Set error message if API call fails
        setIsLoading(false);
      }
    }
  };

  // Handle click on edit button
  const handleEditClick = (announcement) => {
    setEditAnnouncement(announcement); // Set the announcement to be edited
    setShowEditModal(true); // Show the edit modal
  };

  // Handle form submission for editing
  const handleEditSubmit = (e) => {
    e.preventDefault();
    handleUpdate(editAnnouncement.id, editAnnouncement); // Call update function with edited data
  };

  // Close the edit modal
  const handleModalClose = () => {
    setEditAnnouncement(null); // Reset the edit announcement
    setShowEditModal(false); // Hide the modal
  };

  return (
    <div className="container py-4">
      <div className="mb-4">
        <div className="d-flex justify-content-between align-items-center mb-4">
          <h1 className="h3">Announcement Management</h1>
          {!showForm && (
            <button onClick={() => setShowForm(true)} className="btn btn-primary">
              Create Announcement {/* Show form on button click */}
            </button>
          )}
        </div>

        {/* Display error message if any */}
        {errorMessage && <div className="alert alert-danger">{errorMessage}</div>}

        {/* Form for creating a new announcement */}
        {showForm && (
          <div className="card mb-4">
            <div className="card-body">
              <h2 className="h4 mb-3">New Announcement</h2>
              <form onSubmit={handleSubmit}>
                <div className="mb-3">
                  <label className="form-label">Title</label>
                  <input
                    type="text"
                    className="form-control"
                    value={newAnnouncement.title}
                    onChange={(e) =>
                      setNewAnnouncement({ ...newAnnouncement, title: e.target.value })
                    }
                    required
                  />
                </div>
                <div className="mb-3">
                  <label className="form-label">Content</label>
                  {/* TinyMCE editor for content */}
                  <Editor
                    apiKey="m87hnvtbh67hlojxi0rtvmck66pxl1t95e28zms4v8qhpn7v"
                    value={newAnnouncement.content}
                    init={{
                      plugins: [
                        'link', 'image', 'table', 'lists', 'media', 'wordcount', 'advtable',
                      ],
                      toolbar:
                        'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent',
                    }}
                    onEditorChange={(content) =>
                      setNewAnnouncement({ ...newAnnouncement, content })
                    }
                  />
                </div>
                <div className="row mb-3">
                  <div className="col-md-6">
                    <label className="form-label">Start Date</label>
                    <input
                      type="date"
                      className="form-control"
                      value={newAnnouncement.start_date}
                      onChange={(e) =>
                        setNewAnnouncement({ ...newAnnouncement, start_date: e.target.value })
                      }
                      required
                    />
                  </div>
                  <div className="col-md-6">
                    <label className="form-label">End Date</label>
                    <input
                      type="date"
                      className="form-control"
                      value={newAnnouncement.end_date}
                      onChange={(e) =>
                        setNewAnnouncement({ ...newAnnouncement, end_date: e.target.value })
                      }
                      required
                    />
                  </div>
                </div>
                <div className="row mb-3">
                  <div className="col-md-6">
                    <label className="form-label">Department</label>
                        <select
                        className="form-select"
                        value={newAnnouncement.department}
                        onChange={(e) =>
                        setNewAnnouncement({ ...newAnnouncement, department: e.target.value })
                        }
                        disabled // This makes the select field read-only
                        >
                        <option value="Admin">Admin</option>
                        <option value="Finance">Finance</option>
                        <option value="Pendaftar Akademik">Pendaftar Akademik</option>
                        <option value="Pendaftar">Pendaftar</option>
                    </select>
                  </div>

                  <div className="col-md-6">
                    <label className="form-label">Priority</label>
                    <select
                      className="form-select"
                      value={newAnnouncement.priority}
                      onChange={(e) =>
                        setNewAnnouncement({ ...newAnnouncement, priority: e.target.value })
                      }
                    >
                      <option value="low">Low</option>
                      <option value="medium">Medium</option>
                      <option value="high">High</option>
                    </select>
                  </div>
                </div>
                <button type="submit" className="btn btn-primary">
                  Publish Announcement
                </button>
              </form>
            </div>
          </div>
        )}
      </div>

      {/* Display loading spinner */}
      {isLoading && <div className="text-center">Loading...</div>}

      {/* Display announcements */}
      <div className="row g-4">
        {announcements.length > 0 ? (
          announcements.map((announcement) => (
            <div key={announcement.id} className="col-12">
              <div
                className={`card border-${
                  announcement.priority === 'high'
                    ? 'danger'
                    : announcement.priority === 'medium'
                    ? 'warning'
                    : 'success'
                }`}
              >
                {/* Announcement details */}
                <div className="card-body">
                  <h3 className="card-title">{announcement.title}</h3>
                  <p
                    className="card-text"
                    dangerouslySetInnerHTML={{ __html: announcement.content }}
                  />
                </div>
              </div>
            </div>
          ))
        ) : (
          <p>No announcements to display.</p>
        )}
      </div>
    </div>
  );
};

export default AnnouncementManagement;
