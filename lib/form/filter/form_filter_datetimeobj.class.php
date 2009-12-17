<?php
/**
 * Třída přefiltruje element a vytvoří v něm datum se zadaným formátem
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro vytvoření fitru
 */
class Form_Filter_DateTimeObj extends Form_Filter {
//   private $outputDateFormat = null;

   public function  __construct() {
//      $format = 'Y-m-d H:i';
//      $this->outputDateFormat = $format;
   }

   /**
    * Metoda aplikuje filtr na daný element
    * @param Form_Element $elem
    */
   public function filter(Form_Element &$elem) {
      $values = $elem->getValues();
         switch (get_class($elem)) {
            case "Form_Element_Text":
               $this->filterValue($values);
               break;
            default:
               break;
         }
      $elem->setValues($values);
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
         // filtr podle jazyka
//         $matches = array();
//         switch (Locale::getLang()) {
//               // f: d.m.Y
//            case 'cs':
//                  preg_match("/^(?P<day>[0-9]{1,2}).(?P<mounth>[0-9]{1,2}).(?P<year>[0-9]{4})$/", $variable, $matches);
//                  break;
//               // f: m.d.Y
//               default:
//                  break;
//            }
//         $variable = strftime($this->outputDateFormat, mktime(0, 0, 0, $matches['mounth'],$matches['day'],$matches['year']));
         if(!empty ($variable)){
            $date = new DateTime($variable);
            $variable = $date;
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
