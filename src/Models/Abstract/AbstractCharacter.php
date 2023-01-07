<?php

namespace src\Models\Abstract;
abstract class AbstractCharacter
{
    public string $name;
    protected ?AbstractCharacter $inRelationShipWith = null;
    protected bool $inRelationShip = false;
    public bool $isMayor = false;
    protected bool $isAlive = true;
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


    public function accuse(int $possibilityOfVotes)
    {
        return rand(0, $possibilityOfVotes);
    }
    protected function isDead()
    {

    }
}
