<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of form_validate_noemptyclass
 *
 * @author jakub
 */
class Form_Validator_IsNumber extends Form_Validator implements Form_Validator_Interface {

   const TYPE_INT = 1;
   const TYPE_FLOAT = 2;

   /**
 * O jaký druh čísla se jedná
 * @var string
 */
   private $numberType = 1;


   public function  __construct($errMsg = null, $numberType = self::TYPE_INT) {
      if($errMsg == null) {
         parent::__construct(_("Položka \"%s\" ne ve správném číselném formátu"));
      } else {
         parent::__construct($errMsg);
      }
      $this->numberType = $numberType;
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      if($element instanceof Form_Element_Text
         OR $element instanceof Form_Element_TextArea){
         switch ($this->numberType) {
            case 'float':
               $element->addValidationConditionLabel(_("desetiné číslo"));
               break;
            case 'int':
            default:
               $element->addValidationConditionLabel(_("celé číslo"));
               break;
         }
      }
   }

   public function validate(Form_Element $elemObj) {
      switch (get_class($elemObj)) {
      // input text
         case 'Form_Element_Text':
         case 'Form_Element_TextArea':
         case 'Form_Element_Password':
            if($elemObj->isDimensional() OR $elemObj->isMultiLang()) {

            } else {
               if($this->checkNumber($elemObj->getUnfilteredValues()) == false){
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


   public function checkNumber($number) {
      switch ($this->numberType) {
         case self::TYPE_INT:
            return ctype_digit($number);
            break;
         case self::TYPE_FLOAT:

            break;
         default :
            $this->errMessage = _('Nepodporovaný typ validace čísla');
            break;
      }
      return false;
   }
}
?>
