import React, { Component } from 'react';

class NextPhaseButton extends Component {
  constructor(props) {
    super(props)
  }


  render(){
    return (
          <button type="button" 
                  className="btn btn-success btn-large ml-5 w-80"
                  onClick={() => this.props.handleNextPhaseClick()}
                  >
                    End phase {this.props.phaseDesc}
          </button>
    )
  }
}

export default NextPhaseButton;