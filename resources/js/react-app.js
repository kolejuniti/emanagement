import React from 'react';
import ReactDOM from 'react-dom';
import AnnouncementSystem from './components/annoucementStudent/annoucementStudent.jsx';

window.renderAnnouncementSystem = () => {

    const container = document.createElement('div');
    container.id = 'announcementSystem';
    $('#form-student').html(container);

    ReactDOM.render(
        <AnnouncementSystem />,
        document.getElementById('announcementSystem')
    )

}

if (document.getElementById('announcementBanner')) {
    ReactDOM.render(<AnnouncementBanner />, document.getElementById('announcementBanner'));
}