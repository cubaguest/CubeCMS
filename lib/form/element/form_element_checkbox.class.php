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
   public function populate($method = 'post'){
      parent::populate($method);
      if($this->values == null){
         $this->values = false;
      }
   }

   /**
    * Metoda nastaví volbu že se dají vybírat více prvků
    * @param $multiple -- true pro povolení více voleb
    * @return Form_Element_Select -- sám sebe
    */
//   public function setMultiple($multiple = true) {
//      $this->isMultiple = $multiple;
//      return $this;
//   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll() {
      $this->html()->setAttrib('name', $this->getName());
      $this->html()->setAttrib('type', 'checkbox');
      if(!empty ($this->values)){
         $this->html()->setAttrib('value', $this->values);
      }
      if($this->values == true){
         $this->html()->setAttrib('checked', 'checked');
      }

      $l = new Html_Element('label', $this->getLabel());
      $l->setAttrib('for', $this->getName());

      return $this->html();

//      $first = true;
//      foreach ($this->options as $optLabel => $optVal) {
//         $opt = clone $this->html();
//         $opt->setAttrib('id', $this->getName(),'_'.$optVal);
//         $opt->setAttrib('value', $optVal);
//         if(($this->values == $optVal) OR (empty ($this->values) AND $first == true)) {
//            $opt->setAttrib('checked', 'checked');
//         }
//         $first = false;
//         $group .= (string)$opt;
//         $l = new Html_Element('label', $optLabel);
//         $l->setAttrib('for', $this->getName(),'_'.$optVal);
//         $group .= $l;
//         $group .= new Html_Element('br');
//      }
//      return $group;
   }

   /**
    * Metoda vrací popisek k prvku (html element label)
    * @return string
    */
//   public function label() {
//      return (string)null;
//   }
}
?>
