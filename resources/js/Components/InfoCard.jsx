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
    const {activePlayer, currentPlayer, territories, phase, unitsToDeploy, firstTerritory, attackerDice, defenderDice} = this.props
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