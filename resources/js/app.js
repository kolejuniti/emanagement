import React from 'react';
import ReactDOM from 'react-dom';
import RemarkComponent from './components/RemarkComponent.jsx';
import './react-app.js';


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
