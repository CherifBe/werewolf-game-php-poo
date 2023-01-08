<?php

namespace src\Controllers;
use src\Repositories\PlayerRepository;
use src\Models\Cupidon;
use src\Models\Hunter;
use src\Models\Villager;
use src\Models\Werewolf;
use src\Models\Witch;
use src\Service\Renderer;

final class AppController
{
    private static ?self $instance = null;
    private bool $gameIsOver = false;
    private bool $newKill = false;
    private PlayerRepository $repository;
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
        //$this->repository = new PlayerRepository();
        //$players = $this->repository->findAll(' LIMIT 10'); // On limite le jeu à 10 joueurs
        $nameOfPlayers = ['Julie', 'Maxime', 'Jean', 'Louis', 'Henry', 'William', 'Sarah', 'Emma', 'Ludivine', 'Jeremy', 'Christophe', 'Joseph'];
        $nbOfPlayer = 5; // On pourra éventuellement changer cette valeur
        $tabOfTheDead = [];

        for ($i = 0; $i < $nbOfPlayer; $i++) {
            $this->playersAlive[] = new Villager($nameOfPlayers[rand(0, count($nameOfPlayers)-1)]);
        }

        $html_result = '<div class="container"><div class="list-group"><div class="list-group-item">';
        $html_result .= '<p><strong>Les joueurs sont prêts... le Jeu peut commencer...</strong></p>';
        $html_result .='<p>Le jour se lève</p>';

        $html_result .= '<p>Les villageois votent pour leur maire...</p>';
        $playersVote = $this->vote($this->playersAlive);
        $html_result .= "<p>Les villageois: <strong>{$this->playersAlive[$playersVote]->name}</strong> nous semblent être le meilleur pour occuper ce poste</p>";
        $this->playersAlive[$playersVote]->isMayor = true;

        $html_result .= '<p>Les villageois ont passés une super journée, la nuit tombe...</p>';
        $html_result .= '<p>Une lueur apparait dans le ciel</p>';

        $cupidon = new Cupidon();
        $html_result .= '<p>Cupidon apparait!</p>';
        $html_result .= '<p>Il use de sa flèche...</p>';

        $html_result .= $cupidon->coupleSelection($this->playersAlive);

        $html_result .= '<p>Une personne suspicieuse apparait, elle semble hostile...</p>';
        $witch = new Witch();

        $html_result .= '<p>Le chasseur vient de rentrer au village</p>';
        $hunter = new Hunter();
        $this->playersAlive[] = $hunter;

        $html_result .= '<p>La nuit tombe...</p>';
        $html_result .= '<p>Des cris se font entendre, que se passe t\'il ?</p>';

        $wereWolves = [];
        for ($i = 0; $i < 2; $i++) { // On invoque 2 loups garous, on peut éventuellement changer cette valeur
            $wereWolf = new Werewolf();
            $wereWolves[] = $wereWolf;
            $this->playersAlive[] = $wereWolf;
        }

        $html_result .= '<p>Quelque chose de jamais vu se passe au village...</p>';
        $wereWolvesVote = $this->wereWolvesVote($wereWolves);
        $playerIsDead = $this->playersAlive[$wereWolvesVote];

        $tabOfTheDead += $wereWolves[0]->sendToTheCemetery($playerIsDead, $this->playersAlive);
        $this->newKill = true;
        $html_result .= '</div>';
        // A faire une fois que tous les autres joueurs sont prêts
        while(!$this->gameIsOver){
            $html_result .= '<div class="list-group-item">';
            $html_result .= '<h2>Un nouveau jour se lève</h2>';

            if($this->newKill){
                $this->newKill = false;
                $html_result .= '<p>La nuit dernière était terrible!</p>';

                $html_result .= '<p>La sorcière s\'approche du corps...</p>';
                $witchLuck = rand(0, 100);
                if($witchLuck> 90){
                    $playerIsAliveAgain = $witch->revive($tabOfTheDead);
                    $html_result .= $playerIsAliveAgain->name . ' est ressuscité !!';
                    unset($tabOfTheDead[array_search($playerIsAliveAgain, $tabOfTheDead, true)]);
                    $tabOfTheDead = array_merge($tabOfTheDead);
                    $this->playersAlive[] = $playerIsAliveAgain;
                } else {
                    $html_result .= 'La sorcière tente un sort, en vain...';
                }

                $html_result .= '<p>Les villageois cherchent le coupable... ils votent!</p>';
                $villagerVotedFor = $this->vote($this->playersAlive);
                $playerAccused = $this->playersAlive[$villagerVotedFor];
                $html_result .= '<p>On va tuer '. $playerAccused->name . ' ce vaurien!</p>';

                $tabOfTheDead += $witch->sendToTheCemetery($playerAccused, $this->playersAlive);

                if(in_array($playerAccused, $wereWolves)){
                    unset($wereWolves[array_search($playerAccused, $wereWolves, true)]);
                    $wereWolves = array_merge($wereWolves);
                }

            } else {
                $html_result .= '<p>Il ne s\'est rien passé la nuit dernière, les villageois pensent avoir trouvé le bon coupable</p>';
                $html_result .= '<p>Les villageois passent une journée ordinaire</p>';
            }

            if(count($wereWolves) <= 0){
                $html_result .= '<h2>Les villageois ont gagnés!!!!</h2>';
                $this->gameIsOver = true;
            } else {
                $html_result .= '<h2>La nuit tombe</h2>';
                $wereWolfHungry = rand(1,2);
                if($wereWolfHungry > 1){
                    $wereWolvesVote = $this->wereWolvesVote($wereWolves);
                    $playerIsDead = $this->playersAlive[$wereWolvesVote];
                    $html_result .= "<p>{$playerIsDead->name} n'aura pas de chance ce soir...</p>";
                    $tabOfTheDead += $wereWolves[0]->sendToTheCemetery($playerIsDead, $this->playersAlive);
                    $this->newKill = true;
                }
            }

            if(count($this->playersAlive) <= 2){
                $gameIsOver = $this->pleaseVerify();
                if($gameIsOver){
                    $html_result .= '<h2>Les loups garous ont gagnés!!!!</h2>';
                    $this->gameIsOver = true;
                }
            }
            $html_result .= '</div>';
        }
        $html_result .= '</div></div>';

        Renderer::render(compact('html_result'));

    }
}
