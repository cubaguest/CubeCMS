<?php
/**
 * Třída formulářového validátoru pro kontrolu času ve formátu : HH:MM a HH:MM:SS
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision:  $
 * @author        $Author:  $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída validátoru pro kontrolu času
 */
class Form_Validator_Time extends Form_Validator implements Form_Validator_Interface {

   public function  __construct($errMsg = null) {
      if($errMsg == null) {
         parent::__construct($this->tr('Nebyl vyplněn korektní čas v položce "%s"'));
      } else {
         parent::__construct($errMsg);
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
       $element->addValidationConditionLabel($this->tr("HH:MM nebo HH:MM:SS"));
   }

   public function validate(Form_Element $elemObj) {
      switch (get_class($elemObj)) {
         // input text
         case 'Form_Element_Text':
         case 'Form_Element_TextArea':
         case 'Form_Element_Password':
            $values = $elemObj->getUnfilteredValues();
            if(!$elemObj->isMultiLang()) {
               if($elemObj->isDimensional()){
                  $correctTime = true;
                  $someValidate = false;
                  foreach ($values as $value) {
                     if(!empty($value) && !$this->validateTime($value)){
                        $correctTime = false;
                        $someValidate = true;
                     }
                  }
                  if($someValidate && !$correctTime){
                     $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
                     return false;
                  }
               } else {
                  if(empty ($values)) return true;
                  if(!$this->validateTime($values)){
                     $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
                  }
               }
            } else {
               throw new RuntimeException($this->tr('Neimplementovaná Validace času'));
            }
      }
      return true;

   }
   
   protected function validateTime($timeStr)
   {
      $time = array();
      if(preg_match("/^([0-2]?[0-9]{1}):([0-5]?[0-9]{1})(:([0-5]?[0-9]{1}))?$/", $timeStr, $time) == 0
              OR $time[1] > 23 OR $time[2] > 59 OR (isset ($time[3]) AND $time[3] > 59)
         ) {
         return false;
      }
      return true;
   }
}
?>
