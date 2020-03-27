import React, { Component } from 'react';
import validate from '../Functions/validate'
import '../images/side1.png'
import '../images/side2.png'
import '../images/side3.png'
import '../images/side4.png'
import '../images/side5.png'
import '../images/side6.png'


class AttackCard extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    const {firstTerritory, attackerDice, defenderDice} = this.props
    if(attackerDice !== null) {
      attackerDice.sort(function(a, b){return b-a})
    }
    if(defenderDice !== null) {
      defenderDice.sort(function(a, b){return b-a})
    }

    console.log('attacker', attackerDice)
    console.log('defender', defenderDice)

    if(attackerDice === null || attackerDice[0] !== 'blitz') {
      return (
        <div className="card ml-5 mb-4">
          <div className="card-body">
            <h5 className="card-title">Attack phase</h5>
            <p>
              {(firstTerritory === '') ? "Choose territory from where to attack" : `Choose territory to attack from ${validate.humanize(firstTerritory)}`}
            </p>
              {(attackerDice !== null && defenderDice !== null) ? 
                  <div>
                    Your rolls:
                    <br/>
                    {
                      attackerDice.map((die, index) => {
                        switch(die) {
                          case 1: return (<img key={index} src="../images/side1.png" className="img-thumbnail" style={{height: "40px"}} alt=""/>)
                          case 2: return (<img key={index} src="../images/side2.png" className="img-thumbnail" style={{height: "40px"}} alt=""/>)
                          case 3: return (<img key={index} src="../images/side3.png" className="img-thumbnail" style={{height: "40px"}} alt=""/>)
                          case 4: return (<img key={index} src="../images/side4.png" className="img-thumbnail" style={{height: "40px"}} alt=""/>)
                          case 5: return (<img key={index} src="../images/side5.png" className="img-thumbnail" style={{height: "40px"}} alt=""/>)
                          case 6: return (<img key={index} src="../images/side6.png" className="img-thumbnail" style={{height: "40px"}} alt=""/>)
                        }
                      })
                    }
                    <hr />
                    Defender's rolls: 
                    <br/>
                    {
                      defenderDice.map((die, index) => {
                        switch(die) {
                          case 1: return (<img key={index} src="../images/side1.png" className="img-thumbnail" style={{height: "40px"}} alt=""/>)
                          case 2: return (<img key={index} src="../images/side2.png" className="img-thumbnail" style={{height: "40px"}} alt=""/>)
                          case 3: return (<img key={index} src="../images/side3.png" className="img-thumbnail" style={{height: "40px"}} alt=""/>)
                          case 4: return (<img key={index} src="../images/side4.png" className="img-thumbnail" style={{height: "40px"}} alt=""/>)
                          case 5: return (<img key={index} src="../images/side5.png" className="img-thumbnail" style={{height: "40px"}} alt=""/>)
                          case 6: return (<img key={index} src="../images/side6.png" className="img-thumbnail" style={{height: "40px"}} alt=""/>)
                        }
                      })
                    }
                 </div> : ''
                
                  
                
              }
          </div>
        </div>
      )
    } else {
      return (
        <div className="card ml-5 mb-4">
          <div className="card-body">
            <h5 className="card-title">Attack phase</h5>
            <p>
              {(firstTerritory === '') ? "Choose territory from where to attack" : `Choose territory to attack from ${validate.humanize(firstTerritory)}`}
            </p>
              {(attackerDice !== null && defenderDice !== null) ? 
                  <div>
                    You lost {defenderDice[0]} units
                    <hr />
                    Defender lost {defenderDice[1]} units 
                    <br/>
                 </div> : ''
              }
          </div>
        </div>
      )
    }
  }
}

export default AttackCard;