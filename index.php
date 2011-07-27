<?php
/*Hlaseni chyb na obrazavku pro jednoduchou diagnostiku ve vyvojovem prostredi...*/
/**
 * Vložení hlavní třídy aplikace
 */
$maintenance = false;
$maintenanceTitle = null;
$maintenanceMessage = null;
$maintenanceDeadTime = '01:00 12.1.2011';
$maintenanceContact = 'jakubmatas@gmail.com';
$maintenanceAllowAccess = array('127.0.0.2');
if(!file_exists('data/lock.tmp')){
   if($maintenance == false OR in_array($_SERVER['REMOTE_ADDR'], $maintenanceAllowAccess)){
      require_once ('./app.php');
      AppCore::setAppMainLibDir(realpath(dirname(__FILE__)));
      AppCore::setAppMainDir(realpath(dirname(__FILE__)));
      $app = AppCore::createApp();
      $app->runCore();
   } else {
      include 'templates/update.phtml';
   }
} else {
   include 'templates/update.phtml';
}
?>