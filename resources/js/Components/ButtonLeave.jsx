import React, { Component } from 'react';

class ButtonLeave extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    return (
      <>
        <button 
          type="button" 
          className="btn btn-danger btn-sm"
          value={this.props.game_id}
          onClick={(e) => this.props.handleLeaveClick(e)}
          >
            Leave
        </button>
      </>
    )
  }
}

export default ButtonLeave;