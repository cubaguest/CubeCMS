<?php
/**
 * Třída formulářového validátoru pro kontrolu prvku podle regulerního výrazu
 *
 * @copyright  	Copyright (c) 2010 Jakub Matas
 * @version    	$Id: $ VVE 6.3.0 $Revision:  $
 * @author        $Author:  $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída validátoru pro kontrolu prvku podle reg. výrazu
 */
class Form_Validator_Regexp extends Form_Validator implements Form_Validator_Interface {
   const REGEXP_PHONE_CZSK = '/^\+42[01]{1}[ ]?([0-9]{3})[ ]?([0-9]{3})[ ]?([0-9]{3})$/';


   private $regexp = null;

   public function  __construct($regexp='//', $errMsg = null) {
      if($errMsg == null) {
         parent::__construct($this->tr('Položka "%s" nevyhovuje zadanému regulérnímu výrazu'));
      } else if($regexp == self::REGEXP_PHONE_CZSK AND $errMsg == null) {
         parent::__construct($this->tr('Položka "%s" neobsahuje platné telefonní číslo'));
      } else {
         parent::__construct($errMsg);
      }
      $this->regexp = $regexp;
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      if($this->regexp == self::REGEXP_PHONE_CZSK) {
         $element->addValidationConditionLabel($this->tr('např: +420 123 456 789'));
      }
   }

   public function validate(Form_Element $elemObj) {
      if($elemObj->getUnfilteredValues() == null) return true;
      switch (get_class($elemObj)) {
         // input text
         case 'Form_Element_Text':
         case 'Form_Element_Password':
         case 'Form_Element_TextArea':
            if($elemObj->isDimensional() OR $elemObj->isMultiLang()) {

            } else {
               if(preg_match($this->regexp, $elemObj->getUnfilteredValues()) == 0
                  OR preg_match($this->regexp, $elemObj->getUnfilteredValues()) === false ){
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
