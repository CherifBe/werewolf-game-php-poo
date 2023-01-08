<?php


use PHPUnit\Framework\TestCase;
use src\Models\Witch;
use src\Models\Villager;

class WitchTest extends TestCase
{
    public function testMurder()
    {
        function loadAnotherClasses($class ) {
            require_once './'. str_replace('\\', '/', $class) .'.php';
        }
        spl_autoload_register( 'loadAnotherClasses' );
        $witch = new Witch();
        $villager = new Villager('toto');
        $villager2 = new Villager('tata');
        $villager3 = new Villager('titi');
        $players = [];
        $players[] = $villager;
        $players[] = $villager2;
        $players[] = $villager3;

        $this->assertIsArray($witch->sendToTheCemetery($villager, $players));
    }
}
