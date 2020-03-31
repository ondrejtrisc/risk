require('popper.js');

require('bootstrap');


import React from 'react';
import ReactDOM from 'react-dom';
import App from './Components/App.jsx';


if(document.getElementById('root')!==null) {
  ReactDOM.render(<App />, document.getElementById('root'));
}


