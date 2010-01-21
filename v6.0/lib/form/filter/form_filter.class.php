<?php
/**
 * Třída pro implementaci filtru aplikujícího na formulářová element
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro vytvoření fitru
 */
class Form_Filter implements Form_Filter_Interface {
   public function  __construct() {
   }

   /**
    * Metoda aplikuje filtr na daný element
    * @param Form_Element $elem
    */
   public function filter(Form_Element &$elem, &$values) {}

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {}

}
?>
