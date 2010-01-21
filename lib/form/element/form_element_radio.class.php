<?php
/**
 * Třída pro obsluhu INPUT prvku typu RADIO
 * Třída implementující objekt pro obsluhu INPUT prvkuu typu RADIO. Umožňuje kontrolu
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
class Form_Element_Radio extends Form_Element {

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
//   public function setMultiple($multiple = true) {
//      $this->isMultiple = $multiple;
//      return $this;
//   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll() {
      $group = null;
      $this->html()->setAttrib('name', $this->getName());
      $this->html()->setAttrib('type', 'radio');
      
      $first = true;
      foreach ($this->options as $optLabel => $optVal) {
         $opt = clone $this->html();
         $opt->setAttrib('id', $this->getName(),'_'.$optVal);
         $opt->setAttrib('value', $optVal);
         if(($this->values == $optVal) OR (empty ($this->values) AND $first == true)) {
            $opt->setAttrib('checked', 'checked');
         }
         $first = false;
         $group .= (string)$opt;
         $l = new Html_Element('label', $optLabel);
         $l->setAttrib('for', $this->getName(),'_'.$optVal);
         $group .= $l;
         $group .= new Html_Element('br');
      }
      return $group;
   }

   public function  __toString() {
      $str = $this->label();
      $str .= new Html_Element('br');
      $str .= $this->controll();
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
?>
