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
class Form_Validator_NotAllowedChars extends Form_Validator implements Form_Validator_Interface {
   /**
    * Pole s nepovolenými znaky
    * @var array
    */
   private $notAllowedChars = array();

   public function  __construct($chars = null, $errMsg = null) {
      trigger_error("Validator ".__CLASS__." není implementován");
      if(!is_array($chars)){
         $chars = array($chars);
      }
      $this->notAllowedChars = $chars;
      if($errMsg == null) {
         parent::__construct(_("Položka \"%s\" obsahuje nepovolené znaky"));
      } else {
         parent::__construct($errMsg);
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      $element->htmlValidLabel()->addContent(' '.sprintf(_("(nepovolené znaky: %s )"), $this->getChars()));
   }

   public function validate(Form_Element $elemObj) {
      switch (get_class($elemObj)) {
         // input text
         case 'Form_Element_Text':
         case 'Form_Element_Password':
            if($elemObj->isDimensional() OR $elemObj->isMultiLang()) {

            } else {
               if($elemObj->getValues() == null OR $elemObj->getValues() == "") {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
                  return false;
               }
            }
            break;
         // input checkbox
         case 'Form_Element_Checkbox':
            if($elemObj->getValues() == false){
               $this->errMsg()->addMessage($this->errMessage);
               return false;
            }
            break;
         default:
            break;
      }
      return true;

   }

   private function getChars() {
//      foreach ($array as $key => $val) {
//         ;
//      }

      return '",",".","!"';
   }
}
?>
