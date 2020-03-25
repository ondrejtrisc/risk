import React, { Component } from 'react';

class AttackCard extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    const {firstTerritory, attackerDice, defenderDice} = this.props
    return (
        <div className="card ml-5 mb-4">
          <div className="card-body">
            <h5 className="card-title">Attack phase</h5>
            <p>
              {(firstTerritory === '') ? "Choose territory from where to attack" : `Choose territory to attack from ${firstTerritory.charAt(0).toUpperCase() + firstTerritory.slice(1)}`}
            </p>
              {(attackerDice !== null) ? <div>
                    Your rolls: {attackerDice}
                    Defender's rolls: {defenderDice}
                  </div> : ''
                
                  
                
              }
          </div>
        </div>
    )
  }
}

export default AttackCard;