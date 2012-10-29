<?php
/**
 * Třída pro obsluhu skupiny elemntů tlačítek a vybrání akce podle zadaného tlačítka
 *
 * @copyright  	Copyright (c) 2012 Jakub Matas
 * @version    	$Id: $ VVE 7.16 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu skupiny elementů submit
 */
class Form_Element_Multi extends Form_Element {
   /**
    * Pole s elementy
    * @var array
    */
   protected $elements = array();
   
   protected function init() {
      $this->htmlElement = new Html_Element('input');
      $this->html()->setAttrib('type', 'submit');
   }

   /**
    * Metoda vrací jestli byl element vůbec odeslán
    * @return bool
    */
   public function isSend() {
      foreach ($this->elements as $name => $element) {
         if( isset ($_REQUEST[$element->getName()]) ){
            return true;
         }
      }
      return false;
   }

   public function populate() {
      foreach ( $this->elements as $name => $element) {
         if(isset ($_REQUEST[$element->getName()])) {
            $element->populate();
            $this->values[$element->getName()] = $element->getValues();
         }
      }
      $this->unfilteredValues = $this->values;
      $this->isPopulated = true;
   }
   
   public function __get($name)
   {
      return isset($this->elements[$name]) ? $this->elements[$name] : null; 
   }
   
   public function setValues($values, $key = null)
   {
      // create elements
//       foreach ( $values as $act => $label) {
//          parent::setValues($values, $key);
//          $this->buttonsElements[$act] = clone $this->html();
//          $this->buttonsElements[$act]->setAttrib('value', $label);
//          $this->buttonsElements[$act]->addClass('button-action')->addClass('button-action-'.$act);
//       }
   }
   
   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function control($renderKey = null) {
      $rKey = $renderKey != null ? $renderKey : $this->renderedId;
      $str = null;
      foreach ($this->elements as $name => $element){
         // clear element
//          $button->clearClasses();
         
//          $button->setAttrib('name', $this->getName().'_'.$act);
//          $button->setAttrib('id', $this->getName().'_'.$act.'_'.$this->renderedId);
         
//          if(isset($this->confirmMessages[$act])){
//             $this->$act->setAttrib('onclick', 'return confirm(\''.$this->confirmMessages[$act].'\')');
//          }
         
         $str .= $element->control($rKey);
         if($element instanceof Form_Element_Checkbox){
            $str .= $element->label($rKey, true);
         }
      }
      if($renderKey == null){
         $this->renderedId++;
      }
      return (string)$str;
   }

   /**
    * Metoda vrací label
    * @return string
    */
   public function label($renderKey = null, $after = false) {
      // first element label
      if(!$this->haveElements()){ return null; }
      $f = reset($this->elements);
      return $f->label();
   }
   
   protected function haveElements() 
   {
      return !empty($this->elements);
   }
   
   public function setElements($arrayOfElements) 
   {
      $this->elements = $arrayOfElements;
   }
   
   public function addElement(Form_Element $element, $name = null) 
   {
      $this->elements[$name != null ? $name : $element->getName()] = $element;
      return $this;
   }
   
   public function removeElement($elementName) 
   {
      unset($this->elements[$elementName]);
      return $this;
   }
   
   
}
?>
