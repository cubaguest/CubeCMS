<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of form_validate_noemptyclass
 *
 * @author jakub
 */
class Form_Validator_FileMimeType extends Form_Validator implements Form_Validator_Interface {
   /**
    * Pole s povolenými typ souborů
    * @var array
    */
   private $mimeTypes = array();

   public function  __construct($mimeTypes, $errMsg = null) {
      if(!is_array($mimeTypes)){
         $mimeTypes = array($mimeTypes);
      }
      $this->mimeTypes = $mimeTypes;

      if($errMsg == null) {
         parent::__construct(_("V položce \"%s\" nebyl odeslán povolený typ souboru"));
      } else {
         parent::__construct($errMsg);
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      $mimes = null;
      foreach ($this->mimeTypes as $type) {
         $mimes .= $type.", ";
      }
      $mimes = substr($mimes, 0, strlen($mimes)-2);

      $element->addValidationConditionLabel(sprintf(_("soubor typu %s"),$mimes));
   }

   public function validate(Form_Element $elemObj) {
      $values = $elemObj->getUnfilteredValues();
      if(empty($values)){
         return true;
      }
      switch (get_class($elemObj)) {
         // input text
         case 'Form_Element_File':
            if($elemObj->isDimensional() OR $elemObj->isMultiLang()) {

            } else {
               if(!in_array($values['type'], $this->mimeTypes)) {
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
