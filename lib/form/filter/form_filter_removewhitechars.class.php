<?php
/**
 * Třída přefiltruje element a odstraní bílé znaky (mezery, tabulátory, konce řádků)
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.0.2 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída fitru pro odstranění bílých znaků
 */
class Form_Filter_RemoveWhiteChars extends Form_Filter {
   public function  __construct() {}

   /**
    * Metoda aplikuje filtr na daný element
    * @param Form_Element $elem
    */
   public function filter(Form_Element &$elem, &$values) {
      // vytvoříme řetězec pro strip tags
      switch (get_class($elem)) {
         case "Form_Element_Text":
         case "Form_Element_TextArea":
            $this->filterValue($values);
            break;
         default:
            break;
      }
   }

   /**
    * Metoda provede filtraci
    * @param string/array $variable
    */
   private function filterValue(&$variable) {
      if(is_array($variable)){
         foreach ($variable as &$var) {
            $this->filterValue($var);
         }
      } else {
         $variable = preg_replace("/[\s]/", "", $variable);
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {}

}
?>
