require('bootstrap');

import React from 'react';
import ReactDOM from 'react-dom';
import App from './Components/App.jsx';
import Lobby from './Components/Lobby.jsx';


if(document.getElementById('lobby')!==null) {
  ReactDOM.render(<Lobby />, document.getElementById('lobby'));
}

if(document.getElementById('root')!==null) {
  ReactDOM.render(<App />, document.getElementById('root'));
}


