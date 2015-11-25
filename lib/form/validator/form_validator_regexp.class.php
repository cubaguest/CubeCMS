<?php
/**
 * TY�da formul�Yov�ho valid�toru pro kontrolu prvku podle regulern�ho v�razu
 *
 * @copyright  	Copyright (c) 2010 Jakub Matas
 * @version    	$Id: $ VVE 6.3.0 $Revision:  $
 * @author        $Author:  $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      TY�da valid�toru pro kontrolu prvku podle reg. v�razu
 */
class Form_Validator_Regexp extends Form_Validator implements Form_Validator_Interface {
   const PHONE = 1;
   
   
   const REGEXP_PHONE_CZSK = '/^(\+42[01]{1}[ ]?)?([0-9]{3})[ ]?([0-9]{3})[ ]?([0-9]{3})$/';
   const REGEXP_PHONE_ES = '/^(\+34)?[ ]?[0-9][ ]?[0-9][ ]?[0-9][ ]?[0-9][ ]?[0-9][ ]?[0-9][ ]?[0-9][ ]?[0-9][ ]?[0-9][ ]?$/';

   private $regexp = null;

   public function  __construct($regexp='//', $errMsg = null) {
      if( ($regexp == self::REGEXP_PHONE_CZSK || $regexp == self::PHONE) AND $errMsg == null) {
         $errMsg = $this->tr('Položka "%s" neobsahuje platné telefonní číslo');
      }
      // výchozí zpráva
      if($errMsg == null) {
         $errMsg = $this->tr('Položka "%s" nevyhovuje zadanému regulérnímu výrazu');
      }
      parent::__construct($errMsg);
      
      if($regexp == self::PHONE){
         $regexp = self::REGEXP_PHONE_CZSK;
         if(Locales::getLang() == 'cs' || Locales::getLang() == 'sk'){
            $regexp = self::REGEXP_PHONE_CZSK;
         } else if(Locales::getLang() == 'es'){
            $regexp = self::REGEXP_PHONE_ES;
         }
      }
      $this->regexp = $regexp;
   }

   /**
    * Metoda pYid� do elementu prvky z validace
    * @param Form_Element $element -- samotn� element
    */
   public function addHtmlElementParams(Form_Element $element) {
      if($this->regexp == self::REGEXP_PHONE_CZSK 
          || $this->regexp == self::REGEXP_PHONE_ES
          ) {
         $element->addValidationConditionLabel($this->tr('např: +420 123 456 789'));
         if($element->hasValidator("Form_Validator_NotEmpty") && $element->getUnfilteredValues() == null){
            if($this->regexp == self::REGEXP_PHONE_CZSK){
               $element->setValues('+420');
            } else if($this->regexp == self::REGEXP_PHONE_ES){
               $element->setValues('+34');
               
            }
         } 
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
               $values = trim($elemObj->getUnfilteredValues());
               if(preg_match($this->regexp, $values) == 0
                  OR preg_match($this->regexp, $values) === false ){
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
