<?php
/**
 * Abstraktní třída formulářového validátoru
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision:  $
 * @author        $Author:  $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Abstraktní třída validátoru
 */
abstract class Form_Validator implements Form_Validator_Interface {
   protected $errMessage = null;

   /**
    * Proměná s výsledkem validátoru
    * @var boolean
    */
   protected $isValid = true;

   public function  __construct($errMessage = null) {
      $this->errMessage = $errMessage;
   }

   /**
    * Metoda vrací jestli je validátor validní
    * @return boolean
    */
   public function isValid() {
      return $this->isValid;
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {}

   /**
    * Metoda provede validaci formulářového prvku
    * @param Form_Element $elemObj -- prvek
    */
   public function validate(Form_Element $elemObj) {}

   /**
    * Metoda vrací objekt k chybovým hláškám modulů
    * @return Messages
    */
   public function errMsg() {
      return AppCore::getUserErrors();
   }

}
?>
