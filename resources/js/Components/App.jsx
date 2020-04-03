import React, { Component } from 'react';
import Map from './Map'
import PhaseBreadcrumb from './PhaseBreadcrumb';
import validate from '../Functions/validate'
import update from '../Functions/update'
import PlayerList from './PlayerList';
import ButtonBlitz from './ButtonBlitz';
import InfoCard from './InfoCard';
import CardsButton from './CardsButton'
import NextPhaseButton from './NextPhaseButton';


class App extends Component {
  constructor(props) {
    super(props)
    this.state = {
      userList: document.querySelector('meta[name="users"]').getAttribute('content').split(','),
      territories: [],
      activePlayer: '', // need to change to be color
      turns: [],
      turnIndex: 0,
      currentPlayer: document.querySelector('meta[name="color"]').getAttribute('content'),
      firstTerritory: '',
      secondTerritory: '',
      blitz: false,
      game_id: document.querySelector('meta[name="game_id"]').getAttribute('content'),
      phase: '',
      unitsToDeploy: 0,
      endOfPhase: false,
      deployed_units: [],
      warningMessage: '',
      fortified: false,
      fromFortify: '',
      toFortify: '',
      fromFortifyUnits: 0,
      toFortifyUnits: 0,
      maxFortifyUnits: 0,
      attackerDice: null,
      defenderDice: null,
      validFortify: false,
      cardsCard: false,
      interval: '',
      unitsToDistribute: 0,
      clicked: false,
      cards: [],
      attackerLost: 0,
      defenderLost: 0,
      unitsOfContinents: 0,
      unitsOfAfrica: null,
      unitsOfEurope: null,
      unitsOfAustralia: null,
      unitsOfNorthAmerica: null,
      unitsOfSouthAmerica: null,
      unitsOfAsia: null,
      unitsOfTerritories: null
    }
    this.handleMapClick = this.handleMapClick.bind(this)
    this.handleBlitzClick = this.handleBlitzClick.bind(this)
    this.handleNextPhaseClick = this.handleNextPhaseClick.bind(this)
    this.handleFortifyButtonClick = this.handleFortifyButtonClick.bind(this)
    this.handleFromInputChange = this.handleFromInputChange.bind(this)
    this.handleToInputChange = this.handleToInputChange.bind(this)
    this.handleCancelFortifyClick = this.handleCancelFortifyClick.bind(this)
    this.handleCardsClick = this.handleCardsClick.bind(this)
    this.handleOccupyClick = this.handleOccupyClick.bind(this)
    this.handleCancelOccupyClick = this.handleCancelOccupyClick.bind(this)
    this.handleStrengthenClick = this.handleStrengthenClick.bind(this)
    this.handleCancelStrengthenClick = this.handleCancelStrengthenClick.bind(this)
  }




  componentDidMount() {
    update.getStateOfGame(this)
    update.addNumberOfUnits(this.state)
    update.colorTerritories(this.state)
    this.intervalId = setInterval(() => { update.getStateOfGame(this) }, 2000)
  }

  componentDidUpdate() {
    setTimeout(console.log('waiting'), 500)

    if (this.state.activePlayer === this.state.currentPlayer) {
      clearInterval(this.intervalId)
    } 
  }

  componentWillUnmount() {
    clearInterval(this.intervalId)
  }

  handleBlitzClick() {
    (this.state.blitz == true) ? this.setState({ blitz: false }) : this.setState({ blitz: true })
  }

  handleNextPhaseClick() {
    if (validate.isPlayersTurn(this) === false) return
    if (this.state.phase === 'attack') {
      validate.deselectAllTerritories(this)
      this.setState({ phase: 'fortify', firstTerritory: '' })
    } else if (this.state.phase === 'deploy') {
      if (this.state.endOfPhase === true) {
        update.sendDeployToServer(this)
        this.setState({ phase: 'attack' })
      }
      else {
        this.setState({ warningMessage: "You still haven't finished deploying troops" })

      }
    } else if (this.state.phase === 'fortify') {
      update.sendFortifyToServer(this, false)
      this.intervalId = setInterval(() => update.getStateOfGame(this), 2000)
    }
  }

  handleMapClick(event) {
    if(this.state.currentPlayer !== this.state.activePlayer) return
    if (validate.isPlayersTurn(this) === false) return


    //OCCUPY PHASE
    if(this.state.phase === 'occupy') {
      validate.deselectAllTerritories(this)
      this.setState({firstTerritory: ''})
      if(this.state.currentPlayer === this.state.activePlayer) {
        this.state.territories.map(territory => {
          if(territory.name === event.target.id && territory.player === null) {
            this.setState({firstTerritory: event.target.id})
            validate.selectTerritory(event)
            return ''
          } else {
            return ''
          }
        })
      }
    }


    //STRENGTHEN PHASE
    if(this.state.phase === 'strengthen') {
      validate.deselectAllTerritories(this)
      this.setState({firstTerritory: ''})
      this.state.territories.map(territory => {
        if(territory.name === event.target.id && territory.player === this.state.activePlayer) {
          this.setState({firstTerritory: event.target.id})
          validate.selectTerritory(event)
        } else {
          return ''
        }
      })
    }


    // ATTACK PHASE
    if (this.state.phase === 'attack') {
      this.setState({ endOfPhase: true })
      // validates if the click is on a territory
      if (validate.territoryClick(event, this) === false) {
        console.log('this is not a territory')
        return;
      }
      // validates if a territory is selected or not
      if (validate.isTerritorySelected(this) === false) {

        //validates if the player owns this territory
        if (validate.canPlayerSelectTerritory(event, this) === true) {

          //selects the territory
          validate.selectTerritory(event)
          this.setState({ firstTerritory: event.target.id })
          return;
        } else {
          console.log('player doesnt own this territory')
          return;
        }
      }

      //validates if the click is not on the same territory
      else if (validate.thisTerritoryAlreadySelected(event, this) === true) {
        validate.deselectSameTerritory(event)
        this.setState({ firstTerritory: '' })
        return;
      }

      // validates if the click is not on a different territory the player owns
      else if (validate.differentTerritoryAlreadySelected(event, this) === true) {
        validate.deselectOldTerritory(this)
        validate.selectTerritory(event)
        this.setState({ firstTerritory: event.target.id })
        return;
      }

      // finds the two territory objects
      const attackingTerritory = validate.findFirstSelectedObject(this.state.territories, this.state.firstTerritory)
      const defendingTerritory = validate.findSecondSelectedObject(event, this.state.territories)

      //validates if the objects are neighbours
      if (validate.areNeighbours(attackingTerritory, defendingTerritory) === false) {
        console.log('not neighbours')
        return
      }

      //checks if second territory is an enemy territory
      if (validate.isEnemyTerritory(attackingTerritory, defendingTerritory) === false) {
        return
      } else {
        //sends attack to server
        update.sendAttackToServer(attackingTerritory.name, defendingTerritory.name, this)
        validate.deselectOldTerritory(this)
        this.setState({ firstTerritory: '' })
      }
    }

    // DEPLOY PHASE
    else if (this.state.phase === 'deploy') {
      if(this.state.cards[this.state.activePlayer].length >= 5) return

      //Is it a territory?
      if (validate.territoryClick(event, this) === false) {
        console.log('not territory')
        return;
      }

      //is it owned by player?
      if (validate.canPlayerSelectTerritory(event, this) === true) {
        console.log(this.state.unitsToDeploy)
        if (this.state.unitsToDeploy > 0) {
          let updatedTerritories = JSON.parse(JSON.stringify(this.state.territories))
          updatedTerritories.map(territory => {
            if (event.target.id === territory.name) {
              territory.units = Number(territory.units) + 1
            }
          })

          if (this.state.unitsToDeploy === 1) {
            this.setState({ territories: updatedTerritories })
            this.setState({ unitsToDeploy: this.state.unitsToDeploy - 1 })
            this.setState({ endOfPhase: true })
          } else {
            this.setState({ territories: updatedTerritories })
            this.setState({ unitsToDeploy: this.state.unitsToDeploy - 1 })
          }
        }
      }
    }

    // FORTIFY PHASE
    else if (this.state.phase === 'fortify') {
      if (this.state.validFortify === true) return
      if (validate.territoryClick(event, this) === false) {
        console.log('this is not a territory')
        return;
      }
      // validates if a territory is selected or not
      if (validate.isTerritorySelected(this) === false) {

        //validates if the player owns this territory
        if (validate.canPlayerSelectTerritory(event, this) === true) {

          //selects the territory
          validate.selectTerritory(event)
          this.setState({ firstTerritory: event.target.id })
          return;
        } else {
          console.log('player doesnt own this territory')
          return;
        }
      }

      //validates if the click is not on the same territory
      else if (validate.thisTerritoryAlreadySelected(event, this) === true) {
        validate.deselectSameTerritory(event)
        this.setState({ firstTerritory: '' })
        return;
      }
      if (validate.differentTerritoryAlreadySelected(event, this) === true) {
        const fromTerritory = validate.findFirstSelectedObject(this.state.territories, this.state.firstTerritory)
        const toTerritory = validate.findSecondSelectedObject(event, this.state.territories)
        validate.selectTerritory(event)
        this.setState({
          secondTerritory: event.target.id,
          fromFortifyUnits: Number(fromTerritory.units),
          toFortifyUnits: Number(toTerritory.units),
          maxFortifyUnits: Number(toTerritory.units) + Number(fromTerritory.units),
          validFortify: true
        })
        return;
      }


    }


  }


  handleFromInputChange(event) {
    if (event.target.value >= this.state.maxFortifyUnits || event.target.value < 1) {
      return
    } else {
      this.setState({
        fromFortifyUnits: Number(event.target.value),
        toFortifyUnits: Number(this.state.maxFortifyUnits) - Number(event.target.value)
      })
    }
  }

  handleToInputChange(event) {
    if (event.target.value >= this.state.maxFortifyUnits || event.target.value < 1) {
      return
    } else {
      this.setState({
        fromFortifyUnits: Number(this.state.maxFortifyUnits) - Number(event.target.value),
        toFortifyUnits: Number(event.target.value)
      })
    }
  }

  handleCancelFortifyClick() {
    this.setState({
      firstTerritory: '',
      secondTerritory: '',
      fromFortifyUnits: 0,
      toFortifyUnits: 0,
      maxFortifyUnits: 0,
      validFortify: false
    })
    validate.deselectAllTerritories(this)
  }

  handleFortifyButtonClick(event) {
    if (validate.isPlayersTurn(this) === false) return
    validate.deselectAllTerritories(this)
    update.sendFortifyToServer(this)
    this.setState({
      firstTerritory: '',
      secondTerritory: '',
      fromFortifyUnits: 0,
      toFortifyUnits: 0,
      maxFortifyUnits: 0,
      validFortify: false
    })
    this.intervalId = setInterval(() => {update.getStateOfGame(this)}, 2000)
  }

  handleCardsClick(event) {
    this.setState({ cardsCard: !this.state.cardsCard })
  }

  handleOccupyClick(event) {
    this.setState({
      firstTerritory: '',
      activePlayer: 'no one',

    })
    validate.deselectAllTerritories(this)
    update.sendOccupyToServer(this, this.state.firstTerritory)

    this.intervalId = setInterval(() => {update.getStateOfGame(this)}, 2000)
  }

  handleCancelOccupyClick(event) {
    this.setState({
      firstTerritory: ''
    })
    validate.deselectAllTerritories(this)
  }

  handleStrengthenClick(event) {
    this.setState({
      firstTerritory: '',
      activePlayer: 'no one',

    })
    validate.deselectAllTerritories(this)
    update.sendStrengthenToServer(this, this.state.firstTerritory)

    this.intervalId = setInterval(() => {update.getStateOfGame(this)}, 2000)  
  }

  handleCancelStrengthenClick(event) {
    this.setState({
      firstTerritory: ''
    })
    validate.deselectAllTerritories(this)
  }



  render() {
    update.addNumberOfUnits(this.state)
    update.colorTerritories(this.state)
    console.log(this.state)

    let phaseValue = "0%"
    let phaseDesc = ''

    if (this.state.phase === 'deploy') {
      phaseValue = "33%"
      phaseDesc = 'Deploy'
    } else if (this.state.phase === 'attack') {
      phaseValue = "66%"
      phaseDesc = 'Attack'
    } else if (this.state.phase === 'fortify') {
      phaseValue = "100%"
      phaseDesc = 'Fortify'

    }

    if(this.state.turns.length !== 1) {
    return (
      <div className="container">
        <div className="fixed-top mt-3 mr-3">
          <a href="../home">
            <button className="btn btn-primary float-right">Home</button> 
          </a> 
        </div>
        <div className="row d-flex flex-row justify-content-center align-items-end">
          <Map
            handleMapClick={this.handleMapClick}
          />
          <div className="col-4">
            <InfoCard
              activePlayer={this.state.activePlayer}
              currentPlayer={this.state.currentPlayer}
              territories={this.state.territories}
              attackerDice={this.state.attackerDice}
              defenderDice={this.state.defenderDice}
              phase={this.state.phase}
              unitsToDeploy={this.state.unitsToDeploy}
              firstTerritory={this.state.firstTerritory}
              secondTerritory={this.state.secondTerritory}
              fromFortifyUnits={this.state.fromFortifyUnits}
              toFortifyUnits={this.state.toFortifyUnits}
              handleFromInputChange={this.handleFromInputChange}
              handleToInputChange={this.handleToInputChange}
              handleCancelFortifyClick={this.handleCancelFortifyClick}
              handleFortifyButtonClick={this.handleFortifyButtonClick}
              handleCancelOccupyClick={this.handleCancelOccupyClick}
              handleOccupyClick={this.handleOccupyClick}
              handleCancelStrengthenClick={this.handleCancelStrengthenClick}
              handleStrengthenClick={this.handleStrengthenClick}
              cardsCard={this.state.cardsCard}
              cards={this.state.cards}
              unitsToDistribute={this.state.unitsToDistribute}
              defenderLost={this.state.defenderLost}
              attackerLost={this.state.attackerLost}
              game_id={this.state.game_id}
              object={this}
              unitsOfAfrica={this.state.unitsOfAfrica}
              unitsOfEurope={this.state.unitsOfEurope}
              unitsOfAustralia={this.state.unitsOfAustralia}
              unitsOfNorthAmerica={this.state.unitsOfNorthAmerica}
              unitsOfSouthAmerica={this.state.unitsOfSouthAmerica}
              unitsOfAsia={this.state.unitsOfAsia}
              unitsOfTerritories={this.state.unitsOfTerritories}
                           
              />
            <PlayerList
              userList={this.state.userList}
              activePlayer={this.state.activePlayer}
              turns={this.state.turns}
              territories={this.state.territories}
              cards={this.state.cards}

            />
          </div>
        </div>

        <div className="row">
          <div className="col-3 flex-row justify-content-space-between">
            <ButtonBlitz blitz={this.state.blitz}
              handleBlitzClick={this.handleBlitzClick}
            />
            <CardsButton
              handleCardsClick={this.handleCardsClick}
            />
          </div>
          <div className="col-5 d-flex flex-row justify-content-stretch">
            <PhaseBreadcrumb phase={this.state.phase}
              phaseValue={phaseValue}
              phaseDesc={phaseDesc}
            />
          </div>
          <div className="col">
            <NextPhaseButton handleNextPhaseClick={this.handleNextPhaseClick}
              phaseDesc={phaseDesc}
              endOfPhase={this.state.endOfPhase}
              activePlayer={this.state.activePlayer}
              currentPlayer={this.state.currentPlayer}
            />
          </div>
        </div>
      </div>
    );
    }
    else {
      return (
        <div className="container mt-5">
          <div class="jumbotron" style={{backgroundColor: "rgb(248,224,176, .9)"}}>
          <h1 style={{
                    fontFamily: 'Bree Serif, serif', 
                    fontWeight: "600", 
                    color:"rgb(40,16,16, 1)", 
                    textTransform: "uppercase",
                    letterSpacing: ".1rem",
                    
                   }}
                   >
                     Player {this.state.turns[0]} has won! 
                    </h1>
                    <br/>
            <p class="lead">Congratulations to the conqueror, you are now the sole hegemon of the world!</p>
            <hr class="my-4" />
            <p>Bask in your glory for a while and then you can return home to join more games</p>
            <p class="lead">
              <a class="btn btn-primary btn-lg" href="../home" role="button">HOME</a>
            </p>
          </div>
        </div>
      )
    }
  }
}


export default App;
