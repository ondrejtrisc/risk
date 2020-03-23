import React, { Component } from 'react';

class ButtonBlitz extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    return (
      <>
        <button onClick={() => this.props.handleBlitzClick()} 
                type="button" 
                className={(this.props.blitz) ? "btn btn-danger mr-3" : "btn btn-success mr-3"}>
                  {
                  (this.props.blitz) ? "Blitz Attack" : "Normal Attack"
                  }
        </button>


      </>
    )
  }
}

export default ButtonBlitz;