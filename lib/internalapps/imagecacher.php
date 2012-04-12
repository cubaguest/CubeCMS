<?php
/**
 * Soubor pro kešování velikostí obrázků 
 * @author Jakub Matas
 * @copyright 2012 Cube-Studio
 * 
 * Example cached path: http://www.cube-cms.com/cache/imgc/default/200x150/data/title-images/Mister-Monster.jpg
 */
header('Content-Type: text/html; charset=utf-8'); 

if(!isset($_GET['s']) || !isset($_GET['tf']) || !isset($_GET['is'])){
   sendError('Nejsou předány všechny parametry');
}
echo "LIB dir:".$libDir."<br />";
echo "WEB dir:".$webDir."<br />";
var_dump($_GET);


// Base init urlencode atd here
$SOURCE = $strSource = $_GET['s'];
$FACE = $strFace = $_GET['tf'];
$sizesTMP = $strSize = $_GET['is'];
$HASH = isset($_GET['hash']) ? urldecode($_GET['hash']) : null;
$SIZES = array('w' => null, 'h' => null, 'c' => false);
$CACHED_FILE = $webDir.'cache'.DIRECTORY_SEPARATOR."imgc".DIRECTORY_SEPARATOR.$FACE.DIRECTORY_SEPARATOR.$strSize.DIRECTORY_SEPARATOR.$SOURCE;
$cache_file_url = "http://". $_SERVER['SERVER_NAME']."/cache/imgc/$strFace/$strSize/$strSource";

// check face and load face allowed sizes
$allowSizes = array(/* base ratio 4:3 */
   'w' => array(100, 124, 200, 300, 400),
   'h' => array(100, 75, 150, 225),
);
$expectedHash = crypt($strSize, VVE_DB_PASSWD);
echo "Expected hash: ".$expectedHash." urlhash: ".$HASH;
var_dump($HASH =! null, $expectedHash != $HASH);
// parse sizes
$m = array();
if(preg_match('/^([0-9]+)?x([0-9]+)?(c?)$/', $sizesTMP, $m) === false){
   sendError('Vylikost nebyla zadána');
}
$SIZES['w'] = (int)$m[1];
$SIZES['h'] = (int)$m[2];
$SIZES['c'] = $m[3] == "c" ? true : false;
if( ( $HASH != null && $expectedHash != $HASH ) 
    || ( $HASH == null && ( ($SIZES['w'] != null && !in_array($SIZES['w'], $allowSizes['w']) )
         || ($SIZES['h'] != null && !in_array($SIZES['h'], $allowSizes['h']) )  
         || ( $SIZES['c'] == true && ( $SIZES['w'] == null || $SIZES['h'] == null ) ) 
       ) )
   ){
      sendError('Nekorektní rozměry');
}

// check file


// load file lib and create obj
include_once $libDir.'lib'.DIRECTORY_SEPARATOR.'trobject.class.php';
include_once $libDir.'lib'.DIRECTORY_SEPARATOR.'file'.DIRECTORY_SEPARATOR.'file_interface.class.php';
include_once $libDir.'lib'.DIRECTORY_SEPARATOR.'file'.DIRECTORY_SEPARATOR.'file.class.php';
include_once $libDir.'lib'.DIRECTORY_SEPARATOR.'file'.DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR.'file_image.class.php';
include_once $libDir.'lib'.DIRECTORY_SEPARATOR.'file'.DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR.'file_image_base.class.php';
include_once $libDir.'lib'.DIRECTORY_SEPARATOR.'file'.DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR.'file_image_gd.class.php';
include_once $libDir.'lib'.DIRECTORY_SEPARATOR.'fs'.DIRECTORY_SEPARATOR.'fs_dir.class.php';
define('VVE_USE_IMAGEMAGICK', false);
$image = new File_Image($webDir.$SOURCE);

$resizeType = File_Image_Base::RESIZE_AUTO;
if($SIZES['c'] == true){
   $resizeType = File_Image_Base::RESIZE_CROP;
} else if ($SIZES['h'] == null) {
//   $SIZES['h'] = 10000;
   $resizeType = File_Image_Base::RESIZE_LANDSCAPE;
} else if ($SIZES['w'] == null) {
   $resizeType = File_Image_Base::RESIZE_PORTRAIT;
//   $SIZES['w'] = 10000;
}

$dir = new FS_Dir(dirname($CACHED_FILE));
$dir->check();

// create thum in cache
$image->getData()->resize($SIZES['w'], $SIZES['h'], $resizeType )
   ->write($CACHED_FILE);


// redirect to new image
header('Location: '.$cache_file_url.'?new');


function sendError($msg = null){
   header("HTTP/1.0 404 Not Found");
   echo "<h1>".$msg."</h1>";
   die;
}

?>
