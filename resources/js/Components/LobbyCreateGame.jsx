import React, { Component } from 'react'

class LobbyCreateGame extends Component {
  constructor (props) {
    super(props)
    this.state = {
      max_players: "2",
      init_deployment: "random"
    };

    this.handleNumberChange = this.handleNumberChange.bind(this);
    this.handleDeployChange = this.handleDeployChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  handleNumberChange(e){
    this.setState({
      max_players: e.target.value
    })
  }

  handleDeployChange(e){
    this.setState({
      init_deployment: e.target.value
    })
  }

  handleSubmit(e){
    // console.log(e.target)
    let max_players = this.state.max_players;
    let init_deployment = this.state.init_deployment;
    
    let params = {
      max_players,
      init_deployment
    }

    fetch(`../games/store`, 
    {
        method: "POST",
        headers: {
            "Content-Type": "application/json; charset=utf-8",
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            // "Content-Type": "application/x-www-form-urlencoded",
        },
        body: JSON.stringify(params)
    }
    )
    .then(function(response) {
        return response.json(); // parses response as JSON
    })
    .then(data => {
        this.setState ({
          games: data.games,
          users: data.game_users,
          usersList: data.game_users_names_list,
          user: data.user,
          usernames_arr: data.usernames_arr
        })
    });
  }

  render () {
    // const { games, users, usersList, user } = this.props;
    // console.log(this.props)
    let content = (
      <div className="loading">Loading...</div>
    );


    content = (
      <div>
        {
          <div className="card-body" >
            <h3>Create a new game</h3>
            <form onSubmit={e => this.handleSubmit(e)} className="d-flex flex-column"> 
              <label>Number of players: </label>
              <select name="max_players" onChange={e => this.handleNumberChange(e)}>
                  <option value="2">2</option>    
                  <option value="3">3</option>    
                  <option value="4">4</option>    
                  <option value="5">5</option>    
                  <option value="6">6</option>    
              </select>
              <label>Initial troops deployment: </label>
              <select name="init_deployment" onChange={e => this.handleDeployChange(e)}>
                  <option value="random">random</option>    
                  <option value="manual">manual</option>    
              </select>
              <p></p>
              <button type="submit" className="btn btn-info mr-3">Create game</button> 
            </form>
          </div>
        }
      </div>
    )

    return (
      <div className='container py-4'>
        <div className='row justify-content-center'>
          <div className='col-md-12'>
            <div className='card'>
              <div className='card-header'>Create a new game</div>
              <div className='card-body'>

                {content}

              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default LobbyCreateGame