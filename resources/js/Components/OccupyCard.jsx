import React, { Component } from 'react';
import validate from '../Functions/validate'

class OccupyCard extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    const { activePlayer, currentPlayer, firstTerritory, handleOccupyClick, handleCancelOccupyClick } = this.props
    if (activePlayer === currentPlayer) {
      return (
        <div className="card ml-5 mb-4">
          <div className="card-body">
            <h5 className="card-title">OCCUPY PHASE: YOUR TURN</h5>
        <h5></h5>
        <hr />
        {
          (firstTerritory) ? `Do you wish to occupy ${validate.humanize(firstTerritory)}` : 'You can choose one territory to occupy'
        }
        <br />
        <br/>



            <button
              onClick={handleOccupyClick}
              hidden={(firstTerritory) ? false : true} type="button"
              className="btn btn-success float-left btn-sm"
            >Occupy</button>

            <button
              onClick={handleCancelOccupyClick}
              hidden={(firstTerritory !== '') ? false : true} type="button"
              className="btn btn-danger float-right btn-sm">Cancel</button>
          </div>
        </div>
      )
    } else {
      return (
        <div className="card ml-5 mb-4">
        <div className="card-body">
          <h5 className="card-title">OCCUPY PHASE</h5>
          <h6>{validate.humanize(activePlayer)} player is currently selecting a territory</h6>
          <br/>
        </div>
      </div>    
        )
    }
  }
}

  export default OccupyCard;



