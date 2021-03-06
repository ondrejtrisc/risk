import React, { Component } from 'react';
import validate from '../Functions/validate'


class FortifyCard extends Component {
  constructor(props) {
    super(props)
    this.state = {
      fromInput: 0,
      toInput: 0,
      maximum: 0
    }
    this.handleFromInputChange = this.handleFromInputChange.bind(this)
    this.handleToInputChange = this.handleToInputChange.bind(this)

  }


  handleFromInputChange(event) {
    this.setState({fromInput: Number(event.target.value)})
  }

  handleToInputChange(event) {
    this.setState({toInput : Number(event.target.value)})
  }

  render() {
    const { firstTerritory, secondTerritory, fromFortifyUnits, toFortifyUnits, handleCancelFortifyClick, handleFortifyButtonClick } = this.props
    const maximumFortifyUnits = Number(fromFortifyUnits) + Number(toFortifyUnits) - 1


    return (
      <div className="card ml-5 mb-4">
        <div className="card-body">
          <h5 className="card-title">Fortify</h5>
          <p>You may fortify one territory with units from another</p>
          <div className="input-group input-group-sm mb-3">
            <div className="input-group-prepend">
              <span className="input-group-text" id="inputGroup-sizing-sm">From {validate.humanize(firstTerritory)}</span>
            </div>
            <input type="number" onChange={(e) => this.props.handleFromInputChange(e)} value={Number(fromFortifyUnits)} className="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm"></input>
          </div>
          <div className="input-group input-group-sm mb-3">
            <div className="input-group-prepend">
              <span className="input-group-text" id="inputGroup-sizing-sm">To {validate.humanize(secondTerritory)}</span>
            </div>
            <input type="number" onChange={(e) => this.props.handleToInputChange(e)} value={Number(toFortifyUnits)} className="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm"></input>
          </div>

          <button 
            onClick={handleFortifyButtonClick} 
            hidden={(firstTerritory !== '' && secondTerritory !== '') ? false : true} type="button" 
            className="btn btn-success float-left btn-sm"
            >Fortify</button>

          <button 
            onClick={handleCancelFortifyClick} 
            hidden={(firstTerritory !== '') ? false : true} type="button" 
            className="btn btn-danger float-right btn-sm">Cancel</button>
        </div>
      </div>
    )
  }
}

export default FortifyCard;