<?php
/**
 * Soubor pro kešování velikostí obrázků
 * @author Jakub Matas
 * @copyright 2012 Cube-Studio
 *
 * Example cached path: http://www.cube-cms.com/cache/imgc/default/200x150/data/title-images/Mister-Monster.jpg
 */
/* Potřebují se předdefinovat základní třídy aplikace */


function sendError($msg = null){
   header("HTTP/1.0 404 Not Found");
   echo "document.write(\"".$msg."\");";
   die;
}


if(!isset($_GET['path'])){
   sendError('Nejsou předány všechny parametry');
}
if(isset($_GET['debug'])){
   echo "LIB dir:".$libDir."<br />";
   echo "WEB dir:".$webDir."<br />";
   print_r($_GET);
}

$file = str_replace(array('/', '../'), array(DIRECTORY_SEPARATOR, ''), $_GET['path']);
$realpath = $libDir.$file;

header('Content-Type: application/javascript');
readfile($realpath);
//$handle = fopen($realpath, 'rb');
//$buffer = '';
//while (!feof($handle)) {
//   $buffer = fread($handle, 4096);
//   echo $buffer;
//   ob_flush();
//   flush();
//}
//fclose($handle);
