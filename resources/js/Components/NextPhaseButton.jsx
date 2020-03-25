import React, { Component } from 'react';

class NextPhaseButton extends Component {
  constructor(props) {
    super(props)
  }


  render(){
    return (
          <button type="button" 
                  className={(this.props.endOfPhase) ? "btn btn-success btn-large ml-5 w-80" : "btn btn-secondary btn-large ml-5 w-80"}
                  onClick={() => this.props.handleNextPhaseClick()}
                  disabled={(this.props.endOfPhase) ? false : true}
                  hidden={(this.props.activePlayer === this.props.currentPlayer) ? false : true}
                  >
                    {
                    (this.props.phaseDesc === 'Fortify') ? 'End turn' : `End phase ${this.props.phaseDesc}`
                    }
          </button>
    )
  }
}

export default NextPhaseButton;