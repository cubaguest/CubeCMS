<?php
/**
 * Validátor kontroluje jestli prvek není již v poli obsažen
 *
 * @author jakub
 */
class Form_Validator_NotInArray extends Form_Validator implements Form_Validator_Interface {

/**
 * Pole s prvky
 * @var array
 */
   private $array = 'int';


   public function  __construct($array, $errMsg = null) {
      if($errMsg == null) {
         parent::__construct(_("Položka \"%s\" s hodnotou \"%s\" je již obsazena"));
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
      switch (get_class($elemObj)) {
      // input text
         case 'Form_Element_Text':
         case 'Form_Element_TextArea':
         case 'Form_Element_Password':
            if($elemObj->isMultiLang()) {
               trigger_error('Not implemented validation !!');
            } else {
               if(in_array($elemObj->getValues(), $this->array)) {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel(),$elemObj->getValues()));
                  return false;
               }
            }
            break;
         default:
            break;
      }
      return true;
   }
}
?>
