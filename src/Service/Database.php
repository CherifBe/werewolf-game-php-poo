<?php

namespace src\Service\Database;
use PDO;

class Database
{
    private static $instance = null;
    private const PDO_OPTIONS = [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    /**
     * On utilise un singleton pour éviter d'instancier plusieurs pour un même utilisateur
     * @return PDO
     */
    public static function getPDO(): PDO
    {
        if(self::$instance === null){
            self::$instance = new PDO('mysql:host=database;dbname=lamp', 'lamp', 'lamp', self::PDO_OPTIONS);;
        }
        return self::$instance;
    }
}
