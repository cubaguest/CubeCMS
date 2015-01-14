<?php
/**
 * Třída pro obsluhu INPUT prvku typu CHECKBOX
 * Třída implementující objekt pro obsluhu INPUT prvkuu typu CHECKBOX. Umožňuje kontrolu
 * správnosti zadání,kódování/dekódování obsahu prvku, jazykovou obsluhu a jeho
 * vykreslení i s popisem v šabloně. Při špatném zadání se stará o zvýraznění
 * chyby.
 *
 *
 * @copyright     Copyright (c) 2013 Jakub Matas
 * @version       $Id: $ VVE 7.10 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text
 */
class Form_Element_Multi_Checkbox extends Form_Element_Checkbox {
   protected $options = array();

   public $renderCols = 2;

   protected function init() {
      parent::init();
      $this->setMultiple(true);
      $this->cssClasses['wrapperClass'] = 'checkbox-group';
   }

   /**
    * Metoda nastaví všechny hodnoty
    * @param $options
    * @param bool $merge
    */
   public function setOptions($options, $merge = false)
   {
      $this->options = $merge ? array_merge($this->options, $options) : $options;
   }
   
   /**
    * metoda přidá hodnotu do voleb
    * @param string/int $name -- název volby
    * @param string/int $value -- hodnota volby
    */
   public function addOption($name, $value){
      $this->options[(string)$name] = $value;
   }

   /**
    * Metoda nastaví počet sloupců do kterých se budou elementy vykreslovat
    * @param int $cols -- počet sloupců
    * @return Form_Element_Multi_Checkbox
    */
   public function setRenderedCols($cols = 2)
   {
      $this->renderCols = $cols;
      return $this;
   }

   /**
    * Metoda naplní element
    */
   public function populate() {
      parent::populate();
      foreach($this->options as $key => $val) {
         if(!isset($this->values[$key])){
            $this->values[$key] = false;
         }
      }
      $this->unfilteredValues = $this->values;
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @param int $renderKey
    * @return string
    */
   public function control($renderKey = null) {
      $rKey = $renderKey != null ? $renderKey : $this->renderedId;
      $values = $this->getUnfilteredValues();
      $this->html()
          ->addClass($this->getName()."_class");
      

      // rozdělení na dva sloupce
      $opts = count($this->options);
      
      $rows = ceil($opts/$this->renderCols);
      
      $elements = array();
      foreach($this->options as $optKey => $optName){
         $this->html()->setAttrib('name', $this->getName()."[".$optKey."]");
         $this->html()->setAttrib('id', $this->getName().'_'.$rKey."_".$optKey);

         if($values === true || (isset ($values[$optKey]) && $values[$optKey] === true)) {
            $this->html()->setAttrib('checked', 'checked');
         } else {
            $this->html()->removeAttrib('checked');
         }

         $this->html()->setAttrib('type', 'checkbox');

         $l = new Html_Element('label', $optName);
         $l->setAttrib('for', $this->getName().'_'.$rKey."_".$optKey);

         if($renderKey == null){
            $this->renderedId++;
         }
         $wrapper = clone $this->containerElement;
         $wrapper
             ->addClass('checkbox');
         $wrapper->addContent($this->html().$l);
         $elements[] = $wrapper;
      }
      $parts = array_chunk($elements, $rows, true);

      $ret = '<table class="multicheckbox multicheckbox_col_'.$this->renderCols.' '.$this->cssClasses['wrapperClass'].'"><tr>';

      foreach($parts as $part) {
         $ret .= '<td>'.implode('', $part).'</td>';
      }

      $ret .= '</tr></table>';
      return $ret;
   }
}
