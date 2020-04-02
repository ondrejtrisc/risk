<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Game;
use App\Gamestate;
use stdClass;

class GamestateController extends Controller
{
    public function create_initial($game_id, $players, $computerPlayers = [])
    {
        $game = Game::findOrFail($game_id);
        if($game->init_deployment === 'random')
        {
            $this->create_initial_random($game_id, $players, $computerPlayers);
        }
        else
        {
            $this->create_initial_manual($game_id, $players, $computerPlayers);
        }
    }

    public function create_initial_random($game_id, $players, $computerPlayers)
    {
        $gamestate = new Gamestate();
        $gamestate->game_id = $game_id;
        $gamestate->step = 1;

        $state = new stdClass();
        $state->players = $players;
        $state->computerPlayers = $computerPlayers;
        $state->territories = [];
        
        $territoryNames = ['alaska', 'northwest_territory', 'greenland', 'alberta', 'ontario', 'eastern_canada', 'western_united_states', 'eastern_united_states', 'central_america', 'venezuela', 'peru', 'brazil', 'argentina', 'iceland', 'scandinavia', 'great_britain', 'northern_europe', 'western_europe', 'southern_europe', 'russia', 'north_africa', 'egypt', 'east_africa', 'central_africa', 'south_africa', 'madagascar', 'ural', 'siberia', 'yakutsk', 'irkutsk', 'kamchatka', 'afghanistan', 'china', 'mongolia', 'japan', 'middle_east', 'india', 'southeast_asia', 'indonesia', 'new_guinea', 'western_australia', 'eastern_australia'];
        
        //creates the deck of cards
        $deck = [];
        foreach ($territoryNames as $territoryName)
        {
            $card = new stdClass();
            $card->territory = $territoryName;
            switch ($territoryName)
            {                  
                case 'alaska':
                case 'argentina':
                case 'central_africa':
                case 'china':
                case 'east_africa':
                case 'egypt':
                case 'iceland':
                case 'kamchatka':
                case 'middle_east':
                case 'mongolia':
                case 'new_guinea':
                case 'peru':
                case 'southeast_asia':
                case 'venezuela':
                    $card->type = 'infantry';
                    break;
                case 'afghanistan':
                case 'alberta':
                case 'eastern_canada':
                case 'greenland':
                case 'india':
                case 'irkutsk':
                case 'madagascar':
                case 'north_africa':
                case 'ontario':
                case 'russia':
                case 'scandinavia':
                case 'siberia':
                case 'ural':
                case 'yakutsk':
                    $card->type = 'cavalry';
                    break;
                case 'brazil':
                case 'central_america':
                case 'eastern_australia':
                case 'eastern_united_states':
                case 'great_britain':
                case 'indonesia':
                case 'japan':
                case 'northern_europe':
                case 'northwest_territory':
                case 'south_africa':
                case 'southern_europe':
                case 'western_australia':
                case 'western_europe':
                case 'western_united_states':
                    $card->type = 'artillery';
                    break;
            }
            $deck[] = $card;
        }
        $card = new stdClass();
        $card->territory = 'wild';
        $card->type = 'wild';
        $deck[] = $card;
        $card = new stdClass();
        $card->territory = 'wild';
        $card->type = 'wild';
        $deck[] = $card;
        shuffle($deck);
        $state->deck = $deck;

        //creates the cards object
        $cards = new stdClass();
        foreach ($state->players as $player)
        {
            $cards->$player = [];
        }
        $state->cards = $cards;
        $state->matches = 0;

        //creates the territories
        foreach ($territoryNames as $name)
        {
            $territory = new stdClass();
            $territory->name = $name;
            $territory->player = null;
            $territory->units = 0;

            $state->territories[] = $territory;
        }

        //initial distribution of territories (one troop added to each territory)
        switch (count($state->players))
        {
            case 2:
                $initial_units = 50;
                break;
            case 3:
                $initial_units = 35;
                break;
            case 4:
                $initial_units = 30;
                break;
            case 5:
                $initial_units = 25;
                break;
            case 6:
                $initial_units = 20;
                break;          
        }

        $occupiedBy = [];
        $unitsToDistribute = new stdClass();
        foreach ($state->players as $player)
        {
            $occupiedBy[$player] = [];
            $unitsToDistribute->$player = $initial_units;
        }

        $territories = $state->territories;
        shuffle($territories);
        $playerIndex = 0;
        foreach ($territories as $territory)
        {
            $player = $state->players[$playerIndex];
            $territory->player = $player;
            $territory->units = 1;
            $occupiedBy[$player][] = $territory;
            $unitsToDistribute->$player--;

            if ($playerIndex === count($state->players) - 1)
            {
                $playerIndex = 0;
            }
            else
            {
                $playerIndex++;
            }
        }

        //distribution of remaining troops
        while (true)
        {
            $player = $state->players[$playerIndex];
            $territory = $occupiedBy[$player][rand(0, count($occupiedBy[$player]) - 1)];
            $territory->units++;
            $unitsToDistribute->$player--;

            if ($playerIndex === count($state->players) - 1)
            {
                $playerIndex = 0;
            }
            else
            {
                $playerIndex++;
            }

            $player = $players[$playerIndex];
            if($unitsToDistribute->$player === 0)
            {
                break;
            }
        }

        //sets the first player's turn
        $state->turn = 0;

        //sets the phase to deploy
        $state->phase = 'deploy';
        $state->attackerDice = null;
        $state->defenderDice = null;
        $state->attackerLost = null;
        $state->defenderLost = null;

        //calculates how many units the first player can deploy
        $playerHeldTerritories = 0;
        foreach ($state->territories as $territory)
        {
            if ($territory->player === $state->players[$state->turn])
            {
                $playerHeldTerritories++;
            }
        }
        $state->unitsOfTerritories = max(3, floor($playerHeldTerritories / 3));
        $state->unitsToDeploy = $state->unitsOfTerritories;
        
        $state->unitsOfNorthAmerica = null;
        $state->unitsOfSouthAmerica = null;
        $state->unitsOfEurope = null;
        $state->unitsOfAfrica = null;
        $state->unitsOfAsia = null;
        $state->unitsOfAustralia = null;
        $holdsNorthAmerica = true;
        for ($i = 0; $i < 9; $i++)
        {
            if ($state->territories[$i]->player !== $state->players[$state->turn])
            {
                $holdsNorthAmerica = false;
                break;
            }
        }
        if ($holdsNorthAmerica)
        {
            $state->unitsToDeploy += 5;
            $state->unitsOfNorthAmerica = 5;
        }
        $holdsSouthAmerica = true;
        for ($i = 9; $i < 13; $i++)
        {
            if ($state->territories[$i]->player !== $state->players[$state->turn])
            {
                $holdsSouthAmerica = false;
                break;
            }
        }
        if ($holdsSouthAmerica)
        {
            $state->unitsToDeploy += 2;
            $state->unitsOfSouthAmerica = 2;
        }
        $holdsEurope = true;
        for ($i = 13; $i < 20; $i++)
        {
            if ($state->territories[$i]->player !== $state->players[$state->turn])
            {
                $holdsEurope = false;
                break;
            }
        }
        if ($holdsEurope)
        {
            $state->unitsToDeploy += 5;
            $state->unitsOfEurope = 5;
        }
        $holdsAfrica = true;
        for ($i = 20; $i < 26; $i++)
        {
            if ($state->territories[$i]->player !== $state->players[$state->turn])
            {
                $holdsAfrica = false;
                break;
            }
        }
        if ($holdsAfrica)
        {
            $state->unitsToDeploy += 3;
            $state->unitsOfAfrica = 3;
        }
        $holdsAsia = true;
        for ($i = 26; $i < 38; $i++)
        {
            if ($state->territories[$i]->player !== $state->players[$state->turn])
            {
                $holdsAsia = false;
                break;
            }
        }
        if ($holdsAsia)
        {
            $state->unitsToDeploy += 7;
            $state->unitsOfAsia = 7;
        }
        $holdsAustralia = true;
        for ($i = 38; $i < 42; $i++)
        {
            if ($state->territories[$i]->player !== $state->players[$state->turn])
            {
                $holdsAustralia = false;
                break;
            }
        }
        if ($holdsAustralia)
        {
            $state->unitsToDeploy += 2;
            $state->unitsOfAustralia = 2;
        }

        $gamestate->state = json_encode($state);

        $gamestate->save();
    }

    public function create_initial_manual($game_id, $players, $computerPlayers)
    {
        $gamestate = new Gamestate();
        $gamestate->game_id = $game_id;
        $gamestate->step = 1;

        $state = new stdClass();
        $state->players = $players;
        $state->computerPlayers = $computerPlayers;
        $state->territories = [];
        
        $territoryNames = ['alaska', 'northwest_territory', 'greenland', 'alberta', 'ontario', 'eastern_canada', 'western_united_states', 'eastern_united_states', 'central_america', 'venezuela', 'peru', 'brazil', 'argentina', 'iceland', 'scandinavia', 'great_britain', 'northern_europe', 'western_europe', 'southern_europe', 'russia', 'north_africa', 'egypt', 'east_africa', 'central_africa', 'south_africa', 'madagascar', 'ural', 'siberia', 'yakutsk', 'irkutsk', 'kamchatka', 'afghanistan', 'china', 'mongolia', 'japan', 'middle_east', 'india', 'southeast_asia', 'indonesia', 'new_guinea', 'western_australia', 'eastern_australia'];
        
        //creates the deck of cards
        $deck = [];
        foreach ($territoryNames as $territoryName)
        {
            $card = new stdClass();
            $card->territory = $territoryName;
            switch ($territoryName)
            {                  
                case 'alaska':
                case 'argentina':
                case 'central_africa':
                case 'china':
                case 'east_africa':
                case 'egypt':
                case 'iceland':
                case 'kamchatka':
                case 'middle_east':
                case 'mongolia':
                case 'new_guinea':
                case 'peru':
                case 'southeast_asia':
                case 'venezuela':
                    $card->type = 'infantry';
                break;
                case 'afghanistan':
                case 'alberta':
                case 'eastern_canada':
                case 'greenland':
                case 'india':
                case 'irkutsk':
                case 'madagascar':
                case 'north_africa':
                case 'ontario':
                case 'russia':
                case 'scandinavia':
                case 'siberia':
                case 'ural':
                case 'yakutsk':
                    $card->type = 'cavalry';
                break;
                case 'brazil':
                case 'central_america':
                case 'eastern_australia':
                case 'eastern_united_states':
                case 'great_britain':
                case 'indonesia':
                case 'japan':
                case 'northern_europe':
                case 'northwest_territory':
                case 'south_africa':
                case 'southern_europe':
                case 'western_australia':
                case 'western_europe':
                case 'western_united_states':
                    $card->type = 'artillery';
                break;
            }
            $deck[] = $card;
        }
        $card = new stdClass();
        $card->territory = 'wild';
        $card->type = 'wild';
        $deck[] = $card;
        $card = new stdClass();
        $card->territory = 'wild';
        $card->type = 'wild';
        $deck[] = $card;
        shuffle($deck);
        $state->deck = $deck;

        //creates the cards object
        $cards = new stdClass();
        foreach ($state->players as $player)
        {
            $cards->$player = [];
        }
        $state->cards = $cards;
        $state->matches = 0;
        
        //creates the territories
        foreach ($territoryNames as $name)
        {
            $territory = new stdClass();
            $territory->name = $name;
            $territory->player = null;
            $territory->units = 0;

            $state->territories[] = $territory;
        }

        switch (count($state->players))
        {
            case 2:
                $initial_units = 50;
            break;
            case 3:
                $initial_units = 35;
            break;
            case 4:
                $initial_units = 30;
            break;
            case 5:
                $initial_units = 25;
            break;
            case 6:
                $initial_units = 20;
            break;          
        }

        $unitsToDistribute = new stdClass();
        foreach ($state->players as $player)
        {
            $unitsToDistribute->$player = $initial_units;
        }

        //sets the first player's turn
        $state->turn = 0;

        //sets the phase to occupy
        $state->phase = 'occupy';
        $state->unitsToDistribute = $unitsToDistribute;
        $state->unitsToDeploy = null;
        $state->attackerDice = null;
        $state->defenderDice = null;
        $state->attackerLost = null;
        $state->defenderLost = null;

        $gamestate->state = json_encode($state);

        $gamestate->save();
    }

    public function occupy($game_id)
    {
        //gets the data about the move from the request
        $requestPayload = file_get_contents("php://input");
        $object = json_decode($requestPayload);

        return $this->occupy_process($game_id, $object);
    }

    public function occupy_process($game_id, $object)
    {
        //gets the most recent gamestate from the database
        $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
        $state = json_decode($gamestate->state);

        //locates the occupied territory
        $territoryName = $object->territory;
        foreach ($state->territories as $territory)
        {
            if ($territory->name === $territoryName)
            {
                $occupiedTerritory = $territory;
                break;
            }
        }

        //assigns the territory to the player
        $player = $state->players[$state->turn];
        $occupiedTerritory->player = $player;
        $occupiedTerritory->units = 1;
        $state->unitsToDistribute->$player--;

        //makes it the next player's turn
        if ($state->turn === count($state->players) - 1)
        {
            $state->turn = 0;
        }
        else
        {
            $state->turn++;
        }

        //checks if all territories are occupied
        $allTerritoriesOccupied = true;
        foreach ($state->territories as $territory)
        {
            if ($territory->units === 0)
            {
                $allTerritoriesOccupied = false;
                break;
            }
        }

        if ($allTerritoriesOccupied)
        {
            //sets the phase to strengthen
            $state->phase = 'strengthen';
        }

        //creates the new gamestate
        $newGamestate = new Gamestate();
        $newGamestate->game_id = $game_id;
        $newGamestate->step = $gamestate->step + 1;
        $newGamestate->state = json_encode($state);

        //saves the new gamestate to the database
        $newGamestate->save();

        //checks if it is a computer's turn and if so, lets it play
        if (array_search($state->turn, $state->computerPlayers))
        {
            $this->computers_turn($game_id);
            return $this->get_current_state($game_id);
        }

        //returns the new state to the frontend
        unset($state->deck);
        $state->turn = $state->players[$state->turn];
        return json_encode($state);
    }

    public function strengthen($game_id)
    {
        //gets the data about the move from the request
        $requestPayload = file_get_contents("php://input");
        $object = json_decode($requestPayload);

        return $this->strengthen_process($game_id, $object);
    }

    public function strengthen_process($game_id, $object)
    {
        //gets the most recent gamestate from the database
        $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
        $state = json_decode($gamestate->state);

        //locates the strengthened territory
        $territoryName = $object->territory;
        foreach ($state->territories as $territory)
        {
            if ($territory->name === $territoryName)
            {
                $strengthenedTerritory = $territory;
                break;
            }
        }

        //strengthens the territory
        $strengthenedTerritory->units++;
        $player = $state->players[$state->turn];
        $state->unitsToDistribute->$player--;

        //makes it the next player's turn
        if ($state->turn === count($state->players) - 1)
        {
            $state->turn = 0;
        }
        else
        {
            $state->turn++;
        }

        //checks if all the initial troops are distributed
        $player = $state->players[$state->turn];
        if($state->unitsToDistribute->$player === 0)
        {
            //sets the phase to deploy
            $state->phase = 'deploy';
            $state->unitsToDistribute = null;

            //calculates how many units the first player can deploy
            $playerHeldTerritories = 0;
            foreach ($state->territories as $territory)
            {
                if ($territory->player === $state->players[$state->turn])
                {
                    $playerHeldTerritories++;
                }
            }
            $state->unitsOfTerritories = max(3, floor($playerHeldTerritories / 3));
            $state->unitsToDeploy = $state->unitsOfTerritories;
            
            $state->unitsOfNorthAmerica = null;
            $state->unitsOfSouthAmerica = null;
            $state->unitsOfEurope = null;
            $state->unitsOfAfrica = null;
            $state->unitsOfAsia = null;
            $state->unitsOfAustralia = null;
            $holdsNorthAmerica = true;
            for ($i = 0; $i < 9; $i++)
            {
                if ($state->territories[$i]->player !== $state->players[$state->turn])
                {
                    $holdsNorthAmerica = false;
                    break;
                }
            }
            if ($holdsNorthAmerica)
            {
                $state->unitsToDeploy += 5;
                $state->unitsOfNorthAmerica = 5;
            }
            $holdsSouthAmerica = true;
            for ($i = 9; $i < 13; $i++)
            {
                if ($state->territories[$i]->player !== $state->players[$state->turn])
                {
                    $holdsSouthAmerica = false;
                    break;
                }
            }
            if ($holdsSouthAmerica)
            {
                $state->unitsToDeploy += 2;
                $state->unitsOfSouthAmerica = 2;
            }
            $holdsEurope = true;
            for ($i = 13; $i < 20; $i++)
            {
                if ($state->territories[$i]->player !== $state->players[$state->turn])
                {
                    $holdsEurope = false;
                    break;
                }
            }
            if ($holdsEurope)
            {
                $state->unitsToDeploy += 5;
                $state->unitsOfEurope = 5;
            }
            $holdsAfrica = true;
            for ($i = 20; $i < 26; $i++)
            {
                if ($state->territories[$i]->player !== $state->players[$state->turn])
                {
                    $holdsAfrica = false;
                    break;
                }
            }
            if ($holdsAfrica)
            {
                $state->unitsToDeploy += 3;
                $state->unitsOfAfrica = 3;
            }
            $holdsAsia = true;
            for ($i = 26; $i < 38; $i++)
            {
                if ($state->territories[$i]->player !== $state->players[$state->turn])
                {
                    $holdsAsia = false;
                    break;
                }
            }
            if ($holdsAsia)
            {
                $state->unitsToDeploy += 7;
                $state->unitsOfAsia = 7;
            }
            $holdsAustralia = true;
            for ($i = 38; $i < 42; $i++)
            {
                if ($state->territories[$i]->player !== $state->players[$state->turn])
                {
                    $holdsAustralia = false;
                    break;
                }
            }
            if ($holdsAustralia)
            {
                $state->unitsToDeploy += 2;
                $state->unitsOfAustralia = 2;
            }
        }

        //creates the new gamestate
        $newGamestate = new Gamestate();
        $newGamestate->game_id = $game_id;
        $newGamestate->step = $gamestate->step + 1;
        $newGamestate->state = json_encode($state);

        //saves the new gamestate to the database
        $newGamestate->save();

        //checks if it is a computer's turn and if so, lets it play
        if (array_search($state->turn, $state->computerPlayers))
        {
            $this->computers_turn($game_id);
            return $this->get_current_state($game_id);
        }

        //returns the new state to the frontend
        unset($state->deck);
        $state->turn = $state->players[$state->turn];
        return json_encode($state);
    }

    public function get_current_state($game_id)
    {
        try
        {
            $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
            $state = json_decode($gamestate->state);
            unset($state->deck);
            $state->turn = $state->players[$state->turn];
            return json_encode($state);
        }
        catch (Exception $err)
        {
            return json_encode(new stdClass());
        }
    }

    public function play_cards($game_id)
    {
        //gets the data about the move from the request
        $requestPayload = file_get_contents("php://input");
        $object = json_decode($requestPayload);

        return $this->play_cards_process($game_id, $object);
    }

    public function play_cards_process($game_id, $object)
    {
        //gets the most recent gamestate from the database
        $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
        $state = json_decode($gamestate->state);
        
        $set = $object->set;

        //adds extra units to deploy
        $state->matches++;
        $state->unitsOfCards = 0;
        if ($state->matches < 6)
        {
            $state->unitsOfCards += $state->matches * 2 + 2;
        }
        else
        {
            $state->unitsOfCards += $state->matches * 5 - 15;
        }
        $state->unitsToDeploy += $state->unitsOfCards;

        $territoryBonus = false;
        $state->bonusTerritory = null;
        foreach ($set as $card)
        {
            foreach ($state->territories as $territory)
            {
                if ($territory->name === $card->territory)
                {
                    if ($territory->player === $state->players[$state->turn])
                    {
                        $territoryBonus = true;
                        $state->bonusTerritory = $territory->name;
                    }
                    break;
                }
            }
        }
        if ($territoryBonus)
        {
            $state->unitsToDeploy += 2;
        }

        //returns the cards to the back of the deck
        $player = $state->players[$state->turn];
        foreach ($set as $cardPlayed)
        {
            $index = null;
            foreach ($state->cards->$player as $i => $cardHeld)
            {
                if ($cardPlayed->territory === $cardHeld->territory && $cardPlayed->type === $cardHeld->type)
                {
                    $index = $i;    
                    break;
                }
            }
            array_splice($state->cards->$player, $index, 1);
        }
        $state->deck = array_merge($state->deck, $set);

        //creates the new gamestate
        $newGamestate = new Gamestate();
        $newGamestate->game_id = $game_id;
        $newGamestate->step = $gamestate->step + 1;
        $newGamestate->state = json_encode($state);

        //saves the new gamestate to the database
        $newGamestate->save();

        //returns the new state to the frontend
        unset($state->deck);
        $state->turn = $state->players[$state->turn];
        return json_encode($state);
    }

    public function deploy($game_id)
    {
        //gets the data about the move from the request
        $requestPayload = file_get_contents("php://input");
        $object = json_decode($requestPayload);

        return $this->deploy_process($game_id, $object);
    }

    public function deploy_process($game_id, $object)
    {
        //gets the most recent gamestate from the database
        $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
        $state = json_decode($gamestate->state);

        $state->territories = $object->territories;

        //changes the phase to attack
        $state->phase = 'attack';
        $state->unitsToDeploy = null;
        $state->hasGainedTerritory = false;

        //creates the new gamestate
        $newGamestate = new Gamestate();
        $newGamestate->game_id = $game_id;
        $newGamestate->step = $gamestate->step + 1;
        $newGamestate->state = json_encode($state);

        //saves the new gamestate to the database
        $newGamestate->save();

        //returns the new state to the frontend
        unset($state->deck);
        $state->turn = $state->players[$state->turn];
        return json_encode($state);
    }

    public function attack($game_id)
    {
        //gets the data about the move from the request
        $requestPayload = file_get_contents("php://input");
        $object = json_decode($requestPayload);

        return $this->attack_process($game_id, $object);
    }

    public function attack_process($game_id, $object)
    {
        //gets the most recent gamestate from the database
        $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
        $state = json_decode($gamestate->state);
        
        $fromName = $object->attackingTerritory;
        $toName = $object->defendingTerritory;
        $blitz = ($object->blitz == 'true');

        //locates the attacking territory
        foreach ($state->territories as $territory)
        {
            if ($territory->name === $fromName)
            {
                $fromTerritory = $territory;
                break;
            }
        }

        //locates the defending territory
        foreach ($state->territories as $territory)
        {
            if ($territory->name === $toName)
            {
                $toTerritory = $territory;
                break;
            }
        }

        //battle
        $attackerLost = 0;
        $defenderLost = 0;
        $defender = $toTerritory->player;
        $defenderEliminated = false;
        do
        {
            $fromDiceNumber = min($fromTerritory->units - 1, 3);
            $fromDice = [];
            for ($i = 0; $i < $fromDiceNumber; $i++)
            {
                $fromDice[] = rand(1, 6);
            }
            $state->attackerDice = $fromDice;
            rsort($fromDice);
            
            $toDiceNumber = min($toTerritory->units, 2);
            $toDice = [];
            for ($i = 0; $i < $toDiceNumber; $i++)
            {
                $toDice[] = rand(1, 6);
            }
            $state->defenderDice = $toDice;
            rsort($toDice);

            for ($i = 0; $i < min($fromDiceNumber, $toDiceNumber); $i++)
            {
                if ($fromDice[$i] > $toDice[$i])
                {
                    $toTerritory->units -= 1;
                    $defenderLost++;
                }
                else
                {
                    $fromTerritory->units -= 1;
                    $attackerLost++;
                }
            }

            if ($toTerritory->units === 0)
            {
                $toTerritory->player = $fromTerritory->player;
                $state->hasGainedTerritory = true;
                $toTerritory->units = $fromTerritory->units - 1;
                $fromTerritory->units = 1;

                $defenderEliminated = true;
                foreach($state->territories as $territory)
                {
                    if ($territory->player === $defender)
                    {
                        $defenderEliminated = false;
                    }
                }

                break;
            }
        }
        while ($blitz == 'true' && $fromTerritory->units > 1);

        if ($blitz == 'true')
        {
            $state->attackerDice = ['blitz'];
            $state->defenderDice = null;
            $state->attackerLost = $attackerLost;
            $state->defenderLost = $defenderLost;
        }

        if ($defenderEliminated)
        {
            $player = $state->players[$state->turn];

            $defenderIndex = array_search($defender, $state->players);
            array_splice($state->players, $defenderIndex, 1);

            if (count($state->cards->$defender) > 0)
            {
                $state->phase = 'deploy';
                $state->unitsToDeploy = 0;
            }
            $state->cards->$player = array_merge($state->cards->$player, $state->cards->$defender);
            unset($state->cards->$defender);

            if (array_search($defenderIndex, $state->computerPlayers))
            {
                array_splice($state->computerPlayers, $defenderIndex, 1);
            }
            foreach ($state->computerPlayers as $computerPlayer)
            {
                if ($computerPlayer > $defenderIndex)
                {
                    $computerPlayer--;
                }
            }

            $state->playerEliminated = $defender;
        }

        //creates the new gamestate
        $newGamestate = new Gamestate();
        $newGamestate->game_id = $game_id;
        $newGamestate->step = $gamestate->step + 1;
        $newGamestate->state = json_encode($state);

        //saves the new gamestate to the database
        $newGamestate->save();

        //returns the new state to the frontend
        unset($state->deck);
        $state->turn = $state->players[$state->turn];
        return json_encode($state);
    }

    public function fortify($game_id)
    {
        //gets the data about the move from the request
        $requestPayload = file_get_contents("php://input");
        $object = json_decode($requestPayload);

        return $this->fortify_process($game_id, $object);
    }
    
    public function fortify_process($game_id, $object)
    {
        //gets the most recent gamestate from the database
        $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
        $state = json_decode($gamestate->state);

        $fromName = $object->fromTerritory;
        if ($fromName != null)
        {
            $toName = $object->toTerritory;
            $fromUnits = $object->fromUnits;
            $toUnits = $object->toUnits;

            //locates the territory of units' departure
            foreach ($state->territories as $territory)
            {
                if ($territory->name === $fromName)
                {
                    $fromTerritory = $territory;
                    break;
                }
            }

            //locates the territory of units' destination
            foreach ($state->territories as $territory)
            {
                if ($territory->name === $toName)
                {
                    $toTerritory = $territory;
                    break;
                }
            }

            //moves the units
            $fromTerritory->units = $fromUnits;
            $toTerritory->units = $toUnits;
        }

        //awards the player a card
        if ($state->hasGainedTerritory)
        {
            $player = $state->players[$state->turn];
            $state->cards->$player[] = array_shift($state->deck);
            $state->hasGainedTerritory = null;
        }

        //makes it the next player's turn
        if ($state->turn === count($state->players) - 1)
        {
            $state->turn = 0;
        }
        else
        {
            $state->turn++;
        }

        //changes the phase to deploy
        $state->phase = 'deploy';
        $state->attackerDice = null;
        $state->defenderDice = null;
        $state->attackerLost = null;
        $state->defenderLost = null;

        //calculates how many units the first player can deploy
        $playerHeldTerritories = 0;
        foreach ($state->territories as $territory)
        {
            if ($territory->player === $state->players[$state->turn])
            {
                $playerHeldTerritories++;
            }
        }
        $state->unitsOfTerritories = max(3, floor($playerHeldTerritories / 3));
        $state->unitsToDeploy = $state->unitsOfTerritories;
        
        $state->unitsOfNorthAmerica = null;
        $state->unitsOfSouthAmerica = null;
        $state->unitsOfEurope = null;
        $state->unitsOfAfrica = null;
        $state->unitsOfAsia = null;
        $state->unitsOfAustralia = null;
        $holdsNorthAmerica = true;
        for ($i = 0; $i < 9; $i++)
        {
            if ($state->territories[$i]->player !== $state->players[$state->turn])
            {
                $holdsNorthAmerica = false;
                break;
            }
        }
        if ($holdsNorthAmerica)
        {
            $state->unitsToDeploy += 5;
            $state->unitsOfNorthAmerica = 5;
        }
        $holdsSouthAmerica = true;
        for ($i = 9; $i < 13; $i++)
        {
            if ($state->territories[$i]->player !== $state->players[$state->turn])
            {
                $holdsSouthAmerica = false;
                break;
            }
        }
        if ($holdsSouthAmerica)
        {
            $state->unitsToDeploy += 2;
            $state->unitsOfSouthAmerica = 2;
        }
        $holdsEurope = true;
        for ($i = 13; $i < 20; $i++)
        {
            if ($state->territories[$i]->player !== $state->players[$state->turn])
            {
                $holdsEurope = false;
                break;
            }
        }
        if ($holdsEurope)
        {
            $state->unitsToDeploy += 5;
            $state->unitsOfEurope = 5;
        }
        $holdsAfrica = true;
        for ($i = 20; $i < 26; $i++)
        {
            if ($state->territories[$i]->player !== $state->players[$state->turn])
            {
                $holdsAfrica = false;
                break;
            }
        }
        if ($holdsAfrica)
        {
            $state->unitsToDeploy += 3;
            $state->unitsOfAfrica = 3;
        }
        $holdsAsia = true;
        for ($i = 26; $i < 38; $i++)
        {
            if ($state->territories[$i]->player !== $state->players[$state->turn])
            {
                $holdsAsia = false;
                break;
            }
        }
        if ($holdsAsia)
        {
            $state->unitsToDeploy += 7;
            $state->unitsOfAsia = 7;
        }
        $holdsAustralia = true;
        for ($i = 38; $i < 42; $i++)
        {
            if ($state->territories[$i]->player !== $state->players[$state->turn])
            {
                $holdsAustralia = false;
                break;
            }
        }
        if ($holdsAustralia)
        {
            $state->unitsToDeploy += 2;
            $state->unitsOfAustralia = 2;
        }

        //creates the new gamestate
        $newGamestate = new Gamestate();
        $newGamestate->game_id = $game_id;
        $newGamestate->step = $gamestate->step + 1;
        $newGamestate->state = json_encode($state);

        //saves the new gamestate to the database
        $newGamestate->save();

        //checks if it is a computer's turn and if so, lets it play
        if (array_search($state->turn, $state->computerPlayers))
        {
            $this->computers_turn($game_id);
            return $this->get_current_state($game_id);
        }

        //returns the new state to the frontend
        unset($state->deck);
        $state->turn = $state->players[$state->turn];
        return json_encode($state);
    }

    public function computers_turn($game_id)
    {
        //gets the most recent gamestate from the database
        $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
        $state = json_decode($gamestate->state);
        $player = $state->players[$state->turn];

        $neighbours = [

            'alaska' => ['northwest_territory', 'alberta', 'kamchatka'],
            'northwest_territory' => ['alaska', 'alberta', 'ontario', 'greenland'],
            'greenland' => ['northwest_territory', 'ontario', 'eastern_canada', 'iceland'],
            'alberta' => ['alaska', 'northwest_territory', 'ontario', 'western_united_states'],
            'ontario' => ['northwest_territory', 'greenland', 'alberta', 'eastern_canada', 'western_united_states', 'eastern_united_states'],
            'eastern_canada' => ['greenland', 'ontario', 'eastern_united_states'],
            'western_united_states' => ['alberta', 'ontario', 'eastern_united_states', 'central_america'],
            'eastern_united_states' => ['ontario', 'eastern_canada', 'western_united_states', 'central_america'],
            'central_america' => ['western_united_states', 'eastern_united_states', 'venezuela'],
         
            'venezuela' => ['brazil', 'peru', 'central_america'],
            'peru' => ['brazil', 'argentina', 'venezuela'],
            'brazil' => ['argentina', 'peru', 'venezuela', 'north_africa'],
            'argentina' => ['brazil', 'peru'],
            
            'iceland' => ['greenland', 'scandinavia', 'great_britain'],
            'scandinavia' => ['iceland', 'great_britain', 'northern_europe', 'russia'],
            'great_britain' => ['iceland', 'scandinavia', 'northern_europe', 'western_europe'],
            'northern_europe' => ['scandinavia', 'great_britain', 'western_europe', 'southern_europe', 'russia'],
            'western_europe' => ['great_britain', 'northern_europe', 'southern_europe', 'north_africa'],
            'southern_europe' => ['northern_europe', 'western_europe', 'russia', 'north_africa', 'egypt', 'middle_east'],
            'russia' => ['scandinavia', 'northern_europe', 'southern_europe', 'ural', 'afghanistan', 'middle_east'],
            
            'north_africa' => ['brazil', 'western_europe', 'southern_europe', 'egypt', 'east_africa', 'central_africa'],
            'egypt' => ['southern_europe', 'north_africa', 'middle_east', 'east_africa'],
            'east_africa' => ['north_africa', 'egypt', 'middle_east', 'central_africa', 'south_africa', 'madagascar'],
            'central_africa' => ['north_africa', 'east_africa', 'south_africa'],
            'south_africa' => ['central_africa', 'east_africa', 'madagascar'],
            'madagascar' => ['east_africa', 'south_africa'],
            
            'ural' => ['russia', 'siberia', 'afghanistan', 'china'],
            'siberia' => ['ural', 'yakutsk', 'irkutsk', 'china', 'mongolia'],
            'yakutsk' => ['siberia', 'irkutsk', 'kamchatka'],
            'irkutsk' => ['siberia', 'yakutsk', 'kamchatka', 'mongolia'],
            'kamchatka' => ['alaska', 'yakutsk', 'irkutsk', 'mongolia', 'japan'],
            'afghanistan' => ['russia', 'ural', 'china', 'middle_east', 'india'],
            'china' => ['ural', 'siberia', 'afghanistan', 'mongolia', 'india', 'southeast_asia'],
            'mongolia' => ['siberia', 'irkutsk', 'kamchatka', 'china', 'japan'],
            'japan' => ['kamchatka', 'mongolia'],
            'middle_east' => ['southern_europe', 'russia', 'egypt', 'east_africa', 'afghanistan', 'india'],
            'india' => ['afghanistan', 'china', 'middle_east', 'southeast_asia'],
            'southeast_asia' => ['china', 'india', 'indonesia'],
            
            'indonesia' => ['southeast_asia', 'new_guinea', 'western_australia'],
            'new_guinea' => ['indonesia', 'western_australia', 'eastern_australia'],
            'western_australia' => ['indonesia', 'new_guinea', 'eastern_australia'], 
            'eastern_australia' => ['new_guinea', 'western_australia'],
        ];

        $continents = [
            [0, 1, 2, 3, 4, 5, 6, 7, 8],
            [9, 10, 11, 12],
            [13, 14, 15, 16, 17, 18, 19],
            [20, 21, 22, 23, 24, 25],
            [26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37],
            [38, 39, 40, 41]
        ];

        switch ($state->phase)
        {
            case 'occupy':

                $firstMove = true;
                foreach ($state->territories as $territory)
                {
                    if ($territory->player === $player)
                    {
                        $firstMove = false;
                        break;
                    }
                }
                if ($firstMove)
                {
                    foreach ($continents as $continent)
                    {
                        $continentIsEmpty = true;
                        foreach ($continent as $territoryIndex)
                        {
                            if ($state->territories[$territoryIndex]->player !== null)
                            {
                                $continentIsEmpty = false;
                                break;
                            }
                        }
                        if ($continentIsEmpty)
                        {
                            $move = new stdClass();
                            $move->territory = $state->territories[rand($continent[0], $continent[count($continent) - 1])]->name;
                            //plays the move
                            $this->occupy_process($game_id, $move);
                            break;
                        }
                    }
                }
                else
                {
                    $move = null;
                    foreach ($continents as $continent)
                    {
                        $holdsTerritory = false;
                        foreach ($continent as $territoryIndex)
                        {
                            if ($tate->territories[$territoryIndex]->player === $player)
                            {
                                $holdsTerritory = true;
                                break;
                            }
                        }
                        if ($holdsTerritory)
                        {
                            $emptyTerritories = [];
                            foreach ($continent as $territory)
                            {
                                if ($territory->player === null)
                                {
                                    $emptyTerritories[] = $territory;
                                }
                            }
                            if (count($emptyTerritories) > 0)
                            {
                                $move = new stdClass();
                                $move->territory = $emptyTerritories[rand(0, count($emptyTerritories) - 1)]->name;
                                break;
                            }
                        }
                    }

                    if ($move === null)
                    {
                        $emptyTerritories = [];
                        foreach ($state->territories as $territory)
                        {
                            if ($territory->player === null)
                            {
                                $emptyTerritories[] = $territory;
                            }
                        }
                        $move = new stdClass();
                        $move->territory = $emptyTerritories[rand(0, count($emptyTerritories) - 1)]->name;
                    }
                
                    //plays the move
                    $this->occupy_process($game_id, $move);
                }

            break;

            case 'strengthen':

                $continentStrength = [];
                foreach ($contintets as $continent)
                {
                    $strength = 0;
                    foreach ($continent as $territoryIndex)
                    {
                        if ($state->territories[$territoryIndex]->player === $player)
                        {
                            $strength += $state->territories[$territoryIndex]->units;
                        }
                    }
                    $continentStrength[] = $strength;
                }
                $strongestContinentIndex = array_search(max($continentStrength), $continentStrength);

                $territoryVulnerability = [];
                foreach ($contintets[$strongestContinentIndex] as $territoryIndex)
                {
                    if ($state->territories[$territoryIndex]->player === $player)
                    {
                        $enemyStrength = 0;
                        $territoryName = $state->territories[$territoryIndex]->name;
                        foreach ($neighbours[$territoryName] as $neighbour)
                        {
                            foreach ($state->territories as $territory)
                            {
                                if ($territory->name === $neighbour)
                                {
                                    if ($territory->player !== $player)
                                    {
                                        $enemyStrength += $territory->units;
                                    }
                                }
                            }
                        }
                        $territoryVulnerability[] = $enemyStrength / $state->territories[$territoryIndex]->units;
                    }
                    else
                    {
                        $territoryVulnerability[] = 0;
                    }
                }
                $theMostVulnerableTerritoryIndex = array_search(max($territoryVulnerability), $territoryVulnerability);
                $move = new stdClass();
                $move->territory = $state->territories[$theMostVulnerableTerritoryIndex]->name;
                
                //plays the $move
                $this->strengthen_process($game_id, $move);

            break;

            case 'deploy':

                $cards = $state->cards[$player];

                $set = [];
                if (count($cards > 2))
                {
                    $thereIsAWildCard = false;
                    $index = null;
                    foreach ($cards as $i => $card)
                    {
                        if ($card->type === 'wild')
                        {
                            $thereIsAWildCard = true;
                            $index = $i;
                            $set[] = $card;
                            break;
                        }
                    }
                    if ($thereIsAWildCard)
                    {
                        if ($index > 1)
                        {
                            $set = array_merge($set, array_slice(0, 2));
                        }
                        else
                        {
                            $set = array_slice($cards, 0, 3);
                        }
                    }
                    else
                    {
                        $infantryCard = null;
                        $cavalryCard = null;
                        $artilleryCard = null;
                        foreach ($cards as $card)
                        {
                            switch ($card->type)
                            {
                                case 'infantry':
                                    $infantryCard = $card;
                                break;
                                case 'cavalry':
                                    $cavalryCard = $card;
                                break;
                                case 'artillery':
                                    $artilleryCard = $card;
                                break;
                            }
                        }
                        if ($infantryCard !== null && $cavalryCard !== null && $artilleryCard !== $null)
                        {
                            $set = [$infantryCard, $cavalryCard, $artilleryCard];
                        }
                        else
                        {
                            foreach ($cards as $card)
                            {
                                if ($card->type === 'infantry')
                                {
                                    $set[] = $card;
                                }
                            }
                            if (count($set) > 2)
                            {
                                $set = array_slice($set, 0, 3);
                            }
                            else
                            {
                                $set = [];
                                foreach ($cards as $card)
                                {
                                    if ($card->type === 'cavalry')
                                    {
                                        $set[] = $card;
                                    }
                                }
                                if (count($set) > 2)
                                {
                                    $set = array_slice($set, 0, 3);
                                }
                                else
                                {
                                    $set = [];
                                    foreach ($cards as $card)
                                    {
                                        if ($card->type === 'artillery')
                                        {
                                            $set[] = $card;
                                        }
                                    }
                                    if (count($set) > 2)
                                    {
                                        $set = array_slice($set, 0, 3);
                                    }
                                    else
                                    {
                                        $set = [];
                                    }
                                }
                            }
                        }
                    }
                }
                if (count($set) === 3)
                {
                    //plays the move
                    $move = new stdClass();
                    $move->set = $set;
                    $this->play_cards_process($game_id, $move);
                }

                $enemyStrengths = [];
                $playerStrengths = [];
                $territoryVulnerability = [];
                $distribution = [];
                foreach ($state->territories as $territory)
                {
                    if ($territory->player === $player)
                    {
                        $enemyStrength = 0;
                        foreach ($neighbours[$territory->name] as $neighbour)
                        {
                            foreach ($state->territories as $anotherTerritory)
                            {
                                if ($anotherTerritory->name === $neighbour)
                                {
                                    if ($anotherTerritory->player !== $player)
                                    {
                                        $enemyStrength += $territory->units;
                                    }
                                    break;
                                }
                            }
                        }
                        $distribution[$territory->name] = 0;
                        $enemyStrengths[$territory->name] = $enemyStrength;
                        $playerStrengths[$territory->name] = $territory->units;
                        $territoryVulnerability[$territory->name] = $enemyStrength / $territory->units;
                    }
                }
                $availabeUnits = $state->unitsToDistribute;
                while ($availabeUnits > 0)
                {
                    $theMostVulnerableTerritory = array_search(max($territoryVulnerability), $territoryVulnerability);
                    $distribution[$theMostVulnerableTerritory]++;
                    $availabeUnits--;
                    $playerStrengths[$theMostVulnerableTerritory]++;
                    $territoryVulnerability[$theMostVulnerableTerritory] = $enemyStrengths[$theMostVulnerableTerritory] / $playerStrengths[$theMostVulnerableTerritory];
                }
                $move = new stdClass();
                $move->territories = $state->territories;
                foreach ($move->territories as $territory)
                {
                    if (array_key_exists($territory->name, $distribution))
                    {
                        $territory->units += $distribution[$territory->name];
                    }
                }
                //plays the move
                $this->deploy_process($game_id, $move);

            break;
            
            case 'attack':

                $linesOfAttack = [];
                foreach ($state->territories as $territory)
                {
                    if ($territory->player === $player)
                    {
                        foreach ($neighbours[$territory->name] as $neighbour)
                        {
                            if ($neighbour->player !== $player)
                            {
                                $line = new stdClass();
                                $line->attackingTerritory = $territory->name;
                                $line->defendingTerritory = $neighbour->name;
                                $line->ratio = $territory->units / $neighbour->units;
                                $linesOfAttack[] = $line;
                            }
                        }
                    }
                }
                $maxRatio = -1;
                $maxRatioIndex = -1;
                foreach ($linesOfAttack as $i => $line)
                {
                    if ($line->ratio > $maxRatio)
                    {
                        $maxRatio = $line->ratio;
                        $maxRatioIndex = $i;
                    }
                }
                $move = null;
                if(!$state->hasGainedTerritory)
                {
                    if ($maxRatio >= 1)
                    {
                        $move = new stdClass();
                        $move->attackingTerritory = $linesOfAttack[$maxRatioIndex]->attackingTerritory;
                        $move->defendingTerritory = $linesOfAttack[$maxRatioIndex]->defendingTerritory;
                        $move->blitz = 'false';
                    }
                }
                else
                {
                    if ($maxRatio >= 2)
                    {
                        $move = new stdClass();
                        $move->attackingTerritory = $linesOfAttack[$maxRatioIndex]->attackingTerritory;
                        $move->defendingTerritory = $linesOfAttack[$maxRatioIndex]->defendingTerritory;
                        $move->blitz = 'false';
                    }
                }
                if ($move !== null)
                {
                    //play the attack move
                    $this->attack_process($game_id, $move);
                }
                else
                {
                    $enemyStrengths = [];
                    $playerStrengths = [];
                    $territoryVulnerability = [];
                    foreach ($state->territories as $territory)
                    {
                        if ($territory->player === $player)
                        {
                            $enemyStrength = 0;
                            foreach ($neighbours[$territory->name] as $neighbour)
                            {
                                foreach ($state->territories as $anotherTerritory)
                                {
                                    if ($anotherTerritory->name === $neighbour)
                                    {
                                        if ($anotherTerritory->player !== $player)
                                        {
                                            $enemyStrength += $territory->units;
                                        }
                                        break;
                                    }
                                }
                            }
                            $enemyStrengths[$territory->name] = $enemyStrength;
                            $playerStrengths[$territory->name] = $territory->units;
                            $territoryVulnerability[$territory->name] = $enemyStrength / $territory->units;
                        }
                    }
                    
                    $move = null;
                    foreach ($state->territories as $territory)
                    {
                        if ($territory->player === $player && $territory->units > 1)
                        {
                            $hasNoHostileNeighbours = true;
                            foreach ($neighbours[$territory->name] as $neighbour)
                            {
                                foreach ($state->territories as $anotherTerritory)
                                {
                                    if ($anotherTerritory->name === $neighbour)
                                    {
                                        if ($anotherTerritory->player !== $player)
                                        {
                                            $hasNoHostileNeighbours = false;
                                        }
                                        break;
                                    }
                                }
                            }
                            if ($hasNoHostileNeighbours)
                            {
                                foreach ($neighbours[$territory->name] as $neighbour)
                                {
                                    foreach ($state->territories as $anotherTerritory)
                                    {
                                        if ($anotherTerritory->name === $neighbour)
                                        {
                                            if ($anotherTerritory->player === $player)
                                            {
                                                if ($territoryVulnerability[$anotherTerritory->name] > 0)
                                                {
                                                    $move = new stdClass();
                                                    $move->fromTerritory = $territory->name;
                                                    $move->toTerritory = $anotherTerritory->name;
                                                    $move->fromUnits = 1;
                                                    $move->toUnits = $anotherTerritory->units + ($territory->units - 1);
                                                }
                                            }
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($move !== null)
                    {
                        //play the fortify move
                        $this->fortify_process($game_id, $move);
                    }
                    else
                    {
                        $move = new stdClass();
                        //play the empty fortify move
                        $this->fortify_process($game_id, $move);
                    }
                }
            break;
        }

        //gets the most recent gamestate from the database
        $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
        $state = json_decode($gamestate->state);

        //checks if it is a computer's turn and if so, lets it play
        if (array_search($state->turn, $state->computerPlayers))
        {
            $this->computers_turn($game_id);
        }        
    }

    public function test()
    {
        $this->create_initial_manual(25, ['red', 'blue']);
        return $this->get_current_state(25);
    }
}
