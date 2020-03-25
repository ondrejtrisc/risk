import React, { Component } from 'react';
import AttackCard from './AttackCard';
import FortifyCard from './FortifyCard';
import DeployCard from './DeployCard';
import DifferentTurnCard from './DifferentTurnCard';


class InfoCard extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    const {activePlayer, currentPlayer, territories, phase, unitsToDeploy, firstTerritory, attackerDice, defenderDice, secondTerritory, fromFortifyUnits, toFortifyUnits, handleFromInputChange, handleToInputChange, handleFortifyButtonClick, handleCancelFortifyClick} = this.props
    if(activePlayer === currentPlayer) {
      if(phase === 'deploy') {
        return (
          <DeployCard 
            unitsToDeploy={unitsToDeploy}
            firstTerritory={firstTerritory}
          />
        )
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

      }

    } else {
      return (
        < DifferentTurnCard />

      )

    }

  }
}

export default InfoCard;