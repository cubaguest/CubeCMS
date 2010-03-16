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
class Form_Filter_Url extends Form_Filter {
   /**
    * Výchozí prefix při doplňování
    * @var string
    */
   private $prefix = null;

   public function  __construct($prefix = 'http://') {
      $this->prefix = $prefix;
   }

   /**
    * Metoda aplikuje filtr na daný element
    * @param Form_Element $elem
    */
   public function filter(Form_Element &$elem, &$values) {
      switch (get_class($elem)) {
         case "Form_Element_Text":
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
         if($variable != null AND !preg_match('/^([a-z]+:\/\/)/i', $variable)){
            $variable = $this->prefix.$variable;
         }
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {}

}
?>
