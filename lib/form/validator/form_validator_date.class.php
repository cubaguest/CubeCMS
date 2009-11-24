<?php
/**
 * Třída formulářového elementu pro kontrolu data
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
         parent::__construct(_("Nebyla vyplněna koraktní datum v položce \"%s\""));
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
            break;
         default:
            break;
      }
      return true;

   }
}
?>
