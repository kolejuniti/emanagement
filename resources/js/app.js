import React from 'react';
import ReactDOM from 'react-dom';
import RemarkComponent from './components/RemarkComponent.jsx';
import AnnouncementSystem from './components/annoucementStudent/annoucementStudent.jsx';


console.log('app.js is loaded');

window.renderNodeComponent = function(data) {
  console.log('renderNodeComponent is called with data:', data);
  const container = document.createElement('div');
  container.id = 'remarkComponent';
  document.getElementById('getModal').innerHTML = ''; // Clear previous content
  document.getElementById('getModal').appendChild(container);

  ReactDOM.render(
    <RemarkComponent data={data} />,
    document.getElementById('remarkComponent')
  );

  $('#uploadModal').modal('show');
};

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
