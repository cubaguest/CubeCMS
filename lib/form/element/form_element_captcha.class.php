<?php
/**
 * Třída pro obsluhu INPUT prvku typu TEXT s captchou
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 6.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text
 */
class Form_Element_Captcha extends Form_Element_Text {
   
   public function __construct($name, $label = null, $prefix = null)
   {
      if($label == null){
         $label = $this->tr('Ověření');
      }
      parent::__construct($name, $label, $prefix);
      $this->addValidation(new Form_Validator_NotEmpty($this->tr('Ověření formuláře nebylo vylpněno')));
      $this->addValidationConditionLabel($this->tr('Opište červené znaky'));
   }
   
   public function validate()
   {
      parent::validate();
      // validace captchy
      if($this->isValid){
         $capctcha = new Component_Captcha();
         if($capctcha->validate($this->getUnfilteredValues()) == false){
            $this->isValid = false;
            $this->errMsg()->addMessage($this->tr('Kontrolní kód nebyl správně opsán'));
         }
      }
   }

   /**
 * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
 * @return string
 */
   public function control($renderKey = null) {
      $rKey = $renderKey != null ? $renderKey : $this->renderedId;
      $this->html()->setAttrib('type', 'text');
      $this->createValidationLabels();
      $this->html()->clearContent();
      if(!$this->isValid AND $this->isPopulated) {
         $this->html()->addClass(self::$cssClasses['error']);
         if(!self::$elementFocused){ $this->html()->setAttrib('autofocus','autofocus'); self::$elementFocused = true;}
      }
      $this->html()->addClass($this->getName()."_class")->addClass('captcha');
      $this->html()->setAttrib('name', $this->getName());
      $this->html()->setAttrib('id', $this->getName().'_'.$rKey);
      $this->html()->setAttrib('value', null);
      
      // image
      $image = new Html_Element('img');
      $image->setAttrib('src', Component_Captcha::getImage())
         ->setAttrib('alt', 'captcha')
         ->addClass('captcha');
      
      if($renderKey == null){
         $this->renderedId++;
      }
      return $this->html().$image;
   }
}
?>
