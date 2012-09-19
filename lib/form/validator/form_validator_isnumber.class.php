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
class Form_Validator_IsNumber extends Form_Validator implements Form_Validator_Interface {

   const TYPE_INT = FILTER_VALIDATE_INT;
   const TYPE_FLOAT = FILTER_VALIDATE_FLOAT;

   /**
 * O jaký druh čísla se jedná
 * @var string
 */
   private $numberType = self::TYPE_INT;

   private $min = null;

   private $max = null;

   /**
    * Validátor čísla
    * @param string $errMsg -- chybová zpráva, která se má zobrazit
    * @param int $numberType -- typ čísla (konstanta TYPE_XXX nebo konstanta FILTER_VALIDATE_INT x FILTER_VALIDATE_FLOAT)
    * @param int/float $min -- minimální velikost čísla (pouze INT)
    * @param int/float $max -- maximální velikost čísla (pouze INT)
    */
   public function  __construct($numberType = self::TYPE_INT, $min = null, $max = null ,$errMsg = null ) {
      if($numberType == 'float') {$numberType = FILTER_VALIDATE_FLOAT;}
      else if($numberType == 'int') {$numberType = FILTER_VALIDATE_INT;}
      if($errMsg == null) {
         parent::__construct($this->tr('Položka "%s" není ve správném číselném formátu'));
      } else {
         parent::__construct($errMsg);
      }
      $this->numberType = $numberType;
      $this->min = $min;
      $this->max = $max;
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      $addStr = null;
      if($this->numberType == FILTER_VALIDATE_INT AND $this->min !== null AND $this->max !== null){
         $addStr = sprintf($this->tr(' mezi %s a %s'), $this->min, $this->max);
      } else if($this->numberType == FILTER_VALIDATE_INT AND $this->min !== null){
         $addStr = sprintf($this->tr(' větší než %s'), $this->min);
      } else if($this->numberType == FILTER_VALIDATE_INT AND $this->max !== null){
         $addStr = sprintf($this->tr(' menší než %s'), $this->max);
      }
      // sublabel
      if($element instanceof Form_Element_Text OR $element instanceof Form_Element_TextArea){
         if($this->numberType == FILTER_VALIDATE_FLOAT) {
            $element->addValidationConditionLabel($this->tr("desetiné číslo").$addStr);
         } else {
            $element->addValidationConditionLabel($this->tr("celé číslo").$addStr);
         }
      }
   }

   public function validate(Form_Element $elemObj) {
      if($elemObj->getUnfilteredValues() == null) return true;// nekontrolujeme prázdý řetězec to je na validátoru notEmpty
      switch (get_class($elemObj)) {
      // input text
         case 'Form_Element_Text':
         case 'Form_Element_TextArea':
         case 'Form_Element_Password':
            $filterOptions = array('options' => array());
            if($this->min !== null) $filterOptions['options']['min_range'] = $this->min;
            if($this->max !== null) $filterOptions['options']['max_range'] = $this->max;

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
