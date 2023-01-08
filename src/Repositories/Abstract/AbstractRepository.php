<?php

namespace src\Repositories\Abstract;

use src\Service\Database;

abstract class AbstractRepository
{
    protected $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getPDO();
    }
    public function findAll(?string $instruction = ""): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if($instruction){
            $sql .= $instruction;
        }
        $results = $this->db->query($sql);
        $items = $results->fetchAll();
        return $items;
    }
}