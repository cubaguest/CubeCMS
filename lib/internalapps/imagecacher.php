<?php
/**
 * Soubor pro keaov�n� velikost� obr�zko
 * @author Jakub Matas
 * @copyright 2012 Cube-Studio
 *
 * Example cached path: http://www.cube-cms.com/cache/imgc/default/200x150/data/title-images/Mister-Monster.jpg
 */
/* PotYebuj� se pYeddefinovat z�kladn� tY�dy aplikace */
class File_Image_Exception extends Exception {}
class CoreException extends Exception {}
class Translator {
   public function tr($str){ return $str; }
   public function translator(){}
   public function setTranslator(){}
}

function sendError($msg = null){
   header("HTTP/1.0 404 Not Found");
   echo "<h1>".$msg."</h1>";
   die;
}


if(!isset($_GET['s']) || !isset($_GET['tf']) || !isset($_GET['is'])){
   sendError('Nejsou předány všechny parametry');
}
if(isset($_GET['debug'])){
   echo "LIB dir:".$libDir."<br />";
   echo "WEB dir:".$webDir."<br />";
   print_r($_GET);
}
define('VVE_IMAGE_COMPRESS_QUALITY', 97);

// Base init urlencode atd here
$SOURCE = $strSource = str_replace(" ", "+", $_GET['s']); // tohle chce opravit pYed�v�n� + v url znamen� pYi pYevodu mezeru
if(strpos($SOURCE, '.php') !== false 
    || strpos($SOURCE, '.phtml') !== false
    || strpos($SOURCE, '.htaccess') !== false
    ){
   header('HTTP/1.0 403 Forbidden');
   echo "Denied"; 
   die;
}

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
$expectedHash = sha1($strSize.VVE_DB_PASSWD);
// parse sizes
$m = array();
if(preg_match('/^([0-9]+)?x([0-9]+)?(c?)(?:-f_([0-9]+)?(?:_([0-9]+)?)?(?:_([0-9]+)?)?(?:_([0-9]+)?)?(?:_([0-9]+)?)?)?$/', $sizesTMP, $m) == false){
   sendError('Vylikost nebyla zadána');
}
$SIZES['w'] = (int)$m[1];
$SIZES['h'] = (int)$m[2];
$SIZES['c'] = $m[3] == "c" ? true : false;

// 4 - typ filtru
// první parametr filtru
// druhý parametr filtru
$filterParams = array();
if(isset($m[4])){
   $filterParams = array_slice($m, 4);
}

if( ( $HASH != null && $expectedHash != $HASH )
    || ( $HASH == null && ( ($SIZES['w'] != null && !in_array($SIZES['w'], $allowSizes['w']) )
         || ($SIZES['h'] != null && !in_array($SIZES['h'], $allowSizes['h']) )
         || ( $SIZES['c'] == true && ( $SIZES['w'] == null || $SIZES['h'] == null ) )
       ) )
   ){
      sendError('Nekorektní rozmry');
}

// check file


// load file lib and create obj
include_once $libDir.CUBECMS_LIB_DIR.DIRECTORY_SEPARATOR.'defines.php';
include_once $libDir.CUBECMS_LIB_DIR.DIRECTORY_SEPARATOR.'trobject.class.php';
include_once $libDir.CUBECMS_LIB_DIR.DIRECTORY_SEPARATOR.'file'.DIRECTORY_SEPARATOR.'file_interface.class.php';
include_once $libDir.CUBECMS_LIB_DIR.DIRECTORY_SEPARATOR.'file'.DIRECTORY_SEPARATOR.'file.class.php';
include_once $libDir.CUBECMS_LIB_DIR.DIRECTORY_SEPARATOR.'file'.DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR.'file_image.class.php';
include_once $libDir.CUBECMS_LIB_DIR.DIRECTORY_SEPARATOR.'file'.DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR.'file_image_base.class.php';
include_once $libDir.CUBECMS_LIB_DIR.DIRECTORY_SEPARATOR.'file'.DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR.'file_image_gd.class.php';
include_once $libDir.CUBECMS_LIB_DIR.DIRECTORY_SEPARATOR.'fs'.DIRECTORY_SEPARATOR.'fs_dir.class.php';
define('VVE_USE_IMAGEMAGICK', false);
$image = null;
try {
   $image = new File_Image($webDir.$SOURCE);
   if(!$image->exist()){
      $image = new File_Image($libDir.$SOURCE);
   }
   if(!$image->isImage()){
      header('HTTP/1.0 403 Forbidden');
      echo "Denied"; 
      die;
   }

   $resizeType = File_Image_Base::RESIZE_AUTO;
   if($SIZES['c'] == true){
      $resizeType = File_Image_Base::RESIZE_CROP;
   } else if ($SIZES['h'] == null) {
      $resizeType = File_Image_Base::RESIZE_LANDSCAPE;
   } else if ($SIZES['w'] == null) {
      $resizeType = File_Image_Base::RESIZE_PORTRAIT;
   }

   // create thum in cache
   $image->getData()->resize($SIZES['w'], $SIZES['h'], $resizeType , true);

   if(!empty($filterParams)){
      $image->getData()->filter($filterParams);
   }
   
   $dir = new FS_Dir(dirname($CACHED_FILE));
   $dir->check();

   $image->getData()->write($CACHED_FILE);
   $image->send();

// redirect to new image
//header('Content-Type: text/html; charset=utf-8');
   header('Location: '.$cache_file_url.'?new');
} catch (Exception $e) {
   // send original image? or resize in memory and send
   if(is_object($image) && $image->exist()){
      $image->send();
   }
}

