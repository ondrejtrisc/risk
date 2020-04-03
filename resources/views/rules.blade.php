@extends('layouts.app')

@section('content')

<style>
    body{
        height: 100vh;
        background-color: #fff;
        background-image: url("/images/background.jpg");
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        background-size: cover;
    }

    form{
        padding: 0.2em;
    }

    button{
    min-width: 150px;
    }

</style>   

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card" >
                <div class="card-header" style="background-color: rgb(40,16,16, 1)">
                    <h3 class="text-center" 
                    style=
                    "font-family: 'Bree Serif', serif; 
                    font-weight: 200; 
                    font-weight: 600; 
                    color:#F8E0B0; 
                    text-transform: uppercase;
                    letter-spacing: .1rem;

                    ">Rules</h3>
            </div>
                </div>

                <div class="card-body" style="background-color: rgb(248,224,176)">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- @auth --}}
                        <div class="col" style="overflow-y: scroll; height:450px;">
                            {{-- <h2 class="row justify-content-center">Hi {{ Auth::user()->name }}, let's play!)</h2> --}}
                            <div class="row justify-content-center">
                              <h1>OBJECT OF THE GAME</h1>
                              <p>To conquer the world by occupying every territory on the 
                                map, thus eliminating all your opponents.</p> 
                              <h1>SETUP</h1> 
                              <p>Unlike most games, RISK demands careful planning before 
                                you actually start to play. This Initial Army Placement sets 
                                the stage for the battles you’ll fight later on.</p> 
                              <p><strong>INITIAL ARMY PLACEMENT</strong> consists of these steps:</p> 

                              <ol>
                                <li>Depending on the number of players, you will be given a certain number of 
                                  the “armies” you’ll need to start the game.</li> 
                                <ul>
                                  <li>If 2 are playing, each player receives 40 armies.</li> 
                                  <li>If 3 are playing, each player receives 35 armies.</li> 
                                  <li>If 4 are playing, each player receives 30 armies.</li> 
                                  <li>If 5 are playing, each player receives 25 armies.</li>
                                  <li>If 6 are playing, each player receives 20 armies.</li>
                                </ul> 
                                <li>Place one army it onto any territory on the board, 
                                  thus claiming that territory.</li> 
                                <li>Everyone in turn places one army onto any unoccupied territory. 
                                  Continue until all 42 territories have been claimed.</li> 
                                <li>After all 42 territories are claimed, each player in turn 
                                  places one additional army onto any territory he or she already occupies. 
                                  Continue in this way until everyone has run out of armies. There is no limit 
                                  to the number of armies you may place onto a single territory.</li> 
                                <li>Whoever placed the first army takes the first turn.</li> 
                              </ol>
                              <h1>PLAYING</h1> 
                              <p>On your turn, try to capture territories by defeating your opponents’ armies. 
                                But be careful: Winning battles will depend on careful planning, quick decisions 
                                and bold moves. You’ll have to place your forces wisely, attack at just the right 
                                time and fortify your defenses against all enemies.</p> 
                              <p>Each of your turns consists of three steps, in this order:</p> 
                              <ol>
                                <li>Getting and placing new armies;</li> 
                                <li>Attacking, if you choose to;</li> 
                                <li>Fortifying your position.</li>
                              </ol> 
                              <h1>GETTING AND PLACING NEW ARMIES</h1> 
                              <p>At the beginning of each turn, you’ll be given new armies based on:</p> 
                              <ol>
                                <li>The number of territories you occupy;</li> 
                                <li>The value of the continents you control;</li> 
                                <li>The value of the matched sets of RISK cards you trade in;</li> 
                                <li>The specific territory pictured on a traded-in card.</li>
                              </ol> 
                              <p><strong>Territories.</strong> At the beginning of every turn (including your 
                                first), you will receive the number of armies based on the number of territories you 
                                currently occupy. Place the new armies on any territory you already occupy.</p> 
                              <p>Example:</p> 
                              <ul>
                                <li>11 territories = 3 armies</li> 
                                <li>14 territories = 4 armies</li> 
                                <li>17 territories = 5 armies</li>
                              </ul> 
                              <p>You will always receive at least 3 armies on a turn, even if you occupy fewer than 
                                9 territories.</p> 
                              <p><strong>Continents.</strong> In addition, at the beginning of your turn you will 
                                receive armies for each continent you control. (To control a continent, you must occupy 
                                all its territories at the start of your turn.) </p>
                              <h1>RISK CARDS</h1> 
                              <p><strong>Earning Cards.</strong> At the end of any turn in which you have captured at 
                                least one territory, you will earn one (and only one) RISK card. You are trying to 
                                collect sets of 3 cards in any of the following combinations:</p> 
                              <ul>
                                <li>3 cards of same design (Infantry, Cavalry, or Artillery)</li> 
                                <li>1 each of 3 designs</li> 
                                <li>any 2 plus a “wild” card</li>
                              </ul> 
                              <p>If you have collected a set of 3 RISK cards, you may turn them in at the beginning 
                                of your next turn, or you may wait. But if you have 5 or 6 cards at the beginning of 
                                your turn, you must trade in at least one set, and may trade in a second set if you 
                                have one.</p> 
                              <p><strong>Trading In Cards for Armies.</strong> At the beginning of subsequent turns, 
                                you may trade in matched sets of cards and take additional armies based on the total 
                                number of sets anyone has traded in so far. </p>
                              <ul>
                                <li>The first set traded in - 4 armies</li> 
                                <li>The second set traded in - 6 armies </li>
                                <li>The third set traded in - 8 armies </li>
                                <li>The fourth set traded in - 10 armies </li>
                                <li>The fifth set traded in - 12 armies </li>
                                <li>The sixth set traded in - 15 armies </li>
                              </ul>
                              <p>After the sixth set has been traded in, each additional set is worth 5 more armies. 
                                Example: If you trade in the seventh set, you get 20 armies; if you trade in the eighth, 
                                you get 25 armies, and so on. “First” and “second” set, etc., refer to sets traded in by 
                                anyone during the game. Thus, if you trade in the third set in the game, you receive 8 
                                armies, even if it’s the first set you have traded in. </p>
                              <p><strong>Occupied territories.</strong> If any of the 3 cards you trade in shows the 
                                picture of a territory you occupy, you receive 2 extra armies. You must place both those 
                                armies onto that particular territory. </p>
                              <p><strong>Note:</strong> On a single turn, you may receive no more than 2 extra armies 
                                above and beyond those you receive for the matched sets of cards you trade in. </p>
                              <p><strong>Hints:</strong> No matter how many armies you receive at the start of your turn, 
                                deploy them carefully-either to prepare for an attack or to defend against one. It is good 
                                military strategy to move your armies to the front, heavily fortifying territories that 
                                border enemy territories. </p>
                              <h1>ATTACKING</h1> 
                              <p>After placing your armies at the beginning of your turn, decide if you wish to attack at 
                                this time. The object of an attack is to capture a territory by defeating all the opposing 
                                armies already on it. The battle is fought by a roll of the dice. Study the board for a 
                                moment. Do you want to attack? </p>
                              <p>You may choose not to attack. You may still fortify your position, if you wish. </p>
                              <p>If you choose to attack, you must follow these rules: </>
                              <ul>
                                <li>You may only attack a territory that’s adjacent (touching) to one of your own, or 
                                  connected to it by a dashed line. Examples: Greenland may attack the Northwest Territory, 
                                  Ontario, Quebec and Iceland. North Africa may attack Egypt, Western Europe and Brazil. 
                                  At the western and eastern edges of the board, Alaska is considered adjacent to, and may 
                                  attack, Kamchatka. </li>
                                <li>You must always have at least two armies in the territory you’re attacking from. </li>
                                <li>You may continue attacking one territory until you have eliminated all armies on it, or 
                                  you may shift your attack from one territory to another, attacking each as often as you 
                                  like and attacking as many territories as you like during one turn. </li>
                              </ul>
                              <p><strong>TO ATTACK.</strong> First select both the territory you’re attacking from and the one 
                                you’re attacking.</p>
                              {{-- <p><strong>Hint:</strong> The more dice you roll, the greater your odds of winning. Yet the
                                 more dice you roll, the more armies you may lose, or be required to move into a captured 
                                 territory. </p> --}}
                              {{-- <p>The defender will roll either 1 or 2 white dice: To roll 2 dice, he or she must have at 
                                least 2 armies on the territory under attack. Hint: The more dice the defender rolls, the 
                                greater his or her odds of winning-but the more armies he or she may lose. </p> --}}
                              <p><strong>To Decide a Battle.</strong> Compare the highest die each of you rolled. If yours 
                                (the attacker’s) is higher, the defender loses one army from the territory under attack. 
                                But if the defender’s die is higher than yours, you lose one army from the territory you 
                                attacked from. If each of you rolled more than one die, now compare the two next-highest 
                                dice and repeat the process. </p>
                              <p><strong>Notes:</strong></p> 
                              <ul>
                                <li>In case of a tie, the defender always wins.</li> 
                                <li>The attacker can never lose more than 2 armies on a single roll.</li>
                              </ul>                               
                              <p><strong>Capturing territories.</strong> As soon as you defeat the last opposing army on a 
                                territory, you capture that territory and must occupy it immediately. 
                                {{-- To do so, move in at least as many armies as the number of dice you rolled in your last 
                                battle. Remember: In most cases, moving as many armies as you can to the front is 
                                advantageous, because armies left behind can’t help you when you are attacking. 
                                Also remember you must always leave at least one army behind on the territory you attacked 
                                from.  --}}
                                During the game, every territory must always be occupied by at least one army. </p>
                              <p><strong>Ending your attack.</strong> You may end your attack(s) at any time. If you have 
                                captured at least one territory, you will receive the RISK card. (No matter how many 
                                territories you’ve captured on your turn, you receive only one RISK card.) Your last step 
                                is to fortify your position, if you wish (see below). Finally, pass the turn. </p>
                              <p><strong>Eliminating an opponent.</strong> If during your turn you eliminate an opponent 
                                by defeating his or her last army on the game board, you win any RISK cards that player has 
                                collected. </p>
                              <p>If winning them gives you 6 or more cards, you must immediately trade in enough sets to 
                                reduce your hand to 4 or fewer cards, but once your hand is reduced to 4,3, or 2 cards, 
                                you must stop trading. </p>
                              <p>But if winning them gives you fewer than 6, you must wait until the beginning of your next 
                                turn to trade in a set. </p>
                              <p><strong>Note:</strong> When you draw a card from the deck at the end of your turn (for 
                                having won a battle), if this brings your total to 6, you must wait until your next turn 
                                to trade in. </p>
                              <h1>FORTIFYING YOUR POSITION</h1> 
                              <p>No matter what you’ve done on your turn, you may, if you wish, end your turn by fortifying 
                                your position. You are not required to win a battle or even to try an attack to do so. Some 
                                players refer to this as the “free move.” </p>
                              <p>To fortify your position, move as many armies as you’d like from one (and only one) of your 
                                territories into one (and only one) of your adjacent territories. Remember to move troops 
                                towards borders where they can help in an attack! </p>
                              <p>In moving your armies from one territory to another, you must leave at least one army 
                                behind. </p>
                              <h1>WINNING</h1> 
                              <p>The winner is the first player to eliminate every opponent by capturing all 42 territories 
                                on the board.</p>
                          </div>
                        </div>
                    {{-- @endauth --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
