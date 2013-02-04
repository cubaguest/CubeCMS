<?php
/**
 * Třída pro obsluhu INPUT prvku typu RADIO
 * Třída implementující objekt pro obsluhu INPUT prvkuu typu RADIO. Umožňuje kontrolu
 * správnosti zadání,kódování/dekódování obsahu prvku, jazykovou obsluhu a jeho
 * vykreslení i s popisem v šabloně. Při špatném zadání se stará o zvýraznění
 * chyby.
 *
 *
 * @copyright     Copyright (c) 2008 Jakub Matas
 * @version       $Id: $ VVE 6.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text
 */
class Form_Element_Radio extends Form_Element_Select {

   protected $renderLabel = true;

   /**
    * Pole s volbami hodnota=>popis
    * @var array
    */
   protected $options = array();

   protected function init() {
      $this->htmlElement = new Html_Element('input');
      $this->htmlElementLabel = new Html_Element('label');
   }

   /**
    * Metoda nastaví volbu že se dají vybírat více prvků
    * @param $multiple -- true pro povolení více voleb
    * @return Form_Element_Select -- sám sebe
    */
   public function setMultiple($multiple = true) {
      $this->dimensional = $multiple;
      return $this;
   }

   /**
    * Metoda nastaví jestli se má vykreslit label u input prvku
    * @param bool $render
    */
   public function setRenderlabelInControl($render = true)
   {
      $this->renderLabel = $render;
   }

   /**
    * Metoda nastaví jestli se má vykreslit label u input prvku
    * @param bool $render
    */
   public function controlInRow()
   {

   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function control($renderKey = null) {
      $rKey = $renderKey != null ? $renderKey : $this->renderedId;
      $group = null;
      $this->html()->setAttrib('type', 'radio');

      $first = true;
      $i = 1;
      $numOpts = count($this->options);
      foreach ($this->options as $optLabel => $optVal) {
         $opt = clone $this->html();
         if($this->isMultiple()) {
            $opt->setAttrib('name', $this->getName()."[".$this->dimensional."]");
            $opt->setAttrib('id', $this->getName().'_'.$i.'_'.$rKey.'_'.$this->dimensional);
         } else {
            $opt->setAttrib('name', $this->getName());
            $opt->setAttrib('id', $this->getName().'_'.$i.'_'.$rKey);
         }
         if($optVal == false) $optVal = 0;
         $opt->setAttrib('value', (string)$optVal);
         if(($this->unfilteredValues !== false AND $this->unfilteredValues !== null AND $this->unfilteredValues == $optVal)
            OR ($this->unfilteredValues === null AND $first == true) ) {
            $opt->setAttrib('checked', 'checked');
         } else if(is_array($this->unfilteredValues) && in_array($optVal, $this->unfilteredValues)){
            $opt->setAttrib('checked', 'checked');
         }
         $first = false;
         $group .= (string)$opt;
         if($this->renderLabel){
            $l = new Html_Element('label', $optLabel);
            if($this->isMultiple()) {
               $l->setAttrib('for', $this->getName().'_'.$i.'_'.$rKey.'_'.$this->dimensional);
            } else {
               $l->setAttrib('for', $this->getName().'_'.$i.'_'.$rKey);
            }
            $group .= $l.($numOpts > 2 ? '<br />' : null);
         }
         $i++;
      }
      if($renderKey == null){
         $this->renderedId++;
      }
      return $group;
   }

   public function  __toString() {
      $str = $this->label();
      $str .= new Html_Element('br');
      $str .= $this->control();
      return $str;
   }

   /**
    * Metoda upraví vlastnost prvku u vykreslení
    * @param string $type -- typ parametru, který se má upravit
    * @param mixed $value -- hodnota parametru
    */
   public function setRender($type, $size = 30) {
      switch ($type) {
         case 'size':
            $this->html()->setAttrib('size', $size);
            break;
         default:
            break;
      }
   }
}
