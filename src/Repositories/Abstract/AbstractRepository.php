<?php

use src\Service\Database\Database;

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
        $resuls = $this->db->query($sql);
        $items = $resuls->fetchAll();
        return $items;
    }
}