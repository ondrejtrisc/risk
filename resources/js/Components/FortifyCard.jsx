import React, { Component } from 'react';

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
    this.setState({fromInput: event.target.value})
  }

  handleToInputChange(event) {
    this.setState({toInput : event.target.value})
  }

  render() {
    const { firstTerritory, secondTerritory, fromFortifyUnits, toFortifyUnits, handleCancelFortifyClick, handleFortifyButtonClick } = this.props
    const maximumFortifyUnits = fromFortifyUnits + toFortifyUnits - 1


    return (
      <div className="card ml-5 mb-4">
        <div className="card-body">
          <h5 className="card-title">Fortify</h5>
          <p>You may fortify one territory with units from another</p>
          <div className="input-group input-group-sm mb-3">
            <div className="input-group-prepend">
              <span className="input-group-text" id="inputGroup-sizing-sm">From {firstTerritory}</span>
            </div>
            <input type="number" onChange={(e) => this.props.handleFromInputChange(e)} value={fromFortifyUnits} className="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm"></input>
          </div>
          <div className="input-group input-group-sm mb-3">
            <div className="input-group-prepend">
              <span className="input-group-text" id="inputGroup-sizing-sm">To {secondTerritory}</span>
            </div>
            <input type="number" onChange={(e) => this.props.handleToInputChange(e)} value={toFortifyUnits} className="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm"></input>
          </div>

          <button 
            onClick={handleFortifyButtonClick} 
            hidden={(firstTerritory !== '' && secondTerritory !== '') ? false : true} type="button" 
            className="btn btn-success float-left btn-sm"
            >Fortify</button>

          <button 
            onClick={handleCancelFortifyClick} 
            hidden={(firstTerritory !== '' && secondTerritory !== '') ? false : true} type="button" 
            className="btn btn-danger float-right btn-sm">Cancel</button>
        </div>
      </div>
    )
  }
}

export default FortifyCard;