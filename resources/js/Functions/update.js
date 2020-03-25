const update = {
  colorTerritories: function (state) {
    state.territories.map(territory => {
        document.getElementById(`${territory.name}`).classList.remove('blue')
        document.getElementById(`${territory.name}`).classList.remove('green')
        document.getElementById(`${territory.name}`).classList.remove('yellow')
        document.getElementById(`${territory.name}`).classList.remove('purple')
        document.getElementById(`${territory.name}`).classList.remove('brown')
        document.getElementById(`${territory.name}`).classList.remove('red')
        document.getElementById(`${territory.name}`).classList.add(`${territory.player}`)
        return ''
    })
  },

  addNumberOfUnits: function (state) {
    state.territories.map(territory => {
      document.getElementById(`${territory.name}-units-text`).textContent = `${territory.units}`
    })
  },


  getStateOfGame: function(object) {
    fetch(`../${object.state.game_id}`)
      .then(promise => promise.json())
      .then(data => {
        console.log(data)
        object.setState({territories: data.territories})
        object.setState({turns: data.players})
        object.setState({unitsToDeploy: data.unitsToDeploy})
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