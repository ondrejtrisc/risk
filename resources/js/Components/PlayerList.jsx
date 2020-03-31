import React, { Component } from 'react';


class PlayerList extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    const { userList, activePlayer, turns, territories, cards } = this.props
    const style = ['#e3342f', '#3490dc', '#38c172', '#ffed4a', '#8B4513', '#9561e2']
    let numberOfCards = []

    if (cards.red) {
      let players = Object.values(cards)
      players.forEach(player => numberOfCards.push(player.length))
    }

    console.log(numberOfCards)


    return (
      <div>
        <ul className="list-group mb-4 ml-5">
          {
            userList.map((user, index) => {
              if (turns[index] === activePlayer) {
                return (
                  <li
                    key={index}
                    className={`list-group-item mb-1 rounded`}
                    style={{ border: `1px solid ${style[index]}` }}
                  >{user}
                    <span className="badge badge-secondary text-uppercase ml-3"> Playing </span>
                  </li>
                )
              } else {
                return (
                  <li
                    key={index}
                    className={`list-group-item mb-1 rounded`}
                    style={{ border: `1px solid ${style[index]}` }}
                  >
                    {user}

                    {[...Array(numberOfCards[index])].map((e, i) =>
                      <img
                        key={i}
                        src={`../images/cards/risk_card.png`}
                        className="rounded float-right m-0"
                        style={{ height: "25px" }}
                        alt={`card_back`}
                      />
                    )
                    }
                  </li>
                )
              }
            })
          }
        </ul>
      </div>
    )
  }
}

export default PlayerList;


