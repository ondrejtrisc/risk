import React, { Component } from 'react';

class PlayerList extends Component {
  constructor(props){
    super(props)
  }

  render(){
    const {userList, activePlayer, turns, territories} = this.props
    const classList = ['danger', 'primary', 'success', 'warning', 'secondary', 'info']
  
    return (
      <div>
        <ul className="list-group mb-4 ml-5">
          {
            userList.map((user, index) => {
              if(turns[index] === activePlayer) {
                return (
                  <li 
                    key={index} 
                    className={`list-group-item mb-1 border border-${classList[index]} rounded`}>{user} 
                      <span className="badge badge-primary text-uppercase ml-3"> Playing </span>
                      {

                      } 
                  </li>
                )
              } else {
                return (
                <li 
                  key={index} 
                  className={`list-group-item mb-1 border border-${classList[index]} rounded`}>{user}
                  </li>
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


