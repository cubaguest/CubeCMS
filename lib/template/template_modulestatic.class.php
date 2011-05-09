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
      $this->locale = new Locales($moduleName);
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
         parent::addTplFile($name, $this->getModuleName());
      } else {
         parent::addTplFile($name, true);
      }
      return $this;
   }

   /**
    * Metoda přidá javascript soubor do šablony
    * @param string/JsPlugin_JsFile $jsfile -- název souboru nebo objek JsPlugin_JsFile(pro virtuální)
    * @return Template -- objekt sebe
    */
   public function addJsFile($jsfile, $engine = false) {
      if($engine){
         parent::addJsFile('engine:'.$jsfile);
      } else {
         parent::addJsFile($this->getModuleName().':'.$jsfile);
      }
      return $this;
   }

   /**
    * Metoda přidá zadaný css soubor do stylů stránky
    * @param string $cssfile -- css soubor
    * @return Template -- objekt sebe
    */
   public function addCssFile($cssfile, $engine = false) {
      if($engine){
         parent::addCssFile('engine:'.$cssfile);
      } else {
         parent::addCssFile($this->getModuleName().':'.$cssfile);
      }
      return $this;
   }

   /**
    * Metoda přidá soubor do šablony
    * @param <type> $resource
    * @param <type> $directInclude
    */
   public function  addFile($resource, $directInclude = false) {
      // přidání názvu modulu do tpl pokud tam není
      Template::addFile(preg_replace('/^(tpl|css|js):\/\/(?![a-z]+:)/i', '\\1://'.$this->getModuleName().':' , $resource), $directInclude);
   }
}
?>