<?php
/*
 * BASE SETUP
 */
if(!defined('VVE_APP_IS_RUN')){
   define('VVE_APP_IS_RUN', true);
}
//if(is_link(__FILE__)){
//   var_dump(readlink(__FILE__));
//} else {
//$libDir = dirname(__FILE__).DIRECTORY_SEPARATOR;
//}
if(isset($siteFile)){
   $libDir = str_replace(basename(dirname($siteFile)), "", dirname($_SERVER['SCRIPT_FILENAME']));
   $webDir = realpath(dirname($siteFile)).DIRECTORY_SEPARATOR;
//   var_dump($libDir, $webDir);die;
} else {
   $libDir = dirname($_SERVER['SCRIPT_FILENAME']).DIRECTORY_SEPARATOR;
   $webDir = getcwd().DIRECTORY_SEPARATOR;
}

// include site config
include $libDir.'config'.DIRECTORY_SEPARATOR.'config.php';
$allowedInternalApps = array('imagecacher', 'maintenance', 'proxyjs');

// maintenance mode
$maintenance = is_file($webDir.'data'.DIRECTORY_SEPARATOR.'maintenance.lock');
if($maintenance){
   define('MAINTENANCE_DATE', file_get_contents($webDir.'data'.DIRECTORY_SEPARATOR.'maintenance.lock') );
   // try load ip file
   $ips = file($webDir.'data'.DIRECTORY_SEPARATOR.'maintenance.lock', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
//   $ips[]='127.0.0.1';
   if(in_array($_SERVER['REMOTE_ADDR'], $ips)){
      $maintenance = false;
   }
}
/*
 * Některé specifické součásti systému, např resizer, odstávka a podobně
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
if(!$maintenance){
   require_once ( $libDir.'app.php' );
   AppCore::setAppMainLibDir($libDir);
   AppCore::setAppMainDir($webDir);
   $app = AppCore::createApp();
   $app->runCore();
} else {
   include 'templates/update.phtml';
}
?>