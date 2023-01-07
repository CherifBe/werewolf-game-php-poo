<?php

use src\Controllers\AppController;

function loadClasses($class ) {
    // var_dump( $class );
    require_once './'. str_replace('\\', '/', $class) .'.php';
}
spl_autoload_register( 'loadClasses' );


AppController::getApp()->start();