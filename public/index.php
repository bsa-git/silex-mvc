<?php

// public/index.php
try {
    // Base path
    if (!defined('BASEPATH')) {
        define('BASEPATH', realpath(__DIR__ . '/../'));
    }

    // Set the path to library
    set_include_path(implode(PATH_SEPARATOR, array(BASEPATH . '/library', get_include_path())));

    //Set timezone
    date_default_timezone_set("UTC");


    require_once BASEPATH . '/app/Bootstrap.php';

    $bootstrap = new Bootstrap();

    $bootstrap->run();
} catch (\Exception $exc) {
    // catch and report any stray exceptions...
    echo $exc->getMessage();
}