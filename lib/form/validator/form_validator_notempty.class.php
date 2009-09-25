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
class Form_Validator_NotEmpty extends Form_Validator implements Form_Validator_Interface {

   public function  __construct($errMsg = null) {
      if($errMsg == null) {
         parent::__construct(_("Nebyly vyplněny všechny povinné údaje"));
      } else {
         parent::__construct($errMsg);
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      $element->htmlLabel()->addClass('requiredElem');
      $element->htmlLabel()->setAttrib('title', _('prvek je povinný'));
   }

   public function validate(Form_Element $elemObj) {
      if($elemObj->isMultiple() OR $elemObj->isMultiLang()) {

      } else {
         if($elemObj->getValues() == null OR $elemObj->getValues() == "") {
            $this->errMsg()->addMessage($this->errMessage);
            return false;
         }
      }
      return true;
   }

   private function checkEmptyValues($array) {
      foreach ($array as $key => $val) {
         ;
      }
   }
}
?>
