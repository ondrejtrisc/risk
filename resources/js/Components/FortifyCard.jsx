import React, { Component } from 'react';

class FortifyCard extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    const {firstTerritory} = this.props
    return (
        <div className="card ml-5 mb-4">
          <div className="card-body">
            <h5 className="card-title">Attack phase</h5>
            {(firstTerritory === '') ? "Choose territory from where to attack" : `Choose territory to attack from ${firstTerritory}`}
          </div>
        </div>
    )
  }
}

export default FortifyCard;