<?php
/**
 * Třída přefiltruje element a odstraní nepovoléné html elementy
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída fitru pro odstranění html elementů
 */
class Form_Filter_StripTags extends Form_Filter {
   /**
    * Povolené tagy
    * @var array
    */
   private $allowedTags = array();

   /**
    * Řetězec pro strip_tags
    * @var string
    */
   private $allowedTagsStr = null;

   public function  __construct($allowedTags = array()) {
      $this->allowedTags = $allowedTags;
   }

   /**
    * Metoda aplikuje filtr na daný element
    * @param Form_Element $elem
    */
   public function filter(Form_Element &$elem, &$values) {
      // vytvoříme řetězec pro strip tags
      foreach ($this->allowedTags as $tag) {
         $this->allowedTagsStr .= '<'.$tag.'>';
      }
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
         $variable = strip_tags($variable, $this->allowedTagsStr);
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {}

}
?>
