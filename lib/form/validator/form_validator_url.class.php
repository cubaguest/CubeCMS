<?php
/**
 * Třída formulářového validátoru pro kontrolu url adresy
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision:  $
 * @author        $Author:  $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída validátoru pro kontrolu emailu
 */
class Form_Validator_Url extends Form_Validator implements Form_Validator_Interface {
   public function  __construct($errMsg = null) {
      if($errMsg == null) {
         parent::__construct($this->tr('Položka "%s" není korektní URL adresa'));
      } else {
         parent::__construct($errMsg);
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      $element->addValidationConditionLabel($this->tr('např: http://www.google.cz'));
   }

   public function validate(Form_Element $elemObj) {
      $name = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]'; // znaky tvořící uživatelské jméno
      $domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])'; // jedna komponenta domény
      switch (get_class($elemObj)) {
         // input text
         case 'Form_Element_Text':
         case 'Form_Element_Password':
            if($elemObj->isMultiple() OR $elemObj->isMultiLang()) {
               foreach($elemObj->getUnfilteredValues() as $value){
                  if($value != null && !filter_var($value, FILTER_VALIDATE_URL)){
                     $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
                     return false;
                  }
               }

            } else {
               if($elemObj->getUnfilteredValues() != null
//                  AND !preg_match('/^(?#Protocol)(?:(?:ht|f)tp(?:s?)\:\/\/|~\/|\/)?(?#Username:Password)(?:\w+:\w+@)?(?#Subdomains)(?:(?:[-\w]+\.)+(?#TopLevel Domains)(?:com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum|travel|[a-z]{2}))(?#Port)(?::[\d]{1,5})?(?#Directories)(?:(?:(?:\/(?:[-\w~!$+|.,=]|%[a-f\d]{2})+)+|\/)+|\?|#)?(?#Query)(?:(?:\?(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=?(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)(?:&(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=?(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)*)*(?#Anchor)(?:#(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)?$/i', $elemObj->getUnfilteredValues())
                  AND !filter_var($elemObj->getUnfilteredValues(), FILTER_VALIDATE_URL)
               ){
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
