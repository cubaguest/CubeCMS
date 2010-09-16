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
class Form_Validator_FileExtension extends Form_Validator implements Form_Validator_Interface {
   /**
    * Pole s povolenými typ souborů
    * @var array
    */
   private $extensions = array();

   public function  __construct($extensions, $errMsg = null) {
      if(!is_array($extensions)){
         $extensions = str_replace(' ', null, $extensions);
         $extensions = explode(';', $extensions);
      }
      $this->extensions = $extensions;

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
      foreach ($this->extensions as $type) {
         $mimes .= $type.", ";
      }
      $mimes = substr($mimes, 0, strlen($mimes)-2);

      $element->addValidationConditionLabel(sprintf(_("soubor s příponou %s"),$mimes));
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
               foreach ($values as $file){
                  if(!in_array($file['extension'], $this->extensions)) {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
                  $this->isValid = false;
                  return false;
               }
               }
            } else {
               if(!in_array($values['extension'], $this->extensions)) {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
                  $this->isValid = false;
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
