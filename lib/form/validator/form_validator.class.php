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
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {}

   /**
    * Metoda vrací objekt k chybovým hláškám modulů
    * @return Messages
    */
   public function errMsg() {
      return AppCore::getUserErrors();
   }

}
?>
