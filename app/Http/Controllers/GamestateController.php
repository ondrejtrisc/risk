<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Game;
use App\Gamestate;
use stdClass;

class GamestateController extends Controller
{
    public function create_initial($game_id, $players)
    {
        $game = Game::findOrFail($game_id);
        if($game->init_deployment === 'random')
        {
            $this->create_initial_random($game_id, $players);
        }
        else
        {
            $this->create_initial_manual($game_id, $players);
        }
    }

    public function create_initial_random($game_id, $players)
    {
        $gamestate = new Gamestate();
        $gamestate->game_id = $game_id;
        $gamestate->step = 1;

        $state = new stdClass();
        $state->players = $players;
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

        //calculates how many units the first player can deploy
        $playerHeldTerritories = 0;
        foreach ($state->territories as $territory)
        {
            if ($territory->player === $state->players[$state->turn])
            {
                $playerHeldTerritories++;
            }
        }
        $state->unitsToDeploy = max(3, floor($playerHeldTerritories / 3));
        
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
        }

        $gamestate->state = json_encode($state);

        $gamestate->save();
    }

    public function create_initial_manual($game_id, $players)
    {
        $gamestate = new Gamestate();
        $gamestate->game_id = $game_id;
        $gamestate->step = 1;

        $state = new stdClass();
        $state->players = $players;
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

        $gamestate->state = json_encode($state);

        $gamestate->save();
    }

    public function occupy($game_id)
    {
        //gets the most recent gamestate from the database
        $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
        $state = json_decode($gamestate->state);
        
        //gets the data about the move from the request
        $requestPayload = file_get_contents("php://input");
        $object = json_decode($requestPayload);

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

        //returns the new state to the frontend
        unset($state->deck);
        return json_encode($state);
    }

    public function strengthen($game_id)
    {
        //gets the most recent gamestate from the database
        $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
        $state = json_decode($gamestate->state);
        
        //gets the data about the move from the request
        $requestPayload = file_get_contents("php://input");
        $object = json_decode($requestPayload);

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
            $state->unitsToDeploy = max(3, floor($playerHeldTerritories / 3));

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
            }
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
        return json_encode($state);
    }

    public function get_current_state($game_id)
    {
        try
        {
            $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
            $state = json_decode($gamestate->state);
            unset($state->deck);
            return json_encode($state);
        }
        catch (Exception $err)
        {
            return json_encode(new stdClass());
        }
    }

    public function play_cards($game_id)
    {
        //gets the most recent gamestate from the database
        $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
        $state = json_decode($gamestate->state);
        
        //gets the data about the move from the request
        $requestPayload = file_get_contents("php://input");
        $object = json_decode($requestPayload);
        $set = $object->set;

        //adds extra units to deploy
        $state->matches++;
        if ($state->matches < 6)
        {
            $state->unitsToDeploy += $state->matches * 2 + 2;
        }
        else
        {
            $state->unitsToDeploy += $state->matches * 5 - 15;
        }

        $territoryBonus = false;
        foreach ($set as $card)
        {
            foreach ($state->territories as $territory)
            {
                if ($territory->name === $card->territory)
                {
                    if ($territory->player === $state->players[$state->turn])
                    {
                        $territoryBonus = true;
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
        return json_encode($state);
    }

    public function deploy($game_id)
    {
        //gets the most recent gamestate from the database
        $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
        $state = json_decode($gamestate->state);
        
        //gets the data about the move from the request
        $requestPayload = file_get_contents("php://input");
        $object = json_decode($requestPayload);

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
        return json_encode($state);
    }

    public function attack($game_id)
    {
        //gets the most recent gamestate from the database
        $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
        $state = json_decode($gamestate->state);
        
        //gets the data about the move from the request
        $requestPayload = file_get_contents("php://input");
        $object = json_decode($requestPayload);
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
            $state->defenderDice = [$attackerLost, $defenderLost];
        }

        if ($defenderEliminated)
        {
            $player = $state->players[$state->turn];
            $cards->$player = array_merge($cards->$player, $cards->$defender);
            unset($cards->$defender);

            $defenderIndex = array_search($defender, $state->players);
            array_splice($state->players, $defenderIndex, 1);

            $state->phase = 'deploy';
            $state->unitsToDeploy = 0;
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
        return json_encode($state);
    }

    public function fortify($game_id)
    {
        //gets the most recent gamestate from the database
        $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
        $state = json_decode($gamestate->state);
        
        //gets the data about the move from the request
        $requestPayload = file_get_contents("php://input");
        $object = json_decode($requestPayload);

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

        //calculates how many units the next player can deploy
        $playerHeldTerritories = 0;
        foreach ($state->territories as $territory)
        {
            if ($territory->player === $state->players[$state->turn])
            {
                $playerHeldTerritories++;
            }
        }
        $state->unitsToDeploy = max(3, floor($playerHeldTerritories / 3));

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
        return json_encode($state);
    }

    public function test()
    {
        $this->create_initial_manual(24, ['red', 'blue']);
        return $this->get_current_state(24);
    }
}
