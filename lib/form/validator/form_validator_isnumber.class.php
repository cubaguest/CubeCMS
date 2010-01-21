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

/**
 * O jaký druh čísla se jedná
 * @var string
 */
   private $numberType = 'int';


   public function  __construct($errMsg = null, $numberType = 'int') {
      if($errMsg == null) {
         parent::__construct(_("Položka \"%s\" ne ve správném formátu"));
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

   public function validate(Form_Element $elemObj) {
      switch (get_class($elemObj)) {
      // input text
         case 'Form_Element_Text':
         case 'Form_Element_TextArea':
         case 'Form_Element_Password':
            if($elemObj->isMultiLang()) {
               // pokud se kontrolují jen některé sloupce
//               if(!empty ($this->columsNotEmpty)){
//                  $retu = $this->checkEmptyValues($elemObj->getValues(), $this->columsNotEmpty);
//                  if($retu !== true){
//                     $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel().' '.$retu));
//                     return false;
//                  }
//               }
//               // pokud mají být vyplněny všechny sloupce
//               else {
//                  if(!$this->checkEmptyAllValues($elemObj->getValues())){
//                     $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
//                     return false;
//                  }
//               }
            } else {
//               if($elemObj->getValues() == null OR $elemObj->getValues() == "") {
//                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
//                  return false;
//               }
            }
            break;
         // input checkbox
         case 'Form_Element_Checkbox':
//            if($elemObj->getValues() == false) {
//               $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
//               return false;
//            }
            break;
         default:
            break;
      }
      return true;

   }
}
?>
