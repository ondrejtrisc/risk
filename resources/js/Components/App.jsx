import React, { Component } from 'react';
import Map from './Map'
import PhaseBreadcrumb from './PhaseBreadcrumb';
import validate from '../Functions/validate'
import update from '../Functions/update'
import PlayerList from './PlayerList';
import ButtonBlitz from './ButtonBlitz';
import InfoCard from './InfoCard';
import NextPhaseButton from './NextPhaseButton';


class App extends Component {
  constructor(props) {
    super(props)
    this.state = {
      territories: [],
      activePlayer: 1,
      currentPlayer: document.querySelector('meta[name="color"]').getAttribute('content'),
      firstTerritory: '',
      secondTerritory: '',
      blitz: false,
      game_id: document.querySelector('meta[name="game_id"]').getAttribute('content'),
      phase: 'deploy',
      unitsToDeploy: 3,
      endOfPhase: false,
      deployed_units: []
    }
    this.handleMapClick = this.handleMapClick.bind(this)
    this.handleBlitzClick = this.handleBlitzClick.bind(this)
    this.handleNextPhaseClick = this.handleNextPhaseClick.bind(this)


  }

  componentDidMount() {
    update.getStateOfGame(this)
    update.addNumberOfUnits(this.state)

  }

  handleBlitzClick() {
    (this.state.blitz == true) ? this.setState({blitz: false}) : this.setState({blitz: true})
  }

  handleNextPhaseClick() {
    if(this.state.phase === 'attack') {
      this.setState({phase: 'fortify'})
    } else if(this.state.phase === 'deploy' && this.state.endOfPhase === true) {
      update.sendDeployToServer(this)
      this.setState({phase: 'attack'})
    } else if(this.state.phase === 'fortify') {
      this.setState({phase: 'deploy'})
    }
  }

  handleMapClick(event) {

    // ATTACK PHASE
    if(this.state.phase === 'attack') {
      this.setState({endOfPhase: true})
          // validates if the click is on a territory
      if(validate.territoryClick(event, this) === false) {
        console.log('this is not a territory')
        return;
      } 
      // validates if a territory is selected or not
      if(validate.isTerritorySelected(this) === false) 
      {

        //validates if the player owns this territory
        if(validate.canPlayerSelectTerritory(event, this) === true) {

          //selects the territory
          validate.selectTerritory(event)
          this.setState({firstTerritory: event.target.id})
          return;
        } else {
          console.log('player doesnt own this territory')
          return;
        }
      }

      //validates if the click is not on the same territory
      else if(validate.thisTerritoryAlreadySelected(event, this) === true) {
        validate.deselectSameTerritory(event)
        this.setState({firstTerritory: ''})
        return;
      } 

      // validates if the click is not on a different territory the player owns
      else if(validate.differentTerritoryAlreadySelected(event, this) === true) {
        validate.deselectOldTerritory(this)
        validate.selectTerritory(event)
        this.setState({firstTerritory: event.target.id})
        return;
      }

      // finds the two territory objects
      const attackingTerritory = validate.findFirstSelectedObject(this.state.territories, this.state.firstTerritory)
      const defendingTerritory = validate.findSecondSelectedObject(event, this.state.territories)

      //validates if the objects are neighbours
      if(validate.areNeighbours(attackingTerritory, defendingTerritory) === false) {
        console.log('not neighbours')
        return
      }

      //checks if second territory is an enemy territory
      if(validate.isEnemyTerritory(attackingTerritory, defendingTerritory) === false) {
        return
      } else {
        //sends attack to server
        update.sendAttackToServer(attackingTerritory.name, defendingTerritory.name, this)
      }
    } 
    
    // DEPLOY PHASE
    else if(this.state.phase === 'deploy') {

      //Is it a territory?
      if(validate.territoryClick(event, this) === false) {
        console.log('not territory')
        return;
      } 

      //is it owned by player?
      if(validate.canPlayerSelectTerritory(event,this) === true) {
        if(this.state.unitsToDeploy > 0) {
          let updatedTerritories = JSON.parse(JSON.stringify(this.state.territories))
          updatedTerritories.map(territory => {
            if(event.target.id === territory.name) {
              territory.units += 1
            }
          }) 
            if(this.state.unitsToDeploy === 1) {
              this.setState({territories: updatedTerritories})
              this.setState({unitsToDeploy: this.state.unitsToDeploy - 1})
              this.setState({endOfPhase: true})
            } else {
              this.setState({territories: updatedTerritories})
              this.setState({unitsToDeploy: this.state.unitsToDeploy - 1})
            }
          }
        } 
      }
      
      
    else if(this.state.phase === 'fortify') {
      
      }

    
  }

  

  render() {
    console.log(this.state)

    update.addNumberOfUnits(this.state)
    update.colorTerritories(this.state)
    console.log(this.state.phase)
    let phaseValue = "0%"
    let phaseDesc = ''
    if(this.state.phase === 'deploy') {
      phaseValue = "33%"
      phaseDesc = 'Deploy'
    } else if (this.state.phase === 'attack') {
      phaseValue = "66%"
      phaseDesc = 'Attack'
    } else if (this.state.phase === 'fortify') {
      phaseValue = "100%"
      phaseDesc = 'Fortify'

    }
    return (
      <div className="container">

        <div className="row d-flex flex-row justify-content-center align-items-end">
          <Map  
                handleMapClick={this.handleMapClick}        
          />
          <div className="col">
            <InfoCard />
            <PlayerList />
          </div>
        </div>

        <div className="row">
          <div className="col flex-row justify-content-space-between">
            <ButtonBlitz  blitz={this.state.blitz}
                          handleBlitzClick={this.handleBlitzClick}
            />
            <button type="button" className="btn btn-secondary mr-3">Cards</button>
            <button type="button" className="btn btn-secondary">Continent Values</button>
          </div>
          <div className="col d-flex flex-row justify-content-stretch">
            <PhaseBreadcrumb  phase={this.state.phase}
                              phaseValue={phaseValue}
                              phaseDesc={phaseDesc}
                              />
          </div>
          <div className="col">
            <NextPhaseButton  handleNextPhaseClick={this.handleNextPhaseClick}
                              phaseDesc={phaseDesc}
                              endOfPhase={this.state.endOfPhase}
            />
          </div>
        </div>
      </div>
    );
  }
}


export default App;
