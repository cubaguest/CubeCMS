<?php
/**
 * Třída formulářového validátoru pro kontrolu emailu
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision:  $
 * @author        $Author:  $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída validátoru pro kontrolu emailu
 */
class Form_Validator_Email extends Form_Validator implements Form_Validator_Interface {
   public function  __construct($errMsg = null) {
      if($errMsg == null) {
         parent::__construct(_("Položka \"%s\" není korektní emailová adresa"));
      } else {
         parent::__construct($errMsg);
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      $element->addValidationConditionLabel(_('např: info@domena.cz'));
   }

   public function validate(Form_Element $elemObj) {
      $name = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]'; // znaky tvořící uživatelské jméno
      $domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])'; // jedna komponenta domény
      switch (get_class($elemObj)) {
         // input text
         case 'Form_Element_Text':
         case 'Form_Element_Password':
            if($elemObj->isDimensional() OR $elemObj->isMultiLang()) {

            } else {
               if($elemObj->getUnfilteredValues() != null AND !eregi("^$name+(\\.$name+)*@($domain?\\.)+$domain\$", $elemObj->getUnfilteredValues())){
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
