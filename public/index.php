<?php

use Mamook\Utility\Utility;

ob_start(); # Begin output buffering

try {
    # Define the location of this page.
    define('HERE_PATH', 'index.php');
    /*
    ** In settings we
    ** define application settings
    ** define system settings
    ** start a new session
    ** connect to the Database
    */
    require_once '../settings.php';

    # Get the Controller.
    require_once Utility::locateFile(CONTROLLERS . 'index.php');
} catch (Exception $e) {
    //$exception=new Exception($e->getCode(),$e->getMessage(),$e->getFile(),$e->getLine(),$e->getTrace());
    throw new $e;
}

ob_flush(); # Send the buffer to the user's browser.
