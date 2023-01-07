<?php

namespace src\Models;
use src\Models\Abstract\AbstractCharacter;

final class Werewolf extends AbstractCharacter implements KillerInterface, StayAwakeInterface
{
    function killSomeone(Character $player): void
    {
        $player->isAlive = false;
    }

    function accuse()
    {
        // TODO: Implement accuse() method.
    }

    function isDead()
    {
        // TODO: Implement isDead() method.
    }
}