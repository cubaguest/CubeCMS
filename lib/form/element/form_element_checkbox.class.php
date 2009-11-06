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
    * @param string $method -- typ metody přes kterou je prvek odeslán (POST|GET)
    */
   public function populate($method = 'post') {
      parent::populate($method);
//      $this->checkValRecurs($this->values);
      if($this->values == null){
         $this->values = false;
      }
   }
   /**
    * @todo dořešit
    * @param <type> $arr
    */
   private function checkValRecurs(&$arr) {
      if(is_array($arr)){
         foreach ($arr as &$var) {
            $this->checkValRecurs($var);
         }
      } else {
         if($arr == 'on'){
            $arr = true;
         }
      }
   }

   /**
    * Metoda vrací hodnotu prvku
    * @param $key -- (option) klíč hodnoty (pokud je pole)
    * @return mixed -- hodnota prvku
    */
   public function getValues($key = null) {
      if($key !== null AND isset($this->values[$key])){
         return true;
      }
//      else if($key !== null AND is_array($this->values) AND !isset($this->values[$key])) {
//         return false;
//      }
      return false;
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll() {
      if($this->isDimensional()) {
         $this->html()->setAttrib('name', $this->getName()."[".$this->dimensional."]");
         $this->html()->setAttrib('id', $this->getName()."_".$this->dimensional);
      } else {
         $this->html()->setAttrib('name', $this->getName());
         $this->html()->setAttrib('id', $this->getName());
      }

      $this->html()->setAttrib('type', 'checkbox');
      if(!empty ($this->values)) {
         $this->html()->setAttrib('value', $this->values);
      }
      if($this->values == true) {
         $this->html()->setAttrib('checked', 'checked');
      }

      $l = new Html_Element('label', $this->getLabel());
      if($this->isDimensional()) {
         $l->setAttrib('for', $this->getName()."_".$this->dimensional);
      } else {
         $l->setAttrib('for', $this->getName());
      }

      return $this->html();
   }
}
?>
