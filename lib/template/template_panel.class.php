<?php
/**
 * Třída pro práci s šablonami panelu modulu.
 * Třida obsahuje vrstvu mezi šablonovacím systémem a samotným pohledem (viewrem)
 * modulu. Umožňuje všechny základní operace při volbě a plnění šablony a jejímu
 * zobrazení v modulu.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu šablony
 */


class Template_Panel extends Template_Module {
   /**
    * Objekt panelu pro kterou je šablona tvořena
    * @var Category
    */
   private $panelObj = null;

   /**
    * KOnstruktor vytvoří objekt šablony pro modul
    * @param Url_Link_Module $link -- objekt odkazu
    * @param Category $category -- objekt kategorie
    */
   function  __construct(Url_Link_Module $link, Category $category, Panel_Obj $panel) {
      parent::__construct($link, $category);
      $this->panelObj = $panel;
   }

   /**
    * Metoda vrací objekt panelu
    * @return Panel_Obj -- objekt panelu
    */
   public function panelObj(){
      return $this->panelObj;
   }
}
?>