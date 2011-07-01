<?php
/**
 * Třída pro obsluhu INPUT prvku typu CHECKBOX
 * Třída implementující objekt pro obsluhu INPUT prvkuu typu CHECKBOX. Umožňuje kontrolu
 * správnosti zadání,kódování/dekódování obsahu prvku, jazykovou obsluhu a jeho
 * vykreslení i s popisem v šabloně. Při špatném zadání se stará o zvýraznění
 * chyby.
 *
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 6.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text
 */
class Form_Element_Checkbox extends Form_Element {
   protected function init() {
      $this->htmlElement = new Html_Element('input');
      $this->htmlElementLabel = new Html_Element('label');
   }

   /**
    * Metoda naplní element
    */
   public function populate() {
      parent::populate();
      if(is_array($this->values)){
         $this->values = filter_var_array($this->values, FILTER_VALIDATE_BOOLEAN);
      } else {
         $this->values = filter_var($this->values, FILTER_VALIDATE_BOOLEAN);
      }
      $this->unfilteredValues = $this->values;
   }

   /**
    * Metoda vrací hodnotu prvku
    * @param $key -- (option) klíč hodnoty (pokud je pole)
    * @return mixed -- hodnota prvku
    */
   public function getValues($key = null) {
      if($key == null){
         return $this->unfilteredValues;
      } else if(isset ($this->unfilteredValues[$key])) {
         return $this->unfilteredValues[$key];
      }
      return false;
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll() {
      $values = $this->getUnfilteredValues();
      if($this->isDimensional()) {
         $this->html()->setAttrib('name', $this->getName()."[".$this->dimensional."]");
         $this->html()->setAttrib('id', $this->getName().'_'.$this->renderedId."_".$this->dimensional);
         if($values === true || (isset ($values[$this->dimensional]) && $values[$this->dimensional] === true)) {
            $this->html()->setAttrib('checked', 'checked');
         } else {
            $this->html()->removeAttrib('checked');
         }
      } else {
         $this->html()->setAttrib('name', $this->getName());
         $this->html()->setAttrib('id', $this->getName().'_'.$this->renderedId);
         if($values == true) {
            $this->html()->setAttrib('checked', 'checked');
         }
      }


      $this->html()->setAttrib('type', 'checkbox');
      if(!is_array($values) AND !empty ($values)) {
         $this->html()->setAttrib('value', $values);
      }
      

      $l = new Html_Element('label', $this->getLabel());
      if($this->isDimensional()) {
         $l->setAttrib('for', $this->getName().'_'.$this->renderedId."_".$this->dimensional);
      } else {
         $l->setAttrib('for', $this->getName().'_'.$this->renderedId);
      }
      $this->renderedId++;
      return $this->html();
   }
}
?>
