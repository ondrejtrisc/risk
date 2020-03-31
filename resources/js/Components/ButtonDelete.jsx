import React, { Component } from 'react';

class ButtonDelete extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    return (
      <>
        <button 
          type="button" 
          className="btn btn-warning btn-sm"
          value={this.props.game_id}
          onClick={(e) => this.props.handleDeleteClick(e)}
          >
          Delete
        </button>
      </>
    )
  }
}

export default ButtonDelete;