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
class Form_Validator_FileSize extends Form_Validator {
   /**
    * Maximální velikost souboru v bajtech
    * @var int
    */
   private $fileSize = 0;

   public function  __construct($size, $errMsg = null) {
      $this->fileSize = $size;

      if($errMsg == null) {
         parent::__construct($this->tr('V položce "%s" byl odeslán soubor větší než %s'));
      } else {
         parent::__construct($errMsg);
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      if($element->isDimensional()){
         $element->addValidationConditionLabel(sprintf($this->tr("soubory s maximální velikostí %s"),  vve_create_size_str($this->fileSize)));
      } else {
         $element->addValidationConditionLabel(sprintf($this->tr("soubor s maximální velikostí %s"),  vve_create_size_str($this->fileSize)));
      }
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
                  if($file['size'] > ($this->fileSize)) {
                     $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel(), vve_create_size_str($this->fileSize)));
                     return false;
                  }
               }
            } else {
               if($values['size'] > ($this->fileSize)) {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel(), vve_create_size_str($this->fileSize)));
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
