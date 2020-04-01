import React, { Component } from 'react';

class ButtonLobbyMenu extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    return (
      <>
          <button 
            type="button" 
            className={(this.props.pageIsCreate) ? "btn btn-primary mr-3" : "btn btn-info mr-3"}
            onClick={(e) => this.props.handleMenuClick(e)}
          >
            {(this.props.pageIsCreate) ? "Back to games list" : "Create a new game" }
          </button>
      </>
    )
  }
}

export default ButtonLobbyMenu;