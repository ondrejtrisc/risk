import React, { Component } from 'react';
import Card from './Card';
import update from '../Functions/update'

let cardsOwned = [];
let cardsPlayed = [];


class CardsCard extends Component {
  constructor(props){
    super(props)
    this.handleSubmitClick = this.handleSubmitClick.bind(this)
    this.handleOnCardClick = this.handleOnCardClick.bind(this)
    
    this.state = {
      cardsOwned: cardsOwned,
      cardsPlayed: cardsPlayed,
      canPlay: false,
      warning: ''
    }
  }

  handleSubmitClick(event) {
    // console.log(event.target)
    if(cardsPlayed.length==3){
      if((cardsPlayed[0].type == cardsPlayed[1].type 
        && cardsPlayed[0].type == cardsPlayed[2].type)
      || (cardsPlayed[0].type != cardsPlayed[1].type 
        && cardsPlayed[0].type != cardsPlayed[2].type 
        && cardsPlayed[1].type != cardsPlayed[2].type)){
          update.sendCardsToServer(this.props.object, cardsPlayed, this.props.game_id)
          
          
          cardsPlayed = [];
      }else{
        this.setState({warning: 'Cards must all be same or unique type'})
      }
    } 
  }

  handleSelectCardClick(event){
    if(cardsPlayed.length < 3){
      cardsOwned.forEach((card, index) => {
        if(card.territory === event.target.id){
          cardsOwned.splice(index, 1)
          cardsPlayed.push(card)
        }
        if(cardsPlayed.length === 3) {
          this.setState({canPlay: true})
        }
      });
    }

    this.setState({cardsOwned: cardsOwned, cardsPlayed: cardsPlayed, warning: ''})
  }

  handleDeselectCardClick(event){
    cardsPlayed.forEach((card, index) => {
      if(card.territory === event.target.id){
        cardsPlayed.splice(index, 1)
        cardsOwned.push(card)
      }
      if(cardsPlayed.length < 3) {
        this.setState({canPlay: false})
      }
    });

    this.setState({cardsOwned: cardsOwned, cardsPlayed: cardsPlayed, warning: ''})
  }

  handleOnCardClick(event, action){
    if(action == 'select'){
      this.handleSelectCardClick(event)
    }else if(action == 'deselect'){
      this.handleDeselectCardClick(event)
    }
  }

  render(){
    console.log(this.state.canPlay)
    const currentPlayer = this.props.currentPlayer;
    switch (currentPlayer){
      case "red":
        cardsOwned = this.props.cards["red"];
        break;
      case "blue":
        cardsOwned = this.props.cards["blue"];
        break;
      case "green":
        cardsOwned = this.props.cards["green"];
        break;
      case "yellow":
        cardsOwned = this.props.cards["yellow"];
        break;
      case "brown":
        cardsOwned = this.props.cards["brown"];
        break;
      case "purple":
        cardsOwned = this.props.cards["purple"];
        break;
    }
  
    return (
      <div className="card ml-5 mb-4">
        <div className="card-body">
          {(this.state.warning) ? (<span className="text-danger"> {this.state.warning} </span>) : 'Choose three cards to play'}
          <br />
          <br/>
          <div style={{maxWidth: '95%', minHeight: '75px'}} className="row d-flex flex-row justify-content-center flex-nowrap mt-2">
            {cardsOwned !== null && cardsOwned.length > 0 ?
            cardsOwned.map((card, i) => (
              <Card 
                key = {card.territory}
                territory = {card.territory}
                troops={card.type}
                handleOnCardClick={(e) => this.handleOnCardClick(e, 'select')}
                
              />
              )) : (
                <></>
              )
            }
          </div>
          <hr/>

          <div 
          className="row d-flex justify-content-center"
          style={{minHeight:'75px', maxWidth: '95%'}}
          className="row d-flex flex-row justify-content-center flex-nowrap mt-2"
          >
          
            {cardsPlayed !== null && cardsPlayed.length > 0 ?
            cardsPlayed.map((card, i) => (
              <Card 
                key = {card.territory}
                territory = {card.territory}
                troops={card.type}
                handleOnCardClick={(e) => this.handleOnCardClick(e, 'deselect')}
              />
              )) : (
                <></>
              )
            }
          </div>
          <br/>
            <div className="row d-flex flex-row justify-content-center flex-nowrap mt-2">
              <button 
                onClick={(e) => this.handleSubmitClick(e)} 
                disabled={(this.state.canPlay === false) ? true : false} type="button" 
                className="btn btn-success btn-sm"
                >Play Cards</button>
            </div>

       </div>
      </div>    
    )
  }
}

export default CardsCard;