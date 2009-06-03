<?php
/**
 * Třída pro práci s šablonami modulu.
 * Třida obsahuje vrstvu mezi šablonovacím systémem a samotným pohledem (viewrem). 
 * Umožňuje všechny základní operace při volbě a plnění šablony a jejímu zobrazení.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: template.class.php 576 2009-04-15 10:52:59Z jakub $ VVE3.9.4 $Revision: 576 $
 * @author        $Author: jakub $ $Date: 2009-04-15 10:52:59 +0000 (St, 15 dub 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-04-15 10:52:59 +0000 (St, 15 dub 2009) $
 * @abstract 		Třída pro obsluhu šablony
 */

class CoreTemplate extends Template {

   public function getStylesheets() {
      return self::$stylesheets;
   }

   public function getJavascripts() {
      return self::$javascripts;
   }

   public function style($name) {
      return parent::getFileDir($name, self::STYLESHEETS_DIR).$name;
   }

   /**
    * metoda přidává zadany css styl do výstupu
    * @param string -- název scc stylu
    * @param boolean -- true pokud je zadána i cesta se souborem
    */
   public static function addCss($cssName){
      //TODO kontrola souborů
      if(!in_array($cssName, self::$stylesheets)){
         array_push(self::$stylesheets, $cssName);
      }
   }

   /**
    * metoda přidává zadaný javascript do výstupu
    * @param string -- název javascriptu
    * @param boolean -- true pokud je zadána i cesta se souborem
    */
   public static function addJS($jsFile){
      //TODO kontrola souborů
      if(!in_array($jsFile, self::$javascripts)){
         array_push(self::$javascripts, $jsFile);
      }
   }

}
?>