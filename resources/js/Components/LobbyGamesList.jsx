import React, { Component } from 'react'
import ButtonDelete from './ButtonDelete';
import ButtonJoin from './ButtonJoin';
import ButtonPlay from './ButtonPlay';
import ButtonLaunch from './ButtonLaunch';
import ButtonLeave from './ButtonLeave';

class LobbyGamesList extends Component {
  constructor (props) {
    super(props)
    // this.state = {
    //   games: null
    // }
  }

  render () {
    const { games, users, usersList, user } = this.props;
    // console.log(this.props)
    let content = (
      <div className="loading">Loading...</div>
    );

    const divStyle = {
      'overflowY': 'scroll',
      'height': '280px',
      backgroundColor: "rgb(248,224,176, .2)"
    }

    if (games && users) {    //
      content = (
            <div>


                {
                    games.map(game => (
                      <div key={ game.id } className="card">
                        <div className="card-body" style={{backgroundColor: "rgb(248,224,176, .2)"}}>
                          <div className="created_at"><strong>Created at: </strong> { game.created_at.replace('T', ' ').replace('.000000Z', '') }</div>
                          <div className="founder"><strong>Created by: </strong> { users[game.id][0].name }</div>
                          <div className="players"><strong>Players: </strong> { usersList[game.id] }</div>
                          <div className="max_players"><strong>Maximum number of players: </strong> { game.max_players }</div>
                          <div className="init_deploy"><strong>Initial deployment: </strong> { game.init_deployment }</div>
                          <div className="status"><strong>Status: </strong> { game.status }</div>
                          <div className="row d-flex flex-wrap justify-content-space-between">
                            {game.users_ids.split(";").includes(user.id.toString()) == false 
                              && game.status != 'launched' 
                              && users[game.id].length < game.max_players ? (
                            <div className="col-6">
                              <ButtonJoin
                                handleJoinClick={(e) => this.props.handleJoinClick(e)}
                                game_id = {game.id}
                                />
                            </div>
                            ):('')}
                            {game.users_ids.split(";").includes(user.id.toString()) == true 
                              && users[game.id][0].id != user.id
                              && game.status != 'launched' ? (
                            <div className="col-6">
                              <ButtonLeave
                                handleLeaveClick={(e) => this.props.handleLeaveClick(e)}
                                game_id = {game.id}
                                />
                            </div>
                            ):('')}
                            {game.users_ids.split(";").includes(user.id.toString()) 
                              && game.status == 'launched' ? (
                              // && users[game.id].length == game.max_players ? (
                              <div className="col-6">
                                <ButtonPlay 
                                  handlePlayClick={(e) => this.props.handlePlayClick(e)}
                                  game_id = {game.id}
                                />
                              </div> 
                            ):('')}
                            {users[game.id][0].id == user.id 
                              && game.status != 'launched' ? (
                              // && users[game.id].length == game.max_players ? (
                              <div className="col-6">
                                <ButtonLaunch
                                  handleLaunchClick={(e) => this.props.handleLaunchClick(e)}
                                  game_id = {game.id}
                                />
                              </div>
                            ) : (
                              '')} 
                            {users[game.id][0].id == user.id && game.status != 'launched' ? (
                              <div className="col-6">
                                <ButtonDelete
                                  handleDeleteClick={(e) => this.props.handleDeleteClick(e)}
                                  game_id = {game.id}
                                />
                              </div>
                            ) : (
                              '')} 
                          </div>
                        </div>
                      </div>
                    ))
                }
            </div>
      )
    }

    return (
      <div className='container py-4'>
        <div className='row justify-content-center'>
          <div className='col-md-12'>
            <div className='card'>
              <div className='card-header' style={{backgroundColor: "rgb(248,224,176, .4)"}}>
              <h5 class="text-center" 
                    style=
                    {{
                    fontFamily: "Bree Serif, serif",
                    fontWeight: "200", 
                    fontWeight: "600", 
                    color: "rgb(40,16,16, 1)", 
                    textTransform: "uppercase",
                    letterSpacing: ".1rem"
                  }}

                    >List of games</h5>
                </div>
              <div className='card-body'  style={divStyle}>

                <div>
                  { content }
                </div>
                
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default LobbyGamesList