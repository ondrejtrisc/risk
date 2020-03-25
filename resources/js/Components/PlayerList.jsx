import React, { Component } from 'react';

class PlayerList extends Component {
  constructor(props){
    super(props)
  }

  render(){
    const {userList, activePlayer, turns} = this.props
  
    return (
      <div>
        <ul className="list-group mb-4 ml-5">
          {
            userList.map((user, index) => {
              if(turns[index] === activePlayer) {
                return (
                  <li key={index} className="list-group-item active">{user} {turns[index]} </li>
                )
              } else {
                return (
                <li key={index} className="list-group-item">{user} {turns[index]}</li>
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


