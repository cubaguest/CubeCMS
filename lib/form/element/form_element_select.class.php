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
   
   protected $checkAllowedOptions = true;

   protected function init() {
      $this->htmlElement = new Html_Element('select');
      $this->htmlElementLabel = new Html_Element('label');
   }

   /**
    * Metoda nastaví volby
    * @param $options -- volby v poli hodnota=>popis
    * @param $merge -- jestli se mají nové volby připojit ke stávajícím volbám
    * @param $correctArray -- jestli se má provést korektura pole, pokud jsou prohozeny klíč x hodnota
    * @return Form_Element_Select -- sám sebe
    */
   public function setOptions($options, $merge = false, $correctArray = true) {
      reset($options);
      if($correctArray == true && is_string(reset($options)) && is_int(key($options))){
         $options = array_flip($options);
      }
      if($merge === true){
         $this->options = array_merge($this->options, $options);
         $this->options = array_unique($this->options);
      } else {
         $this->options = $options;
      }
      return $this;
   }
   
   public function validate()
   {
      if(!$this->isMultiple && !in_array("", $this->options)){
         $this->addValidation(new Form_Validator_NotEmpty());
      }
      // kontrola odeslaných hodnot jestli jsou v povolených volbách
      if($this->checkAllowedOptions){
         $this->addValidation(new Form_Validator_InArray($this->options, 
            $this->tr('Ve výběru "%s" byla odeslána hodnota "%s", která není v povolených hodnotách. Povolené hodnoty: ')."(".  implode(", ", array_keys($this->options) ).")"));
      }
      parent::validate();
   }

   /**
    * metoda přidá hodnotu do voleb
    * @param string/int $name -- název volby
    * @param string/int $value -- hodnota volby
    */
   public function addOption($name, $value){
      $this->options[$name] = $value;
   }

      /**
    * Metoda vrací volby
    * @return Array -- pole s volbami
    */
   public function getOptions() {
      return $this->options;
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
    * Metoda nastaví kontrolu odeslaných hodnot
    * @param $check -- true pro zapnutí kontroly
    * @return Form_Element_Select -- sám sebe
    */
   public function setCheckOptions($check = true) {
      $this->checkAllowedOptions = $check;
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
         $this->html()->setAttrib('id', $this->getName().'_'.$this->renderedId."_".$this->dimensional);
      } else {
         $this->html()->setAttrib('id', $this->getName().'_'.$this->renderedId);
         if($this->isMultiple) {
            $this->html()->setAttrib('name', $this->getName().'[]');
         } else {
            $this->html()->setAttrib('name', $this->getName());
         }
      }
      $this->renderedId++;

      if($this->isMultiple) {
         $this->setSubLabel(
            $this->tr('Více možností vyberete podržením klávesy "ctrl" při vybrěru.')."<br />"
            .$this->getSubLabel()
            );
         $this->html()->setAttrib('multiple', 'multiple');
      }
      $values = $this->getUnfilteredValues();
      foreach ($this->options as $optLabel => $optVal) {
         if(is_array($optVal)) {
            $opt = new Html_Element('optgroup');
            $opt->setAttrib('label', $optLabel);
            foreach ($optVal as $optLabel2 => $optVal2) {
               $optChild = new Html_Element('option', str_replace(' ', '&nbsp;', htmlspecialchars($optLabel2, ENT_QUOTES)));
               $optChild->setAttrib('value', (string)$optVal2);
               if($values == $optVal2 OR (is_array($values) AND in_array($optVal2, $values))) {
                  $optChild->setAttrib('selected', 'selected');
               }
               $opt->addContent($optChild);
            }
         } else {
            $opt = new Html_Element('option', str_replace(' ', '&nbsp;', htmlspecialchars($optLabel, ENT_QUOTES)));
            $opt->setAttrib('value', (string)$optVal);
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
