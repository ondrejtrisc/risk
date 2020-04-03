import React, { Component } from 'react';

class DeployCard extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    const {activePlayer, unitsToDeploy, unitsOfAfrica, unitsOfEurope, unitsOfAustralia, unitsOfNorthAmerica, unitsOfAsia, unitsOfSouthAmerica, unitsOfTerritories, cards} = this.props
    console.log('cards',cards[activePlayer].length)
    console.log('activePlayer', activePlayer)

    if(cards && cards[activePlayer].length >= 5) {
      return (
        <div>
        <div className="card ml-5 mb-4">
          <div className="card-body">
            <h5 className="card-title">Deploy phase</h5>
            <p className="text-danger">You have {cards[activePlayer].length} cards, you must play them now</p>

          </div>
        </div>

      </div>

      )

    }

    return (
      <div>
        <div className="card ml-5 mb-4">
          <div className="card-body" >
            <h5 className="card-title" >Deploy phase</h5>
            {(unitsOfAfrica) ? (<p>{`${unitsOfAfrica} units for controlling Africa`}</p>) : ''}
            {(unitsOfEurope) ? (<p>{`${unitsOfEurope} units for controlling Europe`}</p>) : ''}
            {(unitsOfAustralia) ? (<p>{`${unitsOfAustralia} units for controlling Australia`}</p>) : ''}
            {(unitsOfNorthAmerica) ? (<p>{`${unitsOfNorthAmerica} units for controlling North America`}</p>) : ''}
            {(unitsOfAsia) ? (<p>{`${unitsOfAsia} units for controlling Asia`}</p>) : ''}
            {(unitsOfSouthAmerica) ? (<p>{`${unitsOfSouthAmerica} units for controlling South America`}</p>) : ''}
            {(unitsOfTerritories) ? (<p>{`${unitsOfTerritories} units for controlling territories`}</p>) : ''}

            <hr/>
            Reinforce your territories            
            <hr/>
            {(unitsToDeploy > 0) ? `You still have ${unitsToDeploy} units to deploy` : 'Deployment complete'}

          </div>
        </div>

      </div>
    )
  }
}

export default DeployCard;