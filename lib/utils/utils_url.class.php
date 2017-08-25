<?php

/**
 * Třída pro usnadnění práce s odkazy
 *
 * @copyright  	Copyright (c) 2008-2014 Jakub Matas
 * @version    	$Id: $ CubeCMS 8.0.4 $Revision:  $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 */
class Utils_Url {

   /**
    * Funkce odstrani nepovolené znaky a diakritiku pro vložení jako klíč do db
    * @param string/array $string -- řetězec nebo pole pro převedení
    * @return string/array -- řetězec nebo pole s převedenými znaky
    */
   public static function toUrlKey($string, $removeSlashes = true, $lang = false)
   {
      if (is_null($string)) {
         return $string;
      } else if (is_array($string)) {
         foreach ($string as $key => $variable) {
            $string[$key] = self::toUrlKey($variable, $removeSlashes, $lang);
         }
      } else {
         $string = strip_tags($string);
         $string = Utils_String::toAscii($string, $lang);
         $slashes = null;
         if ($removeSlashes) {
            $slashes = '\/';
         }
         $regexp = array('/[^a-z0-9\/ _-]+/i', '/[ ' . $slashes . '-]+/', '/[\/]+/');
         $replacements = array('', '-', URL_SEPARATOR);
         $string = strtolower(preg_replace($regexp, $replacements, $string));
      }
      return $string;
   }

   /**
    * Funkce přepíše všechny relativní adresy v řetězce a absolutní
    * @param string &$string -- samotný řetězec
    * @param string $atrName -- (option) název atribut, který se má opravovat (default: src a href)
    * @return string -- řetězec s opravenou adresou
    */
   public static function createFullUrlPaths($string, $atrNames = 'src|href')
   {
      $string = preg_replace('/(src|href)="(?!http)([^"]+)"/i', '\\1="' . Url_Link::getMainWebDir() . '\\2"', $string);
      return $string;
   }

   public static function exist($url)
   {
      $headers = @get_headers($url);
      return (bool) preg_match('/^HTTP\/\d\.\d\s+(200|301|302)/', $headers[0]);
   }
   
   /**
    * Převede systémovou cestu na url adresu
    * @param string $path
    * @return string
    */
   public static function pathToSystemUrl($path)
   {
      return str_replace(array(
         AppCore::getAppWebDir(), 
         DIRECTORY_SEPARATOR, 
      ), array(
         Url_Link::getWebURL(),
         '/'
      ), $path);
   }
      
   /**
    * Převede systémovou url na cestu
    * @param string $url
    * @return string
    */
   public static function urlToSystemPath($url)
   {
      return str_replace(array(
         Url_Link::getWebURL(),
         '/'
      ), array(
         AppCore::getAppWebDir(), 
         DIRECTORY_SEPARATOR, 
      ), $url);
   }
}
