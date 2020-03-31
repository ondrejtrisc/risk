import React, { Component } from 'react';

class Card extends Component {

  constructor(props) {
    super(props)
  }

  render() {
    return (
      <div className="col-2">
        <img
          id={this.props.territory}
          src={`../images/cards/card_${this.props.territory}.png`}
          onClick={(e, action) => this.props.handleOnCardClick(e, action)}
          className="card"
          style={{ height: "75px" }}
          alt={`card_${this.props.territory}`}
        />


      </div>

    );
  }
};

export default Card;