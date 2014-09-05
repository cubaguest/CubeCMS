<?php
/**
 * Třída přefiltruje element a vytvoří v něm validní url adresu (http,ftp)
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída fitru pro vytvoření validní url adresy
 */
class Form_Filter_UrlKey extends Form_Filter {
   protected $removeSlash;


   public function  __construct($removeSlash = true) {
      $this->removeSlash = $removeSlash;
   }

   /**
    * Metoda aplikuje filtr na daný element
    * @param Form_Element $elem
    */
   public function filter(Form_Element &$elem, &$values) {
//      switch (get_class($elem)) {
//         case "Form_Element_Text":
//         case "Form_Element_TextArea":
            $this->filterValue($values);
//            break;
//         default:
//            break;
//      }
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
         $variable = Utils_Url::toUrlKey($variable, $this->removeSlash);
      }
   }
}
