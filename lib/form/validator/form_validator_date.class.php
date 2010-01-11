<?php
/**
 * Třída formulářového validátoru pro kontrolu data
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision:  $
 * @author        $Author:  $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída validátoru pro kontrolu data
 */
class Form_Validator_Date extends Form_Validator implements Form_Validator_Interface {

   public function  __construct($errMsg = null, $beforeDate = null, $afterDate = null) {
      if($errMsg == null) {
         parent::__construct(_('Nebylo vyplněno korektní datum v položce \"%s\"'));
      } else {
         parent::__construct($errMsg);
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
   }

   public function validate(Form_Element $elemObj) {
      switch (get_class($elemObj)) {
         // input text
         case 'Form_Element_Text':
         case 'Form_Element_TextArea':
         case 'Form_Element_Password':
            $values = $elemObj->getUnfilteredValues();
            if(!$elemObj->isMultiLang()) {
               $date = array();
               if(preg_match("/^([0-3]?[0-9]{1})\.([0-1]?[0-9]{1})\.([1-2]{1}[0-9]{3})$/", $values, $date)
                       AND checkdate($date[2],$date[1],$date[3])) {
                  return true;
               } else if(preg_match('/([1-2]{1}[0-9]{3}).([0-1]?[0-9]{1}).([0-3]?[0-9]{1})/', $values, $date)
                       AND checkdate($date[1], $date[2], $date[3])) {
                  return true;
               }
            } else {
               throw new RuntimeException(_('Neimplementovaná Validace Data'));
            }
      }
      return true;

   }
}
?>
