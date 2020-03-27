import React, { Component } from 'react';
import AttackCard from './AttackCard';
import FortifyCard from './FortifyCard';
import DeployCard from './DeployCard';
import DifferentTurnCard from './DifferentTurnCard';
import CardsCard from './CardsCard';
import OccupyCard from './OccupyCard';


class InfoCard extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    const {activePlayer, cardsCard, currentPlayer, territories, phase, unitsToDeploy, firstTerritory, attackerDice, defenderDice, secondTerritory, fromFortifyUnits, toFortifyUnits, handleFromInputChange, handleToInputChange, handleFortifyButtonClick, handleCancelFortifyClick, cards} = this.props
    if(activePlayer === currentPlayer) {
      if(phase === 'deploy') {
        if(cardsCard === true) {
          return (
            <CardsCard
              currentPlayer={currentPlayer}
              cards={cards}

            
            />
          )
        } else {
          return (
            <DeployCard 
              unitsToDeploy={unitsToDeploy}
              firstTerritory={firstTerritory}
            />
        )
      }          
      } else if(phase === 'attack') {
        return (
          <AttackCard 
            firstTerritory={firstTerritory}
            attackerDice={attackerDice}
            defenderDice={defenderDice}
          />
        )

      } else if(phase === 'fortify') {
        return (
          <FortifyCard 
            firstTerritory={firstTerritory}
            secondTerritory={secondTerritory}
            territories={territories}
            fromFortifyUnits={fromFortifyUnits}
            toFortifyUnits={toFortifyUnits}
            handleFromInputChange={handleFromInputChange}
            handleToInputChange={handleToInputChange}
            handleCancelFortifyClick={handleCancelFortifyClick}
            handleFortifyButtonClick={handleFortifyButtonClick}
          />
        )
      } else if(phase === 'occupy') {
        return (
          < OccupyCard />
        )

      }

    } 
    else if( phase === 'occupy') {
      return (
        < OccupyCard />
      )
    }

    else {
      return (
        < DifferentTurnCard />

      )

    }

  }
}

export default InfoCard;