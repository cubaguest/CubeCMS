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
class Form_Validator {
   protected $errMessage = null;

   public function  __construct($errMessage = null) {
      $this->errMessage = $errMessage;
   }

   /**
    * Metoda vrací objekt k chybovým hláškám modulů
    * @return Messages
    */
   public function errMsg() {
      return AppCore::getUserErrors();
   }

}
?>
