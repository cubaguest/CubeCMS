<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 * impelemntuje:
 * images
 * texts
 * applications
 * archives
 * flashes
 *
 *
 */

/**
 * Description of form_validate_noemptyclass
 *
 * @author jakub
 */
class Form_Validator_FileType extends Form_Validator implements Form_Validator_Interface {
   /**
    * Pole s povolenými typ souborů
    * @var array
    */
   private $types = array();

   public function  __construct($types, $errMsg = null) {
      if(!is_array($types)){
         $types = array($types);
      }
      $this->types = $types;

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
      foreach ($this->types as $type) {
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
               if(!in_array($values['type'], $this->types)) {
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
