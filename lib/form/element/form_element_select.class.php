<?php
/**
 * Třída pro obsluhu prvku typu SELECT
 * Třída implementující objekt pro obsluhu INPUT prvkuu typu TEXT. Umožňuje kontrolu
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
class Form_Element_Select extends Form_Element {

/**
 * Pole s volbami hodnota=>popis
 * @var array
 */
   protected $options = array();

   /**
    * Jestli se dají v prvku vybírat více možností
    * @var boolean
    */
   protected $isMultiple = false;

   protected function init() {
      $this->htmlElement = new Html_Element('select');
      $this->htmlElementLabel = new Html_Element('label');
   }

   /**
    * Metoda nastaví volby
    * @param $options -- volby v poli hodnota=>popis
    * @return Form_Element_Select -- sám sebe
    */
   public function setOptions($options) {
      $this->options = $options;
      return $this;
   }

   /**
    * Metoda nastaví volbu že se dají vybírat více prvků
    * @param $multiple -- true pro povolení více voleb
    * @return Form_Element_Select -- sám sebe
    */
   public function setMultiple($multiple = true) {
      $this->isMultiple = $multiple;
      return $this;
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return Html_Element
    */
   public function controll() {
      $this->html()->clearContent();
      if($this->isDimensional()){
         if($this->isMultiple) {
            $this->html()->setAttrib('name', $this->getName()."[".$this->dimensional."][]");
         } else {
            $this->html()->setAttrib('name', $this->getName()."[".$this->dimensional."]");
         }
         $this->html()->setAttrib('id', $this->getName()."_".$this->dimensional);
      } else {
         $this->html()->setAttrib('id', $this->getName());
         if($this->isMultiple) {
            $this->html()->setAttrib('name', $this->getName().'[]');
         } else {
            $this->html()->setAttrib('name', $this->getName());
         }
         
      }
      
      if($this->isMultiple) {
         $this->html()->setAttrib('multiple', 'multiple');
      }
      $values = $this->getUnfilteredValues();
      foreach ($this->options as $optLabel => $optVal) {
         if(is_array($optVal)) {
            $opt = new Html_Element('optgroup');
            $opt->setAttrib('label', $optLabel);
            foreach ($optVal as $optLabel2 => $optVal2) {
               $optChild = new Html_Element('option', $optLabel2);
               $optChild->setAttrib('value', $optVal2);
               if($values == $optVal2 OR (is_array($values) AND in_array($optVal2, $values))) {
                  $optChild->setAttrib('selected', 'selected');
               }
               $opt->addContent($optChild);
            }
         } else {
            $opt = new Html_Element('option', $optLabel);
            $opt->setAttrib('value', $optVal);
            if($values == $optVal OR (is_array($values) AND in_array($optVal, $values))) {
               $opt->setAttrib('selected', 'selected');
            }
         }
         $this->html()->addContent($opt);
      }

      return $this->html();
   }
}
?>
