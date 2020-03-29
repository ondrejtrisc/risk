import React, { Component } from 'react';
import AttackCard from './AttackCard';
import FortifyCard from './FortifyCard';
import DeployCard from './DeployCard';
import DifferentTurnCard from './DifferentTurnCard';
import CardsCard from './CardsCard';
import OccupyCard from './OccupyCard';
import StrengthenCard from './StrengthenCard'


class InfoCard extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    const {object, game_id, attackerLost, defenderLost, activePlayer, cardsCard, currentPlayer, territories, phase, unitsToDeploy, firstTerritory, attackerDice, defenderDice, secondTerritory, fromFortifyUnits, toFortifyUnits, handleFromInputChange, handleToInputChange, handleFortifyButtonClick, handleCancelFortifyClick, cards, handleOccupyClick, handleCancelOccupyClick, handleStrengthenClick, handleCancelStrengthenClick, unitsToDistribute} = this.props
    if(activePlayer === currentPlayer) {
      if(phase === 'deploy') {
        if(cardsCard === true) {
          return (
            <CardsCard
              currentPlayer={currentPlayer}
              cards={cards}
              object={object}
              game_id={game_id}

            
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
            attackerLost={attackerLost}
            defenderLost={defenderLost}
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
          < OccupyCard
            activePlayer={activePlayer}
            currentPlayer={currentPlayer}
            firstTerritory={firstTerritory}
            handleOccupyClick={handleOccupyClick}
            handleCancelOccupyClick={handleCancelOccupyClick}
            unitsToDistribute={unitsToDistribute}

          />
        )

      } else if(phase === 'strengthen') {
        return (
        < StrengthenCard
        activePlayer={activePlayer}
        currentPlayer={currentPlayer}
        firstTerritory={firstTerritory}
        handleStrengthenClick={handleStrengthenClick}
        handleCancelStrengthenClick={handleCancelStrengthenClick}
        unitsToDistribute={unitsToDistribute}

      />        
      )
      }

    } 
    else if( phase === 'occupy') {
      return (
        < OccupyCard
        activePlayer={activePlayer}
        currentPlayer={currentPlayer}
        firstTerritory={firstTerritory}
        handleOccupyClick={handleOccupyClick}
        handleCancelOccupyClick={handleCancelOccupyClick}

      />
      )
    }

    else if(phase === 'strengthen') {
      return (
        < StrengthenCard
        activePlayer={activePlayer}
        currentPlayer={currentPlayer}
        firstTerritory={firstTerritory}
        handleStrengthenClick={handleStrengthenClick}
        handleCancelStrengthenClick={handleCancelStrengthenClick}
        unitsToDistribute={unitsToDistribute}

      />        
      )
    }

    else {
      return (
        < DifferentTurnCard 
        activePlayer={activePlayer}


        />

      )

    }

  }
}

export default InfoCard;