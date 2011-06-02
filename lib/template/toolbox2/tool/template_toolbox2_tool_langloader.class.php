<?php
/**
 * Třída pro vytvoření objektu nástroje (tool) pro toolbox verze 2
 * Třída vytváří objekt nástroje typu redirect (přesměrování). Tento
 * nástroj po kliknutí přesměruje na danou akci
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.1.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Rozhraní pro tvorbu nástroje toolboxu redirect
 */

class Template_Toolbox2_Tool_LangLoader extends Template_Toolbox2_Tool implements Template_Toolbox2_Tool_Interface {
   /**
    * Pole s jazyky
    * @var array
    */
   private $langs = null;

   public function  __construct($text = null) {
      $langs = Locales::getAppLangsNames();
      if($text instanceof Model_ORM_LangCell OR $text instanceof Model_LangContainer_LangColumn){
         foreach ($text as $lang => $val) {
            if($val == null) continue;
            $this->langs[$lang] = $langs[$lang];
         }
      } else {
         $this->langs = $langs;
      }
      $this->setIcon('lang_sel.png');
   }

   /**
    * Metoda vrací pole s jazyky, pro které je daný text přeložen
    * @return Form
    */
   public function getLangs() {
      return $this->langs;
   }

}
?>
