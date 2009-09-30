<?php
/**
 * Třída pro obsluhu html elementů
 * Třída slouží pro práci s jednotlivými html elemnty v šabloně, Jejich správné
 * a validní vykreslení a jednoduchou práci s elementy. Do elementu se dají vkládat
 * další instance a potomci této třídy. Při vykreslení jsou vykresleni také
 * podřízené instance této třídy.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 5.2.0 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu html elementů a jejich vykreslení
 */

class Html_Element_Script extends Html_Element {
   /**
    * Konstruktor pro vatvoření html tagu
    * @param string $name -- název tagu
    * @param string $content -- obsah elementu
    */
   public function  __construct($content = null) {
      parent::__construct('script', $content);
   }

   /**
    * Metoda vrátí obsah elementu
    */
   public function __toStringContent() {
      $content = "/* <![CDATA[ */\n";
      $content .= $this->content;
      $content .= "\n/* ]]> */\n";
      return $content;
   }
}
?>