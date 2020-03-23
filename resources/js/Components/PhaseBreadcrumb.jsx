import React, { Component } from 'react';

class PhaseBreadcrumb extends Component {

  render(){
    return (
      <div className="h-50">
        <nav aria-label="breadcrumb">
          <ol className="breadcrumb">
            <li className="breadcrumb-item">Deploy</li>
            <li className="breadcrumb-item">Combat</li>
            <li className="breadcrumb-item active" aria-current="page">Enforce</li>
          </ol>
        </nav>        
      </div>
    )
  }
}

export default PhaseBreadcrumb;