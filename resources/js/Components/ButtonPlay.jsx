import React, { Component } from 'react';

class ButtonPlay extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    return (
      <>
        <a href={`./game/${this.props.game_id}`} >
          <button 
            type="button" 
            className="btn btn-success btn-sm"
            value={this.props.game_id}
            onClick={(e) => this.props.handlePlayClick(e)}
          >
            Play
          </button>
        </a>
      </>
    )
  }
}

export default ButtonPlay;