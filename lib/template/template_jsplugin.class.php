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


class Template_JsPlugin extends Template {
   /**
    * Název samotný plugin
    * @var JsPlugin
    */
   private $jsPlugin = null;

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
   function  __construct(Url_Link $link, Category $category, JsPlugin $jsPlugin) {
      parent::__construct($link);
      $this->category = $category;
      $this->jsPlugin = $jsPlugin;
   }

   /**
    * Metoda vrací objekt kategorie
    * @return Category
    */
   public function category() {
      return $this->category;
   }

   /**
    * Metoda vrací objekt JsPlugin
    * @return JsPlugin
    */
   public function jsPlugin() {
      return $this->jsPlugin;
   }



   /**
    * Metoda přidá požadovaný soubor šablony do výstupu
    * @param string $name -- název souboru
    * @param boolean $engine -- jestli se jedná o šablonu enginu
    */
   public function addTplFile($name) {
       array_push($this->templateFiles, self::getFileDir($name, $this->jsPlugin()->getName(),true).$name);
   }

   /**
    * Metoda přidá javascript soubor do šablony
    * @param string/JsPlugin_JsFile $jsfile -- název souboru nebo objek JsPlugin_JsFile(pro virtuální)
    * @return Template -- objekt sebe
    */
   public function addJsFile($jsfile, $engine = false) {
      //konttrola jestli se nejedná o URL adresu (vzdálený soubor)
      if(strncmp ($jsfile, 'http', 4) == 0){
         self::addJS($jsfile);
      } else if(!$engine) {
         $filePath = self::getFileDir($jsfile, $this->jsPlugin()->getName(), false);
         if($filePath != null){
            self::addJS($filePath.$jsfile);
         }
      } else {
         $filePath = parent::getFileDir($jsfile, $this->jsPlugin()->getName(),true);
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
      if(strncmp ($cssfile, 'http', 4) == 0){
         self::addCss($cssfile);
      } else if(!$engine) {
         $filePath = self::getFileDir($cssfile, $this->jsPlugin()->getName(), false);
         if($filePath != null){
            self::addCss($filePath.$cssfile);
         }
      } else {
         $filePath = self::getFileDir($cssfile, $this->jsPlugin()->getName(),true);
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
   public static function getFileDir($file, $pluginName = null, $realpath = false, $withFile = false) {
      $pluginName = strtolower($pluginName);
      $faceDir = AppCore::getAppWebDir().self::FACES_DIR.DIRECTORY_SEPARATOR.self::$face.DIRECTORY_SEPARATOR
          .AppCore::ENGINE_TEMPLATE_DIR.DIRECTORY_SEPARATOR.JsPlugin::VIRTUAL_DIR_PREFIX
          .DIRECTORY_SEPARATOR.$pluginName.DIRECTORY_SEPARATOR;
      $mainDir = AppCore::getAppLibDir().JsPlugin::JSPLUGINS_BASE_DIR.DIRECTORY_SEPARATOR.$pluginName.DIRECTORY_SEPARATOR;
      $ret = null;
      // pokud existuje soubor ve vzhledu
      if(is_file($faceDir.$file)) {
          if($realpath){
             $faceDir;
          } else {
             $ret = Url_Request::getBaseWebDir().self::FACES_DIR.URL_SEPARATOR.self::$face.URL_SEPARATOR
          .AppCore::ENGINE_TEMPLATE_DIR.URL_SEPARATOR.JsPlugin::VIRTUAL_DIR_PREFIX.URL_SEPARATOR.$pluginName.URL_SEPARATOR;
          }

      } else if(is_file($mainDir.$file)) {
         if($realpath){
            $ret = $mainDir;
         } else {
            $ret = Url_Request::getBaseWebDir().JsPlugin::JSPLUGINS_BASE_DIR.URL_SEPARATOR.$pluginName.URL_SEPARATOR;
            $ret = null;
         }
      } else {
         trigger_error(sprintf($this->tr('Soubor "%s" s šablonou v JsPluginu "%s" nebyl nalezen'),
               $file, $pluginName));
      }
      if($withFile == true){
         $ret .= $file;
      }
      return $ret;
   }
}
?>