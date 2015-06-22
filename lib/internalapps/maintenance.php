<?php
/**
 * Soubor pro zapnutí/vypnutí/přiadání IP v odstávkovém módu
 * @author Jakub Matas
 * @copyright 2014 Cube-Studio
 *
 * Example activate path: http://www.cube-cms.com/?internalApp=maintenance&action=activate
 * Example deactivate path: http://www.cube-cms.com/?internalApp=maintenance&action=deactivate
 * Example add IP path: http://www.cube-cms.com/?internalApp=maintenance&action=add
 */


if(!isset($_GET['action']) || !isset($_GET['key'])){
   echo 'Unsupported args';
   die;
}
if(!defined("MAINTENANCE_KEY") || MAINTENANCE_KEY != $_GET['key']){
   echo 'App error. Config need update!';
   die;
}
define("FILE_MAINTENANCE", 'maintenance.lock');
define("FILE_IP", 'maintenance.ip');

define("DIR", realpath(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
$action = $_GET['action'];
if($action == 'activate'){
   if(is_file(DIR.FILE_MAINTENANCE)){
      unlink(DIR.FILE_MAINTENANCE);
   }
   echo 'Systém byl aktivován';
} else if($action == 'deactivate'){
   file_put_contents(DIR.FILE_MAINTENANCE, isset($_GET['date']) ? $_GET['date'] : null);
   echo 'Systém byl deaktivován. Povoleny jsou pouze určené IP adresy.';
} else if($action == 'add'){
   $ips = array($_SERVER['REMOTE_ADDR']);
   if(is_file(DIR.FILE_MAINTENANCE)){
      $ips = array_merge($ips, file(DIR.FILE_IP, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES));
   }
   
   $ips = array_unique($ips);
   $fp = fopen(DIR.FILE_IP, 'w');
   foreach($ips as $ip){
      fwrite($fp, $ip.PHP_EOL);
      echo 'IP '.$ip.' přidána<br />';
   }
   
} else {
   echo 'Unsuportet arg';
}