<?php
/**
 * Description of form_validate_noemptyclass
 *
 * @author jakub
 */
class Form_Validator_MaxLength extends Form_Validator implements Form_Validator_Interface {

/**
 * Minimální délka řetězce
 * @var int
 */
   private $max = null;

   public function  __construct($max = null, $message = null) {
      $this->max = $max;
      if($message != null) {
         parent::__construct($message);
      } else {
         parent::__construct(_("\"%s\" je příliš dlouhý, maximálně %s znaků"));
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      $element->addValidationConditionLabel(sprintf(_("max. %s znaků"), $this->max));
   }

   public function validate(Form_Element $elemObj) {
      switch (get_class($elemObj)) {
         case 'Form_Element_Text':
         case 'Form_Element_TextArea':
         case 'Form_Element_Password':
            if($elemObj->isDimensional() OR $elemObj->isMultiLang()) {

            } else {
               if(strlen($elemObj->getUnfilteredValues()) > $this->max) {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel(), $this->max));
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
