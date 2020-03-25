import React, { Component } from 'react';

class PlayerList extends Component {
  constructor(props){
    super(props)
  }

  render(){
    const {userList, activePlayer, turns} = this.props
    const classList = ['danger', 'primary', 'success', 'warning', 'secondary', 'info']
  
    return (
      <div>
        <ul className="list-group mb-4 ml-5">
          {
            userList.map((user, index) => {
              if(turns[index] === activePlayer) {
                return (
                  <li key={index} className={`list-group-item text-${classList[index]}`}>{user} <span className="badge badge-primary ml-3"> Playing </span> </li>
                )
              } else {
                return (
                <li key={index} className={`list-group-item text-${classList[index]}`}>{user}</li>
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


