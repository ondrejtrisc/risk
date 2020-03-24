const update = {
  colorTerritories: function (state) {
    state.territories.map(territory => {
      if(territory.player === 1) {
        document.getElementById(`${territory.name}`).classList.remove("ownedByPlayerBlue")
        document.getElementById(`${territory.name}`).classList.add("ownedByPlayerGreen")
        return ''
      } else if (territory.player === 2) {
        document.getElementById(`${territory.name}`).classList.remove("ownedByPlayerGreen")
        document.getElementById(`${territory.name}`).classList.add(`ownedByPlayerBlue`)
        return ''
      }
      return ''
    })
  },

  addNumberOfUnits: function (state) {
    state.territories.map(territory => {
      document.getElementById(`${territory.name}-units-text`).textContent = `${territory.units}`
    })
  },


  getInitialStateOfGame: function(object) {
    fetch(`../initialize/${object.state.game_id}`)
      .then(promise => promise.json())
      .then(data => {
        object.setState({territories: data.territories})
        update.colorTerritories(object.state)
        update.addNumberOfUnits(object.state)
      })
},

  sendAttackToServer: function(attacking, defending, object) {
    let toSend = {
      attackingTerritory: attacking,
      defendingTerritory: defending,
      blitz: object.state.blitz
    }
    console.log(toSend)
    fetch(`../attack/${object.state.game_id}`, 
    {
        method: "POST",
        headers: {
            "Content-Type": "application/json; charset=utf-8",
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(toSend)
    }
      )
      .then(response => response.json())// parses response as JSON
      .then(data => {
        object.setState({territories: data.territories})
        update.colorTerritories(object.state)
        update.addNumberOfUnits(object.state)
      });
  },

  sendDeployToServer: function(object) {
    let toSend = {
      territories: object.state.territories
    }
    console.log(toSend)
    fetch(`../deploy/${object.state.game_id}`, 
    {
        method: "POST",
        headers: {
            "Content-Type": "application/json; charset=utf-8",
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(toSend)
    }
      )
      .then(response => response.json())// parses response as JSON
      .then(data => {
        object.setState({phase: data.phase})
      });
  },



}

export default update