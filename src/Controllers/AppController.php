<?php

namespace src\Controllers;
use src\Models\Cupidon;
use src\Models\Villager;

final class AppController
{
    private static ?self $instance = null;
    private bool $gameIsOver = false;
    public static function getApp(): self
    {
        if( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function vote(array $tab_of_players, ?array $tab_of_accused = null): int
    {
        if(is_null($tab_of_accused)){
            $tab_of_accused = $tab_of_players;
        }
        $votes = [];
        foreach($tab_of_players as $player){
            $votes[] = $player->accuse(count($tab_of_accused)-1);
        }
        $res = (array_count_values($votes));
        return (array_search(max($res), $res)); // ici on récupère le chiffre qui apparait le plus c'est à dire celui pour qui on a le plus voté
    }

    public function start(): void
    {
        $nameOfPlayers = ['Julie', 'Maxime', 'Jean', 'Louis', 'Henry', 'William', 'Sarah', 'Emma', 'Ludivine', 'Jeremy', 'Christophe', 'Joseph'];

        $nbOfPlayer = 10; // On pourra éventuellement changer cette valeur

        $playersAlive = [];
        for ($i = 0; $i < $nbOfPlayer; $i++) {
            $playersAlive[] = new Villager($nameOfPlayers[rand(0, count($nameOfPlayers)-1)]);
        }
        echo '<p>Les joueurs sont prêts... le Jeu peut commencer...</p>';
        echo '<p>Le jour se lève</p>';

        echo '<p>Les villageois votent pour leur maire...</p>';
        $playersVote = $this->vote($playersAlive);
        echo "<p>Les villageois: <strong>{$playersAlive[$playersVote]->name}</strong> nous semblent être le meilleur pour occuper ce poste</p>";
        $playersAlive[$playersVote]->isMayor = true;

        echo '<p>Les villageois ont passés une super journée, la nuit tombe...</p>';
        echo '<p>Une lueur apparait dans le ciel</p>';

        $cupidon = new Cupidon();
        echo '<p>Cupidon apparait!</p>';
        echo '<p>Il use de sa flèche...</p>';
        //TODO: Faire fonction pour sélection deux personnages
        function coupleSelection(Cupidon $cupidon, array $playersAlive)
        {
            $playerOne = rand(0, count($playersAlive)-1);
            $playerTwo = rand(0, count($playersAlive)-1);
            if($playerOne != $playerTwo){
                $cupidon->couple($playersAlive[$playerOne], $playersAlive[$playerTwo]);
                return;
            }
            return coupleSelection($cupidon, $playersAlive); // Si jamais on obtient le même random, on reprocède à la sélection
        }
        coupleSelection($cupidon, $playersAlive);

        // TODO: Créer sorcière et loup garou avant de lancer la boucle

        // A faire une fois que tous les autres joueurs sont prêts
        while(!$this->gameIsOver){
            echo '<p>Un nouveau jour se lève</p>';
            $this->gameIsOver = true;
        }

    }


}
