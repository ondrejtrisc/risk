import React, { Component } from 'react';

class DifferentTurnCard extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    return (
      <div className="card ml-5 mb-4">
      <div className="card-body">
        <h5 className="card-title">Game information</h5>
        Player's turn:
        <br/>
        Сurrent phase:
        <br />
        How many troops left to deploy:
        <br />
        How many territories you own:
        <br/>
        How many continents you have:
        <br/>
        How many units you will deploy next turn calculation
        <br/>
      </div>
    </div>    )
  }
}

export default DifferentTurnCard;



