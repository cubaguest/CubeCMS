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
class Form_Validator_FileSize extends Form_Validator implements Form_Validator_Interface {
   /**
    * Maximální velikost souboru v bajtech
    * @var int
    */
   private $fileSize = 0;

   public function  __construct($size, $errMsg = null) {
      $this->fileSize = $size;

      if($errMsg == null) {
         parent::__construct(_("V položce \"%s\" byl odeslán soubor větší než %sKB"));
      } else {
         parent::__construct($errMsg);
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      $element->addValidationConditionLabel(sprintf(_("soubor maximální velikostí %sKB"),$this->fileSize));
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
               if($values['size'] > ($this->fileSize*1024)) {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel(), $this->fileSize));
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
