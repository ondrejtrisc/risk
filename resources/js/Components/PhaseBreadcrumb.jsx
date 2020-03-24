import React, { Component } from 'react';

class PhaseBreadcrumb extends Component {
  constructor(props) {
    super(props)
  }

  render(){
    const { phaseValue, phaseDesc } = this.props
    return (
        <div className="progress h-100 w-100">
          <div  className="progress-bar h-100" 
                role="progressbar" 
                style={{width: phaseValue}}
                aria-valuenow={phaseValue} 
                aria-valuemin="0" 
                aria-valuemax="100">
                  {phaseDesc}
          </div>
        </div>       
    )
  }
}

export default PhaseBreadcrumb;