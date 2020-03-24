import React, { Component } from 'react';

class PlayerList extends Component {
  constructor(props){
    super(props)
  }

  render(){
    return (
      <div>
        <ul className="list-group mb-4 ml-5">
          {
            this.props.turns.map((player, index) => {
              if(player === this.props.activePlayer) {
                return (
                  <li key={index} className="list-group-item active">{player}</li>
                )
              } else {
                return (
                  <li key={index} className="list-group-item">{player}</li>
                )
              }
            })

          }
        </ul>        
      </div>
    )
  }
}

export default PlayerList;


