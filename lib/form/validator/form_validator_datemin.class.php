<?php

/**
 * Třída formulářového validátoru pro kontrolu data
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ CubeCMS 8.4.9 $Revision:  $
 * @author        $Author:  $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída validátoru pro kontrolu data
 */
class Form_Validator_DateMin extends Form_Validator implements Form_Validator_Interface {

   protected $date = null;

   public function __construct(DateTime $date, $errMsg = null)
   {
      $this->date = $date;
      if ($errMsg == null) {
         parent::__construct($this->tr('Zadané datum v položce "%s" je menší než povolené. Minimální povolené datum je %s.'));
      } else {
         parent::__construct($errMsg);
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element)
   {
      $element->addValidationConditionLabel(sprintf($this->tr("datum od %s"), Utils_DateTime::fdate('%x', $this->date)));
   }

   public function validate(Form_Element $elemObj)
   {
      if ($elemObj instanceof Form_Element_Text || $elemObj instanceof Form_Element_TextArea || $elemObj instanceof Form_Element_Password) {

         $values = $elemObj->getUnfilteredValues();
         if (!$elemObj->isMultiLang()) {
            if ($elemObj->isDimensional()) {
               $correctDate = true;
               $someValidate = false;
               foreach ($values as $value) {
                  if (!empty($value) && !$this->validateDate($value)) {
                     $correctDate = false;
                     $someValidate = true;
                  }
               }
               if ($someValidate && !$correctDate) {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel(), Utils_DateTime::fdate('%x', $this->date)));
                  return false;
               }
            } else {
               if (!empty($values) && !$this->validateDate($values)) {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel(), Utils_DateTime::fdate('%x', $this->date)));
                  return false;
               }
            }
         } else {
            throw new RuntimeException($this->tr('Neimplementovaná Validace Data'));
         }
      }
      return true;
   }

   private function validateDate($date)
   {
      if (!$date instanceof DateTime) {
         $date = new DateTime($date);
      }
      return $this->date < $date;
   }

}
