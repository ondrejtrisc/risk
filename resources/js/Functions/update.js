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
    fetch('./initialize/8')
      .then(promise => promise.json())
      .then(data => {
        object.setState({territories: data.territories})
        update.colorTerritories(object.state)
        update.addNumberOfUnits(object.state)
      })
},

  sendAttackToServer: function(attacking, defending, blitz, object) {
    let toSend = {
      attackingTerritory: attacking,
      defendingTerritory: defending,
      blitz: blitz
    }
    console.log(toSend)
    fetch('./attack/8', 
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
  }
}

export default update