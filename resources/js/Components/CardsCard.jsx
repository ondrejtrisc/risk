import React, { Component } from 'react';
import Card from './Card';

let cardsOwned = [
  {'territory': 'China', 'troops': 'artillery'},
  {'territory': 'India', 'troops': 'infantry'},
  {'territory': 'Iceland', 'troops': 'infantry'},
  {'territory': 'Brazil', 'troops': 'cavalry'}
];
let cardsPlayed = [];


class CardsCard extends Component {
  constructor(props){
    super(props)
    this.handleSubmitClick = this.handleSubmitClick.bind(this)
    this.handleCancelClick = this.handleCancelClick.bind(this)
    this.handleOnCardClick = this.handleOnCardClick.bind(this)
    
    this.state = {
      cardsOwned: cardsOwned,
      cardsPlayed: cardsPlayed
    }
  }

  handleSubmitClick(event) {
    console.log(event.target)
  }

  handleCancelClick(event) {
    console.log(event.target)
  }

  handleSelectCardClick(event){
    if(cardsPlayed.length < 3){
      cardsOwned.forEach((card, index) => {
        if(card.territory === event.target.id){
          cardsOwned.splice(index, 1)
          cardsPlayed.push(card)
        }
      });
    }

    this.setState({cardsOwned: cardsOwned, cardsPlayed: cardsPlayed})
  }

  handleDeselectCardClick(event){
    cardsPlayed.forEach((card, index) => {
      if(card.territory === event.target.id){
        cardsPlayed.splice(index, 1)
        cardsOwned.push(card)
      }
    });

    this.setState({cardsOwned: cardsOwned, cardsPlayed: cardsPlayed})
  }

  handleOnCardClick(event, action){
    if(action == 'select'){
      this.handleSelectCardClick(event)
    }else if(action == 'deselect'){
      this.handleDeselectCardClick(event)
    }
  }

  render(){
    console.log(this.props.cards)
    

    return (
      <div className="card ml-5 mb-4">
        <div className="card-body">
          <h5 className="card-title">Play Cards</h5>
          Section for cards owned <br />
          <div className="row d-flex justify-content-around">
            {cardsOwned !== null && cardsOwned.length > 0 ?
            cardsOwned.map((card, i) => (
              <Card 
                key = {card.territory}
                territory = {card.territory}
                troops={card.troops}
                handleOnCardClick={(e) => this.handleOnCardClick(e, 'select')}
              />
              )) : (
                <></>
              )
            }
          </div>
          <hr/>

          Section for cards to be played
          <br />

          <div className="row d-flex justify-content-around">
            {cardsPlayed !== null && cardsPlayed.length > 0 ?
            cardsPlayed.map((card, i) => (
              <Card 
                key = {card.territory}
                territory = {card.territory}
                troops={card.troops}
                handleOnCardClick={(e) => this.handleOnCardClick(e, 'deselect')}
              />
              )) : (
                <></>
              )
            }
          </div>

          <br />
          <button 
            onClick={(e) => this.handleSubmitClick(e)} 
            hidden={true ? false : true} type="button" 
            className="btn btn-success float-left btn-sm"
            >Play Cards</button>

          <button 
            onClick={(e) => this.handleCancelClick(e)} 
            hidden={true ? false : true} type="button" 
            className="btn btn-danger float-right btn-sm">Cancel</button>
          <br/>
        </div>
      </div>    
    )
  }
}

export default CardsCard;