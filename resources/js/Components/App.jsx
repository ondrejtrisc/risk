import React, { Component } from 'react';
import Map from './Map'
import PhaseBreadcrumb from './PhaseBreadcrumb';
import validate from '../Functions/validate'
import update from '../Functions/update'
import PlayerList from './PlayerList';
import ButtonBlitz from './ButtonBlitz';
import InfoCard from './InfoCard';


class App extends Component {
  constructor(props) {
    super(props)
    this.state = {
      territories: [],
      activePlayer: 1,
      firstTerritory: '',
      secondTerritory: '',
      blitz: false,
      game_id: 10,
      currentPhase: 'deploy',
      units_to_deploy: 10,
      deployed_units: []
    }
    this.handleMapClick = this.handleMapClick.bind(this)
    this.handleBlitzClick = this.handleBlitzClick.bind(this)

  }

  componentDidMount() {
    update.getInitialStateOfGame(this)

  }

  handleBlitzClick() {
    (this.state.blitz == true) ? this.setState({blitz: false}) : this.setState({blitz: true})
  }

  handleMapClick(event) {
    if(this.state.currentPhase === 'combat') {
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
    
    else if(this.state.currentPhase === 'deploy') {
      if(validate.territoryClick(event, this) === false) {
        console.log('not territory')
        return;
      } 

      if(validate.canPlayerSelectTerritory(event,this) === true) {

    }
  }
    
    else if(this.state.currentPhase === 'fortify') {
      
      }

    }

  

  render() {
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
          <div className="col d-flex flex-row justify-content-center align-items-baseline">
            <PhaseBreadcrumb />
          </div>
          <div className="col">
            <button type="button" className="btn btn-success btn-block">Next phase</button>
          </div>
        </div>
      </div>
    );
  }
}


export default App;
