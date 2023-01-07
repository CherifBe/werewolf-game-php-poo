<?php

namespace src\Models;
use src\Models\Abstract\AbstractCharacter;
use src\Models\Interface\KillerInterface;

final class Witch extends AbstractCharacter implements KillerInterface
{
    protected const NAME = 'Witch';

    public function revive(array $tabOfTheDead): AbstractCharacter
    {
        $playerIsAliveAgain = end($tabOfTheDead);
        $playerIsAliveAgain->isAlive = true;
        echo $playerIsAliveAgain->name . ' est ressuscité !!';
        return $playerIsAliveAgain;
    }

    public function killSomeone(AbstractCharacter $player): void
    {
        if($player->inRelationShip){
            $player->inRelationShipWith->isAlive = false;
        }
        $player->isAlive = false;
    }

    public function sendToTheCemetery(AbstractCharacter $player, array $players): array
    {
        $this->killSomeone($player);
        unset($players[array_search($player, $players, true)]);
        $players = array_merge($players);
        $tabOfTheDead[] = $player;
        if($player->inRelationShip){
            echo "<p>Et il ne mourra pas seul... ". strtoupper($player->inRelationShipWith->name) ." L'ACCOMPAGNERA</p>";
            $player->inRelationShip = false;
            $tabOfTheDead[] = $player->inRelationShipWith;

            unset($players[array_search($player->inRelationShipWith, $players, true)]);
            $players = array_merge($players);

            $player->inRelationShipWith->inRelationShip = false;
            $player->inRelationShipWith->inRelationShipWith = null;
            $player->inRelationShipWith = null;
        }
        return $tabOfTheDead;
    }
}
