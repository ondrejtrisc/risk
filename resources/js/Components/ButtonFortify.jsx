import React, { Component } from 'react';

class ButtonFortify extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    return (
      <button type="button" 
              hidden={(this.props.phaseDesc === 'Fortify') ? false : true} 
              className="btn btn-secondary mr-3"
              onClick={() => this.props.handleFortifyButtonClick()}
              >Fortify</button>
      )
  }
}

export default ButtonFortify;