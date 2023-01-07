<?php

namespace src\Repositories;

use src\Repositories\Abstract\AbstractRepository;

final class PlayerRepository extends AbstractRepository
{
    protected string $table = 'players';
}
