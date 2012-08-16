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
class Form_Validator_Range extends Form_Validator implements Form_Validator_Interface {

   private $min = null;

   private $max = null;

   /**
    * Validátor čísla
    * @param string $errMsg -- chybová zpráva, která se má zobrazit
    * @param int $numberType -- typ čísla (konstanta TYPE_XXX nebo konstanta FILTER_VALIDATE_INT x FILTER_VALIDATE_FLOAT)
    * @param int/float $min -- minimální velikost čísla (pouze INT)
    * @param int/float $max -- maximální velikost čísla (pouze INT)
    */
   public function  __construct($min = 0, $max = 100, $errMsg = null) {
      if($errMsg == null) {
         parent::__construct($this->tr('Položka "%s" není ve správném číselném formátu'));
      } else {
         parent::__construct($errMsg);
      }
      $this->min = $min;
      $this->max = $max;
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      // sublabel
      if($element instanceof Form_Element_Text OR $element instanceof Form_Element_TextArea){
         $element->addValidationConditionLabel(sprintf($this->tr("číslo v rozsahu %s - %s"), $this->min, $this->max ) );
      }
   }

   public function validate(Form_Element $elemObj) {
      if($elemObj->getUnfilteredValues() == null) return true;// nekontrolujeme prázdý řetězec to je na validátoru notEmpty
      switch (get_class($elemObj)) {
      // input text
         case 'Form_Element_Text':
         case 'Form_Element_TextArea':
         case 'Form_Element_Password':
            $filterOptions = array('options' => array(
               'min_range' => $this->min,
               'max_range' => $this->max,
            ));
            if($elemObj->isDimensional() OR $elemObj->isMultiLang()) {

            } else {
               if (filter_var($elemObj->getUnfilteredValues(), $this->numberType, $filterOptions) === false) {
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
