<?php
/**
 * Třída pro práci s šablonou.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu šablony
 */


class Template_File_Phtml extends Template_File {
/**
 * Objekt pro lokalizaci v modulu
 * @var Locale
 */
   protected $locale = null;

   /**
    * KOnstruktor vytvoří objekt šablony pro modul
    * @param Url_Link_Module $link -- objekt odkazu
    * @param Category $category -- objekt kategorie
    */
   function  __construct() {
   }

   /**
    * Metoda vrací objekt lokalizace
    * @return Locale
    */
   final public function locale() {
   }

   /**
    * Metoda přeloží zadaný řetězec
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    */
   final public function _($message) {
   }

   /**
    * Metoda přeloží zadaný řetězec alias k metodě _()
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    * @deprecated -- lepší je použití $this->_() pro podobnost s funkcí gettextu
    */
   final public function _m($message) {
   }

   /**
    * Metoda vrací název adresáře s požadovaným souborem (bez souboru)
    * @param string $file -- název souboru
    * @param string $type -- typ adresáře - konstanta třídy
    * @param boolean $engine -- jestli se jedná o objekt enginu nebo modulu
    * @return string -- adresář bez souboru
    */
   public static function getFileDir($file, $dir = self::TEMPLATES_DIR, $moduleName = null, $realpath = true) {
      $faceDir = AppCore::getAppWebDir().self::FACES_DIR.DIRECTORY_SEPARATOR.self::$face.DIRECTORY_SEPARATOR
          .AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR
          .$dir.DIRECTORY_SEPARATOR;
      $mainDir = AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR
          .$dir.DIRECTORY_SEPARATOR;
      // pokud existuje soubor ve vzhledu
      if(file_exists($faceDir.$file)) {
          if($realpath){
             $faceDir;
          } else {
             return Url_Request::getBaseWebDir().self::FACES_DIR.URL_SEPARATOR.self::$face.URL_SEPARATOR.AppCore::MODULES_DIR.URL_SEPARATOR.$moduleName.URL_SEPARATOR;
          }
         
      } else if(file_exists($mainDir.$file)) {
         if($realpath){
            return $mainDir;
         } else {
            return Url_Request::getBaseWebDir().AppCore::MODULES_DIR.URL_SEPARATOR.$moduleName.URL_SEPARATOR
          .$dir.URL_SEPARATOR;
            return null;
         }
      } else {
         trigger_error(sprintf(_('Soubor "%s" s šablonou v modulu "%s" nebyl nalezen'),
               $file, $moduleName));
      }
   }
}
?>