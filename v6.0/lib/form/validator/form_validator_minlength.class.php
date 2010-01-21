<?php
/**
 * Description of form_validate_noemptyclass
 *
 * @author jakub
 */
class Form_Validator_MinLength extends Form_Validator implements Form_Validator_Interface {

/**
 * Minimální délka řetězce
 * @var int
 */
   private $min = null;

   public function  __construct($min = null, $message = null) {
      $this->min = $min;
      if($message != null) {
         parent::__construct($message);
      } else {
         parent::__construct(_("\"%s\" je příliš krátký"));
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      $element->htmlLabel()->addContent(' '.sprintf(_("(min. %s znaků)"), $this->min));
   }

   public function validate(Form_Element $elemObj) {
      switch (get_class($elemObj)) {
         case 'Form_Element_Text':
         case 'Form_Element_Password':
            if($elemObj->isDimensional() OR $elemObj->isMultiLang()) {

            } else {
               if(strlen($elemObj->getUnfilteredValues()) < $this->min) {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
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
