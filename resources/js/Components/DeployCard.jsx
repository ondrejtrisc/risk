import React, { Component } from 'react';

class DeployCard extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    const {unitsToDeploy} = this.props
    return (
      <div>
        <div className="card ml-5 mb-4">
          <div className="card-body">
            <h5 className="card-title">Deploy phase</h5>
            <br/>
            Reinforce your territories            
            <hr/>
            {(unitsToDeploy > 0) ? `You still have ${unitsToDeploy} units to deploy` : 'Deployment complete'}

          </div>
        </div>

      </div>
    )
  }
}

export default DeployCard;