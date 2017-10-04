<?php

/**
 * Třída pro usnadnění práce s poli
 *
 * @copyright  	Copyright (c) 2008-2014 Jakub Matas
 * @version    	$Id: $ CubeCMS 8.0.4 $Revision:  $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 */
class Utils_Image {
   
   const IMG_RATION_16BY9 = '16x9';
   const IMG_RATION_16BY10 = '16x10';
   const IMG_RATION_4BY3 = '4x3';
   const IMG_RATION_3BY4 = '4x3';
   
   public static function cache($path, $width = null, $height = null, $crop = false, $filter = array())
   {
      if (strpos($path, AppCore::getAppWebDir()) !== false) { // absolutní cesta
         $path = str_replace(array(
             AppCore::getAppWebDir(), DIRECTORY_SEPARATOR,
             ), array(
             Url_Request::getBaseWebDir(), "/",
             ), $path);
      }

      // check if http
      if (substr($path, 0, 4) != "http") {
         $path = Url_Request::getBaseWebDir() . $path;
      }

      // explode by parts
      $parts = explode('?', $path);
      $getParasms = null;
      $path = $parts[0];
      if (isset($parts[1])) {
         $getParasms = $parts[1];
      }
      
      if(is_string($height)){
         $parts = explode('x', $height);
         if(isset($parts[0]) && isset($parts[1])){
            $height = round($width/(int)$parts[0]*(int)$parts[1]);
         } else {
            $height = (int)$height;
         }
      }


      $sizes = $width . 'x' . $height . ($crop == false ? '' : 'c');
      if(!empty($filter)){
         $sizes .= '-f_'.implode('_', $filter);
      }
      $origPath = str_replace(Url_Request::getBaseWebDir(), '', $path);
      $cachePath = 'cache/imgc/' . Template::face() . '/' . $sizes . '/' . $origPath;
      $hash = sha1($sizes . (defined('CUBE_CMS_DB_PASSWD') ? CUBE_CMS_DB_PASSWD : VVE_DB_PASSWD));
      // pokud má obrázek url adresu ze současného webu
      if (strpos($path, Url_Request::getBaseWebDir()) !== false) {
         $path = str_replace(Url_Request::getBaseWebDir(), Url_Request::getBaseWebDir() . 'cache/imgc/' . Template::face() . '/' . $sizes . '/', $path);
         $path .= "?hash=" . urlencode($hash);
      }
      // pokud je obrázek přímo z data
      else if (strpos($path, VVE_DATA_DIR . "/") === 0) {
         $path = Url_Request::getBaseWebDir() . 'cache/imgc/' . Template::face() . '/' . $sizes . '/' . $path
             . "?hash=" . urlencode($hash);
      }
      // create abs paths
      $realOrigPath = realpath($origPath);
      $realCachedPath = realpath($cachePath);
      // check change time if cached is oldest then original remove
      if (is_file($realCachedPath) && filemtime($realCachedPath) < filemtime($realOrigPath)) {
         @unlink($realCachedPath);
         $path .= '&t=' .(string)microtime(true);
      }
      return $path . ($getParasms != null ? '&' . $getParasms : null);
   }

   /**
    * 
    * @param string|File $image
    * @return boolean
    */
    public static function isLandscape($image)
   {
      if((string)strpos($image, 'http') === 0){
         $image = str_replace(array(Url_Link::getWebURL(), '/'), array(AppCore::getAppWebDir(), DIRECTORY_SEPARATOR), (string)$image);
      }
      $size = getimagesize((string)$image); 
      $aspect = $size[1] / $size[0]; 
      if ($aspect >= 1) {
         return false;
      }
      return true;
   }
   
   /**
    * 
    * @param string|File $image
    * @return boolean
    */
   public static function getRatio($image)
   {
      if(strpos((string)$image, 'http') === 0){
         $image = str_replace(array(Url_Link::getWebURL(), '/'), array(AppCore::getAppWebDir(), DIRECTORY_SEPARATOR), (string)$image);
      }
      $size = getimagesize((string)$image); 
      return $size[0] / $size[1]; 
   }
   
   /**
    * Vrací velikost obrázku. Stejně jako getimagesize()
    * @param string|File $image
    * @return array|bool
    */
   public static function getSize($image)
   {
      if(strpos((string)$image, 'http') === 0){
         $image = str_replace(array(Url_Link::getWebURL(), '/'), array(AppCore::getAppWebDir(), DIRECTORY_SEPARATOR), (string)$image);
      }
      if(is_file((string)$image)){
         return getimagesize((string)$image); 
      }
      return false; 
   }
}
