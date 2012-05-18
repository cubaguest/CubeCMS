<?php
/**
 * Validátor kontroluje jestli prvek není již v poli obsažen
 *
 * @author jakub
 */
class Form_Validator_InArray extends Form_Validator implements Form_Validator_Interface {

   /**
    * Pole s prvky
    * @var array
    */
   private $array = array();


   public function  __construct($array, $errMsg = null) {
      if($errMsg == null) {
         parent::__construct($this->tr('Položka "%s" s hodnotou "%s" není obsažena v povolených hodnotách. Povolené hodnoty: ')."(".  implode(", ", $array ).")");
      } else {
         parent::__construct($errMsg);
      }
      $this->array = $array;
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
   }

   public function validate(Form_Element $elemObj) {
      if($elemObj->getUnfilteredValues() == null) return true;// nekontrolujeme prázdý řetězec to je na validátoru notEmpty
      $vals = $elemObj->getUnfilteredValues();
      switch (get_class($elemObj)) {
      // input text
         case 'Form_Element_Text':
         case 'Form_Element_TextArea':
         case 'Form_Element_Password':
            if($elemObj->isMultiLang()) {
               trigger_error('Not implemented validation !!');
            } else {
               if(!in_array($vals, $this->array)) {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel(),$vals));
                  return false;
               }
            }
            break;
         case 'Form_Element_Select':
         case 'Form_Element_Radio':
            if(is_array($vals)){
               
            } else {
               if(!in_array($vals, $this->array)) {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel(),$vals));
                  return false;
               }
            }
         default:
            break;
      }
      return true;
   }
}
?>
