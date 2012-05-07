<?php
/*
 * BASE SETUP
 */
if(!defined('VVE_APP_IS_RUN')){
   define('VVE_APP_IS_RUN', true);
}
$maintenance = false;
$maintenanceTitle = null;
$maintenanceMessage = null;
$maintenanceDeadTime = '01:00 12.1.2011';
$maintenanceContact = 'jakubmatas@gmail.com';
$maintenanceAllowAccess = array('127.0.0.2');
$libDir = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR;
$webDir = realpath(dirname(isset ($siteFile) ? $siteFile : __FILE__)).DIRECTORY_SEPARATOR;
$allowedInternalApps = array('imagecacher');
// include site config
include $libDir.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';
/*
 * Některé specifické součásti systému, např resizer
 */
if(isset($_GET['internalApp'])){
   $appFile = $libDir.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'internalapps'.DIRECTORY_SEPARATOR.$_GET['internalApp'].'.php';
   if(is_file($appFile) && in_array($_GET['internalApp'], $allowedInternalApps)){
      include $appFile;
   } else {
      echo 'Unsuported APP: '.$_GET['internalApp'];
   }
   // after internal app complete, exit
   exit();
}
/**
 * Vložení hlavní třídy aplikace
 */
if(!file_exists('data/lock.tmp')){
   if($maintenance == false OR in_array($_SERVER['REMOTE_ADDR'], $maintenanceAllowAccess)){
      require_once ( $libDir.'app.php' );
      AppCore::setAppMainLibDir($libDir);
      AppCore::setAppMainDir($webDir);
      $app = AppCore::createApp();
      $app->runCore();
   } else {
      include 'templates/update.phtml';
   }
} else {
   include 'templates/update.phtml';
}
?>