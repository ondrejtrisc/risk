import React, { Component } from 'react'
import LobbyGamesList from './LobbyGamesList'
import LobbyCreateGame from './LobbyCreateGame'
import ButtonLobbyMenu from './ButtonLobbyMenu'


export default class Lobby extends Component {
  constructor () {
    super()
    this.handlePlayClick = this.handlePlayClick.bind(this)
    this.handleJoinClick = this.handleJoinClick.bind(this)
    this.handleDeleteClick = this.handleDeleteClick.bind(this)
    this.handleLaunchClick = this.handleLaunchClick.bind(this)
    this.handleMenuClick = this.handleMenuClick.bind(this)
    this.state = {
      pageIsCreate: false,
      games: null,
      users: null,
      user: null,
      usersList: null,
      usernames_arr: null
    }
  }

  handleMenuClick(e){
    this.setState({
      pageIsCreate: !this.state.pageIsCreate
    })
  }

  handleDeleteClick(e) {
    fetch(`../games/${e.target.value}/delete`, 
    {
        method: "DELETE",
        headers: {
            "Content-Type": "application/json; charset=utf-8",
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            // "Content-Type": "application/x-www-form-urlencoded",
        },
        body: JSON.stringify({id: e.target.value})
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
    });  }

  handleJoinClick(e) {
  
    fetch(`../games/${e.target.value}/update`, 
    {
        method: "POST",
        headers: {
            "Content-Type": "application/json; charset=utf-8",
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            // "Content-Type": "application/x-www-form-urlencoded",
        },
        body: JSON.stringify({id: e.target.value})
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

  handleLaunchClick(e) {
    fetch(`../games/${e.target.value}/launch`, 
    {
        method: "POST",
        headers: {
            "Content-Type": "application/json; charset=utf-8",
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            // "Content-Type": "application/x-www-form-urlencoded",
        },
        body: JSON.stringify({id: e.target.value})
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

  handlePlayClick(e) {
    console.log(e.target.value);
  }

  componentDidMount() {

    fetch('/games')
    .then(response => response.json())
    .then(data => {

        this.setState({
            games: data.games,
            users: data.game_users,
            usersList: data.game_users_names_list,
            user: data.user,
            usernames_arr: data.usernames_arr
        })
    })
  }


  render () {
    let content = '';
    if(this.state.pageIsCreate){ //
      content = (
        <div>
          <LobbyCreateGame 

          />
        </div>
      )
    }else{
      content = (
        <LobbyGamesList 
          games={this.state.games}
          users={this.state.users}
          usersList={this.state.usersList}
          user={this.state.user}
          usernames_arr={this.state.usernames_arr}
          handleDeleteClick={this.handleDeleteClick}
          handleJoinClick={this.handleJoinClick}
          handleLaunchClick={this.handleLaunchClick}
          handlePlayClick={this.handlePlayClick}
        />
      )
    }
    return (
      <>
        <ButtonLobbyMenu 
          pageIsCreate={this.state.pageIsCreate}
          handleMenuClick={this.handleMenuClick}
        />

        {content}
      </>
    )
  }
}

