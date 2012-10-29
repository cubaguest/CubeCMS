<?php
/**
 * Třída pro obsluhu INPUT prvku typu HIDDEN pro vytvoření bezpečnostního tokenu
 * Třída implementující objekt pro obsluhu INPUT prvku typu HEDDEN s bezpečnostním tokenem. 
 * Slouží především pro ověření formuláře pro xsf útokům
 * chyby.
 *
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 6.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text
 */
class Form_Element_Token extends Form_Element_Hidden {
   private $token = null;

   public function __construct($name, $label = null, $prefix = null)
   {
      parent::__construct($name, $label, $prefix);
      $this->token = Token::getToken();
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function control($renderKey = null) {
      $this->setValues($this->token);
      return parent::control();
   }

   public function validate()
   {
      $this->isValidated = true;
      if(!Token::check($this->getValues())){
         $this->isValid = false;
      }
   }
   
}
?>
