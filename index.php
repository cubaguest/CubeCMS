<?php
/*
 * BASE SETUP
 */
if(!defined('CUBE_CMS_APP_IS_RUN')){
   define('CUBE_CMS_APP_IS_RUN', true);
   define('VVE_APP_IS_RUN', true); // compatibility
}
if(!defined('CUBECMS_LIB_DIR')){
   define('CUBE_CMS_LIB_DIR', 'lib');
}
// základní složky
define('CUBE_CMS_WEB_DIR', dirname($_SERVER['SCRIPT_FILENAME']).DIRECTORY_SEPARATOR);
define('CUBE_CMS_BASE_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);

// include site config
if(CUBE_CMS_WEB_DIR != CUBE_CMS_BASE_DIR && is_file(CUBE_CMS_WEB_DIR.'config'.DIRECTORY_SEPARATOR.'config.php')){
   include_once CUBE_CMS_WEB_DIR.'config'.DIRECTORY_SEPARATOR.'config.php';
}
// include base config
include_once CUBE_CMS_BASE_DIR.'config'.DIRECTORY_SEPARATOR.'config.php';
$allowedInternalApps = array('imagecacher', 'maintenance', 'proxyjs');

// maintenance mode
$maintenance = is_file(CUBE_CMS_WEB_DIR.'data'.DIRECTORY_SEPARATOR.'maintenance.lock');
if($maintenance){
   define('MAINTENANCE_DATE', file_get_contents(CUBE_CMS_WEB_DIR.'data'.DIRECTORY_SEPARATOR.'maintenance.lock') );
   // try load ip file
   $ips = file(CUBE_CMS_WEB_DIR.'data'.DIRECTORY_SEPARATOR.'maintenance.lock', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
   if(in_array($_SERVER['REMOTE_ADDR'], $ips)){
      $maintenance = false;
   }
}
/*
 * Některé specifické součásti systému, např resizer, odstávka a podobně
 */
if(isset($_GET['internalApp'])){
   $appFile = CUBE_CMS_BASE_DIR.DIRECTORY_SEPARATOR.CUBE_CMS_LIB_DIR.DIRECTORY_SEPARATOR.'internalapps'.DIRECTORY_SEPARATOR.$_GET['internalApp'].'.php';
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
   require_once ( CUBE_CMS_BASE_DIR.'app.php' );
   $app = AppCore::createApp();
   $app->runCore();
} else {
   include 'templates/update.phtml';
}