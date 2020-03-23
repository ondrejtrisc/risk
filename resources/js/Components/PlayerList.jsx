import React, { Component } from 'react';

class PlayerList extends Component {

  render(){
    return (
      <div>
        <ul className="list-group mb-4 ml-5">
          <li className="list-group-item active">Player 1</li>
          <li className="list-group-item">Player 2</li>
          <li className="list-group-item">Player 3</li>
          <li className="list-group-item">Player 4</li>
          <li className="list-group-item">Player 5</li>
        </ul>        
      </div>
    )
  }
}

export default PlayerList;


