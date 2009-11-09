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
    * Proměná obsahuje odkaz na stránku
    * @var Url_Link
    */
   private $pageLink = null;
   /**
    * KOnstruktor vytvoří objekt šablony pro modul
    * @param Url_Link_Module $link -- objekt odkazu
    * @param Category $category -- objekt kategorie
    */
   function  __construct(Url_Link_Component $link, Url_Link $pageLink) {
      $this->pageLink = $pageLink;
      parent::__construct($link);
   }

   /**
    * Metoda vrací objekt kategorie
    * @return Category
    */
   public function category() {
      return Category::getSelectedCategory();
   }

   /**
    * Metoda vrací odkaz na stránku
    * @return Url_Link 
    */
   public function pageLink() {
      return $this->pageLink;
   }
}
?>