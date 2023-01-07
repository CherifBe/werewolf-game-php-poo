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
    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $results = $this->db->query($sql);
        $items = $results->fetchAll();
        return $items;
    }

    public function createDatabase(): void
    {
        $sql = "CREATE DATABASE players";
        $this->db->query($sql);
    }
}