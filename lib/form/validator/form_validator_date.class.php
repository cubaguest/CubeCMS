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
         parent::__construct($this->tr('Nebylo vyplněno korektní datum v položce "%s"'));
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
               
               if($elemObj->isDimensional()){
                  $correctDate = true;
                  $someValidate = false;
                  foreach ($values as $value) {
                     if(!empty($value) && !$this->validateDate($value)){
                        $correctDate = false;
                        $someValidate = true;
                     }
                  }
                  if($someValidate && !$correctDate){
                     $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
                     return false;
                  }
               } else {
                  if(!empty($values) && !$this->validateDate($values)){
                     $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
                     return false;
                  }
               }
            } else {
               throw new RuntimeException($this->tr('Neimplementovaná Validace Data'));
            }
      }
      return true;
   }
   
   private function validateDate($dateStr)
   {
      $date = array();
      if(preg_match("/^([0-3]?[0-9]{1})\.([0-1]?[0-9]{1})\.([1-2]{1}[0-9]{3})$/", $dateStr, $date) == 1 
            AND checkdate($date[2],$date[1],$date[3])){
         // dd.mm.yyyy
         return true;
      } else if(preg_match('/^([1-2]{1}[0-9]{3}).([0-1]?[0-9]{1}).([0-3]?[0-9]{1})$/', $dateStr, $date) == 1
              AND checkdate($date[1], $date[2], $date[3])) {
         // yyyy.mm.dd
         return true;
      } else if(preg_match('/^([0-1]?[0-9]{1})\/([0-3]?[0-9]{1})\/([1-2]{1}[0-9]{3})$/', $dateStr, $date) == 1
              AND checkdate($date[1], $date[2], $date[3])) {
         // mm/dd/yyyy
         return true;
      }
      return false;
   }
}
