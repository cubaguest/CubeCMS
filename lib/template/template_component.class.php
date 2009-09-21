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


class Template_Component extends Template {
   /**
    * KOnstruktor vytvoří objekt šablony pro modul
    * @param Url_Link_Module $link -- objekt odkazu
    * @param Category $category -- objekt kategorie
    */
   function  __construct(Url_Link_Component $link) {
      parent::__construct($link);
   }

   /**
    * Metoda vrací objekt kategorie
    * @return Category
    */
   public function category() {
      return Category::getMainCategory();
   }

   /**
    * Metoda přidá požadovaný soubor šablony do výstupu
    * @param string $name -- název souboru
    * @param boolean $engine -- jestli se jedná o šablonu enginu
    */
//   public function addTplFile($name, $engine = false) {
//         if(!$engine){
//            array_push($this->templateFiles, self::getFileDir($name, self::TEMPLATES_DIR, $this->category->getModule()->getName()).$name);
//         } else {
//            array_push($this->templateFiles, parent::getFileDir($name, self::TEMPLATES_DIR, false).$name);
//         }
//   }

   /**
    * Metoda přidá javascript soubor do šablony
    * @param string/JsPlugin_JsFile $jsfile -- název souboru nebo objek JsPlugin_JsFile(pro virtuální)
    * @return Template -- objekt sebe
    */
//   public function addJsFile($jsfile, $engine = false) {
//      //konttrola jestli se nejedná o URL adresu (vzdálený soubor)
//      if(eregi('http://[a-zA-Z_.]+', $jsfile)){
//         self::addJS($jsfile);
//      } else if(!$engine) {
//         self::addJS(self::getFileDir($jsfile, Template::JAVASCRIPTS_DIR, $this->category->getModule()->getName()).$jsfile);
//      } else {
//         self::addJS(parent::getFileDir($jsfile, Template::JAVASCRIPTS_DIR).$jsfile);
//      }
//      return $this;
//   }

   /**
    * Metoda přidá zadaný css soubor do stylů stránky
    * @param string $cssfile -- css soubor
    * @return Template -- objekt sebe
    */
//   public function addCssFile($cssfile, $engine = false) {
//      if(eregi('http://[a-zA-Z_.]+', $cssfile)){
//         self::addCss($jsfile);
//      } else if(!$engine) {
//         self::addCss(self::getFileDir($cssfile, self::STYLESHEETS_DIR, $this->category->getModule()->getName()).$cssfile);
//      } else {
//         self::addCss(parent::getFileDir($cssfile, Template::STYLESHEETS_DIR).$cssfile);
//      }
//      return $this;
//   }

   /**
    * Metoda vrací název adresáře s požadovaným souborem (bez souboru)
    * @param string $file -- název souboru
    * @param string $type -- typ adresáře - konstanta třídy
    * @param boolean $engine -- jestli se jedná o objekt enginu nebo modulu
    * @return string -- adresář bez souboru
    */
//   public static function getFileDir($file, $dir = self::TEMPLATES_DIR, $moduleName = null) {
//      $faceDir = './'.self::FACES_DIR.DIRECTORY_SEPARATOR.self::$face.DIRECTORY_SEPARATOR
//          .AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR
//          .$dir.DIRECTORY_SEPARATOR;
//      $mainDir = './'.AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR
//          .$dir.DIRECTORY_SEPARATOR;
//      // pokud existuje soubor ve vzhledu
//      if(file_exists($faceDir.$file)) {
//         return $faceDir;
//      } else if(file_exists($mainDir.$file)) {
//         return $mainDir;
//      } else {
//         trigger_error(sprintf(_('Soubor "%s" s šablonou v modulu "%s" nebyl nalezen'),
//               $file, $moduleName),E_NOTICE);
//      }
//   }
}
?>