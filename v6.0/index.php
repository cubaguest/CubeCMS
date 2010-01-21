<?php
/*Hlaseni chyb na obrazavku pro jednoduchou diagnostiku ve vyvojovem prostredi...*/
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Prague');

/**
 * Vložení hlavní třídy aplikace
 */
require_once ('./app.php');
AppCore::setAppMainLibDir(realpath(dirname(__FILE__)));
AppCore::setAppMainDir(realpath(dirname(__FILE__)));
AppCore::createApp();

?>