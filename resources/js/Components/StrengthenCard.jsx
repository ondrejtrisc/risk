import React, { Component } from 'react';
import validate from '../Functions/validate'

class StrengthenCard extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    const { activePlayer, currentPlayer, firstTerritory, handleStrengthenClick, handleCancelStrengthenClick } = this.props
    if (activePlayer === currentPlayer) {
      return (
        <div className="card ml-5 mb-4">
          <div className="card-body">
            <h5 className="card-title">STRENGTHEN PHASE: YOUR TURN</h5>
        <hr />
        {
          (firstTerritory) ? `Do you wish to strengthen ${validate.humanize(firstTerritory)}?` : 'You can choose one territory to strengthen'
        }
        <br />
        <br/>



            <button
              onClick={handleStrengthenClick}
              hidden={(firstTerritory) ? false : true} type="button"
              className="btn btn-success float-left btn-sm"
            >Confirm</button>

            <button
              onClick={handleCancelStrengthenClick}
              hidden={(firstTerritory !== '') ? false : true} type="button"
              className="btn btn-danger float-right btn-sm">Cancel</button>
          </div>
        </div>
      )
    } else {
      return (
        <div className="card ml-5 mb-4">
        <div className="card-body">
          <h5 className="card-title">STRENGTHEN PHASE</h5>
          <hr/>
          <h6>{validate.humanize(activePlayer)} player is currently selecting a territory to enforce</h6>
          <br/>
        </div>
      </div>    
        )
    }
  }
}

  export default StrengthenCard;



