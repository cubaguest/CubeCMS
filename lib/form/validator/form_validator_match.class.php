<?php
/**
 * Description of form_validate_noemptyclass
 *
 * @author jakub
 */
class Form_Validator_Match extends Form_Validator implements Form_Validator_Interface {

/**
 * Minimální délka řetězce
 * @var int
 */
   private $match = null;

   public function  __construct($match, $message = null) {
      $this->match = $match;
      if($message != null) {
         parent::__construct($message);
      } else {
         parent::__construct($this->tr('"%s" musí obsahovat hodnotu "%s"'));
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
   }

   public function validate(Form_Element $elemObj) {
      switch (get_class($elemObj)) {
//         case 'Form_Element_Text':
//         case 'Form_Element_TextArea':
//         case 'Form_Element_Password':
         case 'Form_Element_Checkbox':
            if($elemObj->isDimensional() OR $elemObj->isMultiLang()) {

            } else {
               if(strlen($elemObj->getUnfilteredValues()) != $this->match) {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel(), $this->match));
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
