<?php

namespace src\Models\Interface;

use src\Models\Abstract\AbstractCharacter;

interface KillerInterface{
    public function killSomeone( AbstractCharacter $player ): void;
    public function sendToTheCemetery( AbstractCharacter $player, array &$players ): array;
}
