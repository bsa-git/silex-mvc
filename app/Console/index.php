<?php
// app/console/index.php
try {

    // Base path
    if (!defined('BASEPATH')) {
        define('BASEPATH', realpath(__DIR__ . '/../../'));
    }

    // Set the path to library
    set_include_path(implode(PATH_SEPARATOR, array(BASEPATH . '/vendor/library', get_include_path())));


    //Set timezone
    date_default_timezone_set("UTC");

    // Run Bootstrap
    require_once 'Bootstrap.php';

    $bootstrap = new Bootstrap();

    $bootstrap->run();
} catch (\Exception $e) {
    // catch and report any stray exceptions...
    echo $e->getMessage();
}