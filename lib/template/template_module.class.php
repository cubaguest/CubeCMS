<?php
/**
 * Třída pro práci s šablonami modulu.
 * Třida obsahuje vrstvu mezi šablonovacím systémem a samotným pohledem (viewrem)
 * modulu. Umožňuje všechny základní operace při volbě a plnění šablony a jejímu
 * zobrazení v modulu.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: template_module.class.php 646 2009-08-28 13:44:00Z jakub $ VVE3.9.4 $Revision: 646 $
 * @author        $Author: jakub $ $Date: 2009-08-28 15:44:00 +0200 (Pá, 28 srp 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-08-28 15:44:00 +0200 (Pá, 28 srp 2009) $
 * @abstract 		Třída pro obsluhu šablony
 */


class Template_Module extends Template {
/**
 * Objekt pro lokalizaci v modulu
 * @var Locale
 */
   protected $locale = null;

   /**
    * Objekt kategorie pro kterou je šablona tvořena
    * @var Category
    */
   private $category = null;

   /**
    * KOnstruktor vytvoří objekt šablony pro modul
    * @param Url_Link_Module $link -- objekt odkazu
    * @param Category $category -- objekt kategorie
    */
   function  __construct(Url_Link_Module $link, Category $category) {
      parent::__construct($link);
      $this->category = $category;
      $this->locale = new Locale($category->getModule()->getName());
   }

   /**
    * Metoda vrací objekt lokalizace
    * @return Locale
    */
   final public function locale() {
      return $this->locale;
   }

   /**
    * Metoda přeloží zadaný řetězec
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    */
   final public function _($message) {
      return $this->locale()->_m($message);
   }

   /**
    * Metoda přeloží zadaný řetězec alias k metodě _()
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    * @deprecated -- lepší je použití $this->_() pro podobnost s funkcí gettextu
    */
   final public function _m($message) {
      return $this->_($message);
   }

   /**
    * Metoda vrací objekt kategorie
    * @return Category
    */
   public function category() {
      return $this->category;
   }

   /**
    * Metoda přidá požadovaný soubor šablony do výstupu
    * @param string $name -- název souboru
    * @param boolean $engine -- jestli se jedná o šablonu enginu
    */
   public function addTplFile($name, $engine = false) {
         if(!$engine){
            array_push($this->templateFiles, self::getFileDir($name, self::TEMPLATES_DIR, $this->category->getModule()->getName()).$name);
         } else {
            array_push($this->templateFiles, parent::getFileDir($name, self::TEMPLATES_DIR, false).$name);
         }
   }

   /**
    * Metoda přidá javascript soubor do šablony
    * @param string/JsPlugin_JsFile $jsfile -- název souboru nebo objek JsPlugin_JsFile(pro virtuální)
    * @return Template -- objekt sebe
    */
   public function addJsFile($jsfile, $engine = false) {
      //konttrola jestli se nejedná o URL adresu (vzdálený soubor)
      if(eregi('http://[a-zA-Z_.]+', $jsfile)){
         self::addJS($jsfile);
      } else if(!$engine) {
         $filePath = self::getFileDir($jsfile, Template::JAVASCRIPTS_DIR, $this->category->getModule()->getName(), false);
         if($filePath != null){
            self::addJS($filePath.$jsfile);
         }
      } else {
         $filePath = parent::getFileDir($jsfile, Template::JAVASCRIPTS_DIR, false);
         if($filePath != null){
            self::addJS($filePath.$jsfile);
         }
      }
      return $this;
   }

   /**
    * Metoda přidá zadaný css soubor do stylů stránky
    * @param string $cssfile -- css soubor
    * @return Template -- objekt sebe
    */
   public function addCssFile($cssfile, $engine = false) {
      if(eregi('http://[a-zA-Z_.]+', $cssfile)){
         self::addCss($jsfile);
      } else if(!$engine) {
         $filePath = self::getFileDir($cssfile, self::STYLESHEETS_DIR, $this->category->getModule()->getName(), false);
         if($filePath != null){
            self::addCss($filePath.$cssfile);
         }
      } else {
         $filePath = parent::getFileDir($cssfile, Template::STYLESHEETS_DIR, false);
         if($filePath!=null){
            self::addCss($filePath.$cssfile);
         }
      }
      return $this;
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