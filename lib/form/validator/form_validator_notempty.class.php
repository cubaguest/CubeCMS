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
      parent::__construct($errMsg);
      if($errMsg == null){
         $this->errMessage = _("Nebyly vyplněny všechny povinné údaje");
      } else {
         $this->errMessage = $errMsg;
      }
   }

   public function validate(Form_Element $elemObj) {
      if($elemObj->multiple() OR $elemObj->multiLang()){

      } else {
         if($elemObj->getValues() == null OR $elemObj->getValues() == ""){
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
