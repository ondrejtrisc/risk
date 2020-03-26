import React, { Component } from 'react';

class CardsCard extends Component {
  constructor(props){
    super(props)
    this.handleSubmitClick = this.handleSubmitClick.bind(this)
    this.handleCancelClick = this.handleCancelClick.bind(this)
  }

  handleSubmitClick(event) {
    console.log(event.target)
  }

  handleCancelClick(event) {
    console.log(event.target)
  }



  render(){
    return (
        <div className="card ml-5 mb-4">
        <div className="card-body">
          <h5 className="card-title">Play Cards</h5>
          Section for cards owned
          <hr/>
          Section for cards to be played
          <br />
          <br />
          <button 
            onClick={(e) => this.handleSubmitClick(e)} 
            hidden={true ? false : true} type="button" 
            className="btn btn-success float-left btn-sm"
            >Play Cards</button>

          <button 
            onClick={(e) => this.handleCancelClick(e)} 
            hidden={true ? false : true} type="button" 
            className="btn btn-danger float-right btn-sm">Cancel</button>
          <br/>
        </div>
      </div>    
    )
  }
}

export default CardsCard;