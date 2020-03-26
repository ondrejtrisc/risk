import React, { Component } from 'react';

class CardsButton extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    return (
      <button 
        type="button" 
        onClick={(e) => this.props.handleCardsClick(e)}
        className="btn btn-secondary mr-3">
          Cards
      </button>
      )
  }
}

export default CardsButton;

