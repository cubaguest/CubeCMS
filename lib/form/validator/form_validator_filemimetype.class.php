<?php
/**
 * Třída formulářového validátoru pro mime typ souboru
 *
 * @copyright  	Copyright (c) 2010 Jakub Matas
 * @version    	$Id: $ VVE 6.4 $Revision:  $
 * @author        $Author:  $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída validátoru souboru podle mime typu
 * @todo          Dořešit co se správně uploadovanými soubory
 */
class Form_Validator_FileMimeType extends Form_Validator {
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
         parent::__construct($this->tr('V položce "%s" nebyl odeslán povolený typ souboru'));
      } else {
         parent::__construct($errMsg);
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      $element->html()->setAttrib('accept', implode(', ', $this->mimeTypes));
      $element->addValidationConditionLabel(sprintf($this->tr("soubor typu %s"),  implode(', ', $this->mimeTypes)));
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
                  if(!in_array($file['type'], $this->mimeTypes)) {
                     $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
                     return false;
                  }
               }
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
