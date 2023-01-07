<?php

namespace src\Models;
use src\Models\Abstract\AbstractCharacter;

final class Cupidon extends AbstractCharacter
{
    protected const NAME = 'Cupidon';
    public function couple(AbstractCharacter $playerOne, AbstractCharacter $playerTwo): void
    {
        $playerOne->inRelationShip = true;
        $playerTwo->inRelationShip = true;

        $playerOne->inRelationShipWith = $playerTwo;
        $playerTwo->inRelationShipWith = $playerOne;
        echo "<p>Cupidon : {$playerOne->name} et {$playerTwo->name} sont en couple!</p>";
    }

}
