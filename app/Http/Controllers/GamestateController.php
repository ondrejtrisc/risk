<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gamestate;
use stdClass;

class GamestateController extends Controller
{
    public function create_initial($game_id, $players)
    {
        $gamestate = new Gamestate();
        $gamestate->game_id = $game_id;
        $gamestate->step = 1;

        $state = new stdClass();
        $state->players = $players;
        $state->territories = [];
        
        $territoryNames = ['alaska', 'northwest_territory', 'greenland', 'alberta', 'ontario', 'quebec', 'western_united_states', 'eastern_united_states', 'central_america', 'venezuela', 'peru', 'brazil', 'argentina', 'iceland', 'scandinavia', 'great_britain', 'northern_europe', 'western_europe', 'southern_europe', 'russia', 'north_africa', 'egypt', 'east_africa', 'central_africa', 'south_africa', 'madagascar', 'ural', 'siberia', 'yakursk', 'irkutsk', 'kamchatka', 'afghanistan', 'china', 'mongolia', 'japan', 'middle_east', 'india', 'southeast_asia', 'indonesia', 'new_guinea', 'western_australia', 'eastern_australia'];
        
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
        $unitsToDistribute = [];
        foreach ($state->players as $player)
        {
            $occupiedBy[$player] = [];
            $unitsToDistribute[$player] = $initial_units;
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
            $unitsToDistribute[$player]--;

            if ($playerIndex === count($state->players) - 1)
            {
                $playerIndex = 0;
            }
            else
            {
                $playerIndex++;
            }
        }

        while (true)
        {
            $player = $state->players[$playerIndex];
            $territory = $occupiedBy[$player][rand(0, count($occupiedBy[$player]) - 1)];
            $territory->units++;
            $unitsToDistribute[$player]--;

            if ($playerIndex === count($state->players) - 1)
            {
                $playerIndex = 0;
            }
            else
            {
                $playerIndex++;
            }

            if($unitsToDistribute[$players[$playerIndex]] === 0)
            {
                break;
            }
        }

        //distribution of remaining troops
        $limit = $initial_units - floor((count($state->territories) / count($state->players)));
        for ($i = 0; $i < $limit; $i++)
        {
            foreach($state->players as $player)
            {                  
                $territory = $occupiedBy[$player][rand(0, count($occupiedBy[$player]) - 1)];
                $territory->units += 1;
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

        $gamestate->state = json_encode($state);

        $gamestate->save();
    }

    /*a method for testing purposes - to create a new game make a GET request at /initialize/{game_id} with a fresh game_id*/
    public function initialize($game_id)
    {
        $this->create_initial($game_id, [1, 2]);
        return $this->get_current_state($game_id);
    }

    public function get_current_state($game_id)
    {
        try
        {
            $gamestate = Gamestate::where('game_id', $game_id)->orderBy('step', 'desc')->first();
            return $gamestate->state;
        }
        catch (Exception $err)
        {
            return json_encode(new stdClass());
        }
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

        // //deploys the units
        // foreach ($state->territories as $territory)
        // {
        //     if ($territory->player === $state->players[$state->turn])
        //     {
        //         $name = $territory->name;
        //         $territory->units += $object->$name;
        //     }
        // }

        //changes the phase to attack
        $state->phase = 'attack';
        $state->unitsToDeploy = null;

        //creates the new gamestate
        $newGamestate = new Gamestate();
        $newGamestate->game_id = $game_id;
        $newGamestate->step = $gamestate->step + 1;
        $newGamestate->state = json_encode($state);

        //saves the new gamestate to the database
        $newGamestate->save();

        //returns the new state to the frontend
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
                }
                else
                {
                    $fromTerritory->units -= 1;
                }
            }

            if ($toTerritory->units === 0)
            {
                $toTerritory->player = $fromTerritory->player;
                $toTerritory->units = $fromTerritory->units - 1;
                $fromTerritory->units = 1;
                break;
            }
        }
        while ($blitz == 'true' && $fromTerritory->units > 1);

        //creates the new gamestate
        $newGamestate = new Gamestate();
        $newGamestate->game_id = $game_id;
        $newGamestate->step = $gamestate->step + 1;
        $newGamestate->state = json_encode($state);

        //saves the new gamestate to the database
        $newGamestate->save();

        //returns the new state to the frontend
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
        if ($fromName != '')
        {
            $toName = $object->toTerritory;
            $units = $object->units;        

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
            $fromTerritory->units -= $units;
            $toTerritory->units += $units;
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

        //creates the new gamestate
        $newGamestate = new Gamestate();
        $newGamestate->game_id = $game_id;
        $newGamestate->step = $gamestate->step + 1;
        $newGamestate->state = json_encode($state);

        //saves the new gamestate to the database
        $newGamestate->save();

        //returns the new state to the frontend
        return json_encode($state);
    }
}
