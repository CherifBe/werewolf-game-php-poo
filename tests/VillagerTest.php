<?php

use PHPUnit\Framework\TestCase;
use src\Models\Villager;

class VillagerTest extends TestCase
{
    public function testVillagerObject()
    {
        function loadClasses($class ) {
            require_once './'. str_replace('\\', '/', $class) .'.php';
        }
        spl_autoload_register( 'loadClasses' );
        $villager = new Villager('toto');
        $this->assertEquals('toto', $villager->getName());

        $this->assertIsInt($villager->accuse(15, 2));
    }
}
