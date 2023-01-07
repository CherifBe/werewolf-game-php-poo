<?php

namespace src\Models\Abstract;
abstract class AbstractCharacter
{
    public string $name;
    public ?AbstractCharacter $inRelationShipWith = null;
    public bool $inRelationShip = false;
    public bool $isMayor = false;
    public bool $isAlive = true;
    // protected string $role;

    public function __construct(?string $name = null)
    {
        $class = get_called_class();
        $this->name = (is_null($name) ? $class::NAME : $name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function accuse(int $possibilityOfVotes, int $myPosition): int
    {
        $myVote = rand(0, $possibilityOfVotes);
        if($myVote != $myPosition){ //Car on ne vote jamais pour se tuer soi même
            return $myVote;
        }
        return $this->accuse($possibilityOfVotes, $myPosition);

    }
}
