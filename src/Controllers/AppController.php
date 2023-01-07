<?php

namespace src\Controllers;
use src\Models\Cupidon;
use src\Models\Hunter;
use src\Models\Villager;
use src\Models\Werewolf;
use src\Models\Witch;


// TODO: TOUT METTRE DANS UNE VARIABLE HTML ET LANCER LE RENDER A LA FIN

final class AppController
{
    private static ?self $instance = null;
    private bool $gameIsOver = false;
    private bool $newKill = false;
    private array $playersAlive;
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
            $voteOfThePlayer = $player->accuse((count($tab_of_accused)-1), array_search($player, $tab_of_players, true));
            $votes[] = $voteOfThePlayer;
            if($player->isMayor){ // Le Maire peut voter deux fois (et bien évidemment il vote deux fois la même valeur)
                $votes[] = $voteOfThePlayer;
            }
        }
        $res = (array_count_values($votes));
        return (array_search(max($res), $res, true)); // ici on récupère le chiffre qui apparait le plus c'est à dire celui pour qui on a le plus voté
    }

    public function wereWolvesVote(array $wereWolves): int
    {
        $wereWolvesVote = $this->vote($wereWolves, $this->playersAlive);
        if($this->playersAlive[$wereWolvesVote]->name == 'Werewolf'){
            return $this->wereWolvesVote($wereWolves);
        }
        return $wereWolvesVote;
    }

    public function pleaseVerify(): bool
    {
        foreach($this->playersAlive as $player){
            if($player->name == 'Werewolf'){
                return true;
            }
        }
        return false;
    }

    public function start(): void
    {
        $nameOfPlayers = ['Julie', 'Maxime', 'Jean', 'Louis', 'Henry', 'William', 'Sarah', 'Emma', 'Ludivine', 'Jeremy', 'Christophe', 'Joseph'];
        $nbOfPlayer = 10; // On pourra éventuellement changer cette valeur
        $tabOfTheDead = [];

        for ($i = 0; $i < $nbOfPlayer; $i++) {
            $this->playersAlive[] = new Villager($nameOfPlayers[rand(0, count($nameOfPlayers)-1)]);
        }
        echo '<p>Les joueurs sont prêts... le Jeu peut commencer...</p>';
        echo '<p>Le jour se lève</p>';

        echo '<p>Les villageois votent pour leur maire...</p>';
        $playersVote = $this->vote($this->playersAlive);
        echo "<p>Les villageois: <strong>{$this->playersAlive[$playersVote]->name}</strong> nous semblent être le meilleur pour occuper ce poste</p>";
        $this->playersAlive[$playersVote]->isMayor = true;

        echo '<p>Les villageois ont passés une super journée, la nuit tombe...</p>';
        echo '<p>Une lueur apparait dans le ciel</p>';

        $cupidon = new Cupidon();
        echo '<p>Cupidon apparait!</p>';
        echo '<p>Il use de sa flèche...</p>';

        $cupidon->coupleSelection($this->playersAlive);

        echo '<p>Une personne suspicieuse apparait, elle semble hostile...</p>';
        $witch = new Witch();

        echo '<p>Le chasseur vient de rentrer au village</p>';
        $hunter = new Hunter();
        $this->playersAlive[] = $hunter;

        echo '<p>La nuit tombe...</p>';
        echo '<p>Des cris se font entendre, que se passe t\'il ?</p>';

        $wereWolves = [];
        for ($i = 0; $i < 2; $i++) { // On invoque 2 loups garous, on peut éventuellement changer cette valeur
            $wereWolf = new Werewolf();
            $wereWolves[] = $wereWolf;
            $this->playersAlive[] = $wereWolf;
        }

        echo '<p>Quelque chose de jamais vu se passe au village...</p>';
        $wereWolvesVote = $this->wereWolvesVote($wereWolves);
        $playerIsDead = $this->playersAlive[$wereWolvesVote];

        $tabOfTheDead += $wereWolves[0]->sendToTheCemetery($playerIsDead, $this->playersAlive);
        $this->newKill = true;

        // A faire une fois que tous les autres joueurs sont prêts
        while(!$this->gameIsOver){

            if(count($this->playersAlive) <= 2){
                $gameIsOver = $this->pleaseVerify();
                if($gameIsOver){
                    echo '<h2>Les loups garous ont gagnés!!!!</h2>';
                    $this->gameIsOver = true;
                    return;
                }
            }

            echo '<h2>Un nouveau jour se lève</h2>';

            if($this->newKill){
                $this->newKill = false;
                echo '<p>La nuit dernière était terrible!</p>';

                echo '<p>La sorcière s\'approche du corps...</p>';
                $witchLuck = rand(0, 100);
                if($witchLuck> 90){
                    $playerIsAliveAgain = $witch->revive($tabOfTheDead);
                    unset($tabOfTheDead[array_search($playerIsAliveAgain, $tabOfTheDead, true)]);
                    $tabOfTheDead = array_merge($tabOfTheDead);
                    $this->playersAlive[] = $playerIsAliveAgain;
                } else {
                    echo 'La sorcière tente un sort, en vain...';
                }

                echo '<p>Les villageois cherchent le coupable... ils votent!</p>';
                $villagerVotedFor = $this->vote($this->playersAlive);
                $playerAccused = $this->playersAlive[$villagerVotedFor];
                echo '<p>On va tuer '. $playerAccused->name . ' ce vaurien!</p>';

                $tabOfTheDead += $witch->sendToTheCemetery($playerAccused, $this->playersAlive);

                if(in_array($playerAccused, $wereWolves)){
                    unset($wereWolves[array_search($playerAccused, $wereWolves, true)]);
                    $wereWolves = array_merge($wereWolves);
                }

            } else {
                echo '<p>Il ne s\'est rien passé la nuit dernière, les villageois pensent avoir trouvé le bon coupable</p>';
                echo '<p>Les villageois passent une journée ordinaire</p>';
            }

            if(count($wereWolves) <= 0){
                echo '<h2>Les villageois ont gagnés!!!!</h2>';
                $this->gameIsOver = true;
                return;
            } else {
                echo '<h2>La nuit tombe</h2>';
                $wereWolfHungry = rand(1,2);
                if($wereWolfHungry > 1){
                    $wereWolvesVote = $this->wereWolvesVote($wereWolves);
                    $playerIsDead = $this->playersAlive[$wereWolvesVote];
                    echo "<p>{$playerIsDead->name} n'aura pas de chance ce soir...</p>";
                    $tabOfTheDead += $wereWolves[0]->sendToTheCemetery($playerIsDead, $this->playersAlive);
                    $this->newKill = true;
                }
            }
        }
    }
}
