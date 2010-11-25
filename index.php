<?php
/*Hlaseni chyb na obrazavku pro jednoduchou diagnostiku ve vyvojovem prostredi...*/
/**
 * Vložení hlavní třídy aplikace
 */
if(!file_exists('data/lock.tmp')){
   require_once ('./app.php');
   AppCore::setAppMainLibDir(realpath(dirname(__FILE__)));
   AppCore::setAppMainDir(realpath(dirname(__FILE__)));
   AppCore::createApp();
} else {
   include 'templates/update.phtml';
}
?>