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
class Form_Validator_DateMax extends Form_Validator_DateMin implements Form_Validator_Interface {

   public function __construct($errMsg = null, $beforeDate = null, $afterDate = null)
   {
      if ($errMsg == null) {
         parent::__construct($this->tr('Zadané datum v položce "%s" je větší než povolené. Maximální povolené datum je %s.'));
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
      $element->addValidationConditionLabel(sprintf($this->tr("datum do %s"), Utils_DateTime::fdate('%x', $this->date)));
   }

   private function validateDate($date)
   {
      if (!$date instanceof DateTime) {
         $date = new DateTime($date);
      }
      return $this->date < $date;
   }

}
