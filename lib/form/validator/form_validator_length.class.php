<?php
/**
 * Description of form_validate_noemptyclass
 *
 * @author jakub
 */
class Form_Validator_Length extends Form_Validator implements Form_Validator_Interface {

/**
 * Maximální délka řetězce
 * @var int
 */
   private $max = null;

   /**
    * Minimální délka řetězce
    * @var int
    */
   private $min = null;

   public function  __construct($min = null, $max = null, $message = null) {
      $this->max = $max;
      $this->min = $min;
      if($message != null) {
         parent::__construct($message);
      } else {
         parent::__construct(_("\"%s\" nemá požadovanou délku"));
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      if($this->min === null) {
         $element->addValidationConditionLabel(sprintf(_("max. %s znaků"), $this->max));
         $element->html()->setAttrib('maxlength', $this->max);
      } else if($this->max === null) {
         $element->addValidationConditionLabel(sprintf(_("min. %s znaků"), $this->min));
      } else if($this->min == $this->max) {
         $element->addValidationConditionLabel(sprintf(_("%s znaků"), $this->max));
         $element->html()->setAttrib('maxlength', $this->max);
      } else {
         $element->addValidationConditionLabel(sprintf(_("%s - %s znaků"),$this->min, $this->max));
         $element->html()->setAttrib('maxlength', $this->max);
      }
   }

   public function validate(Form_Element $elemObj) {
      switch (get_class($elemObj)) {
         case 'Form_Element_Text':
         case 'Form_Element_Password':
            if($elemObj->isDimensional() OR $elemObj->isMultiLang()) {

            } else {
               if($elemObj->getUnfilteredValues() != null AND !is_null($this->max)
                  AND strlen($elemObj->getUnfilteredValues()) > $this->max) {
                  $this->errMsg()->addMessage(sprintf(_("\"%s\" je příliš dlouhý"), $elemObj->getLabel()));
                  return false;
               } else if($elemObj->getUnfilteredValues() != null
                  AND !is_null($this->min) AND strlen($elemObj->getUnfilteredValues()) < $this->min) {
                  $this->errMsg()->addMessage(sprintf(_("\"%s\" je příliš krátký"), $elemObj->getLabel()));
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
