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
   //      if($this->min === null){
   //         $element->htmlLabel()->addContent(' '.sprintf(_("(max. %s znaků)"), $this->max));
   //      } else if($this->max === null){
            $element->htmlLabel()->addContent(' '.sprintf(_("(min. %s znaků)"), $this->min));
   //      } else if($this->min == $this->max){
   //         $element->htmlLabel()->addContent(' '.sprintf(_("(%s znaků)"), $this->max));
   //      } else {
   //         $element->htmlLabel()->addContent(' '.sprintf(_("(%s - %s znaků)"),$this->min, $this->max));
   //      }
   }

   public function validate(Form_Element $elemObj) {
      switch (get_class($elemObj)) {
         case 'Form_Element_Text':
         case 'Form_Element_Password':
            if($elemObj->isDimensional() OR $elemObj->isMultiLang()) {

            } else {
               if(strlen($elemObj->getValues()) < $this->min) {
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
