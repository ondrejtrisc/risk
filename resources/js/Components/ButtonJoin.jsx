import React, { Component } from 'react';

class ButtonJoin extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    return (
      <>
        <button 
          type="button" 
          className="btn btn-primary btn-sm"
          value={this.props.game_id}
          onClick={(e) => this.props.handleJoinClick(e)}
          >
            Join
        </button>
      </>
    )
  }
}

export default ButtonJoin;