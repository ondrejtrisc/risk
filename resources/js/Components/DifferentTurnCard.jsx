import React, { Component } from 'react';
import validate from '../Functions/validate';

class DifferentTurnCard extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    return (
      <div className="card ml-5 mb-4">
      <div className="card-body">
        <h5 className="card-title">Game information</h5>
        { (this.props.activePlayer) ? `${validate.humanize(this.props.activePlayer)} player's turn` : '' }
        <br/>
      </div>
    </div>    
    )
  }
}

export default DifferentTurnCard;



