<?php
/**
 * Třída pro práci s šablonami statického modulu.
 * Třida obsahuje vrstvu mezi šablonovacím systémem a samotným pohledem (viewrem)
 * modulu. Umožňuje všechny základní operace při volbě a plnění šablony a jejímu
 * zobrazení v modulu.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: template_module.class.php 646 2009-08-28 13:44:00Z jakub $ VVE 6.0.0 $Revision: 646 $
 * @author        $Author: jakub $ $Date: 2009-08-28 15:44:00 +0200 (Pá, 28 srp 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-08-28 15:44:00 +0200 (Pá, 28 srp 2009) $
 * @abstract 		Třída pro obsluhu šablony
 */


class Template_ModuleStatic extends Template_Module {
   /**
    * Objekt kategorie pro kterou je šablona tvořena
    * @var Category
    */
   private $moduleName = null;

   /**
    * KOnstruktor vytvoří objekt šablony pro modul
    * @param Url_Link_Module $link -- objekt odkazu
    * @param Category $category -- objekt kategorie
    */
   function  __construct(Url_Link_ModuleRequest $link, $moduleName) {
      Template::__construct($link);
      $this->moduleName = $moduleName;
      $this->locale = new Locale($moduleName);
   }

   /**
    * Metoda vrací název modulu
    * @return string
    */
   public function getModuleName() {
      return $this->moduleName;
   }

   /**
    * Metoda přidá požadovaný soubor šablony do výstupu
    * @param string $name -- název souboru
    * @param boolean $engine -- jestli se jedná o šablonu enginu
    */
   public function addTplFile($name, $engine = false) {
         if(!$engine){
            array_push($this->templateFiles, self::getFileDir($name, self::TEMPLATES_DIR, $this->getModuleName(),true).$name);
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
         $filePath = self::getFileDir($jsfile, Template::JAVASCRIPTS_DIR, $this->getModuleName(), false);
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
         $filePath = self::getFileDir($cssfile, self::STYLESHEETS_DIR, $this->getModuleName(), false);
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
}
?>