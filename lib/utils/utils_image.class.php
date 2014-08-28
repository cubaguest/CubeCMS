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


      $sizes = $width . 'x' . $height . ($crop == false ? '' : 'c');
      if(!empty($filter)){
         $sizes .= '-f_'.implode('_', $filter);
      }
      $origPath = str_replace(Url_Request::getBaseWebDir(), '', $path);
      $cachePath = 'cache/imgc/' . Template::face() . '/' . $sizes . '/' . $origPath;
      $hash = sha1($sizes . VVE_DB_PASSWD);
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

}
