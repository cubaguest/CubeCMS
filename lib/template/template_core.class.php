<?php
/**
 * Třída pro práci s šablonami modulu.
 * Třida obsahuje vrstvu mezi šablonovacím systémem a samotným pohledem (viewrem). 
 * Umožňuje všechny základní operace při volbě a plnění šablony a jejímu zobrazení.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu šablony
 */

class Template_Core extends Template {

   /**
    * Konstruktor
    */
   function  __construct() {
      parent::__construct(new Url_Link());
   }

   /**
    * Metoda vrací pole se všemy css soubory
    * @return array
    */
   public function getStylesheets() {
      return self::$stylesheets;
   }

   /**
    * Metoda vrací pole se všemi javascripty
    * @return array
    */
   public function getJavascripts() {
      return self::$javascripts;
   }

   /**
    * Metoda vkládá cestu k css souboru i s názvem souboru
    * @param string $name -- název souboru
    * @return string -- cesta k souboru
    */
   public function style($name) {
      return parent::getFileDir($name, self::STYLESHEETS_DIR).$name;
   }
}
?>