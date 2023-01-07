<?php

namespace src\Models;
use src\Models\Abstract\AbstractCharacter;

final class Cupidon extends AbstractCharacter
{
    protected const NAME = 'Cupidon';
    public function couple(AbstractCharacter $playerOne, AbstractCharacter $playerTwo): string
    {
        $playerOne->inRelationShip = true;
        $playerTwo->inRelationShip = true;

        $playerOne->inRelationShipWith = $playerTwo;
        $playerTwo->inRelationShipWith = $playerOne;
        return "<p>Cupidon : {$playerOne->name} et {$playerTwo->name} sont en couple!</p>";
    }

    public function coupleSelection(array $playersAlive)
    {
        $playerOne = rand(0, count($playersAlive)-1);
        $playerTwo = rand(0, count($playersAlive)-1);
        if($playerOne != $playerTwo){
            return $this->couple($playersAlive[$playerOne], $playersAlive[$playerTwo]);
        }
        return $this->coupleSelection($playersAlive); // Si jamais on obtient le même random, on reprocède à la sélection
    }

}
