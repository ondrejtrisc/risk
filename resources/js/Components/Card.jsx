import React, { Component } from 'react';

class Card extends Component {

  constructor(props){
    super(props)
  }

  render() {
    return (
        <svg   
          id={`${ this.props.territory }`}
          className="card"
          width="50" 
          height="70" 
          viewBox="0 0 100 100"
          onClick={(e, action) => this.props.handleOnCardClick(e, action)}
          >
          <g>
            <path
              id={ this.props.territory }
            ></path>
          </g>
          <text className="card-territory" x="15" y="30">
            { this.props.territory }
          </text>
          <text className="card-troops" x="15" y="70">
            { this.props.troops }
          </text>
        </svg>
    );
  }
};

export default Card;