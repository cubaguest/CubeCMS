<?php
/**
 * Třída pro obsluhu INPUT prvku typu TEXT
 * Třída implementující objekt pro obsluhu INPUT prvkuu typu TEXT. Umožňuje kontrolu
 * správnosti zadání,kódování/dekódování obsahu prvku, jazykovou obsluhu a jeho
 * vykreslení i s popisem v šabloně. Při špatném zadání se stará o zvýraznění
 * chyby.
 *
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 5.1.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text
 */
class Form_Element_Submit extends Form_Element implements Form_Element_Interface {
   
   protected $prefixIcon = null;
   protected $sufixIcon = null;

   protected function init() {
      $this->htmlElement = new Html_Element('button');
      $this->html()->setAttrib('type', 'submit');
   }
   
   public function setSufixIcon($icon)
   {
      $this->sufixIcon = $icon;
   }
   
   public function setPrefixIcon($icon)
   {
      $this->prefixIcon = $icon;
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function control($renderKey = null) {
      
      $this->setValues($this->getLabel());
      $cnt = null;
      if($this->prefixIcon != null){
         $cnt .= '<span class="icon icon-'.$this->prefixIcon.'"></span> ';
      }
      $cnt .= (string)$this->getLabel();
      if($this->sufixIcon != null){
         $cnt .= ' <span class="icon icon-'.$this->sufixIcon.'"></span>';
      }
      $this->html()->setContent($cnt);
      
      $this->html()->setAttrib('name', $this->getName());
      $this->html()->setAttrib('id', $this->getName().'_'.$this->renderedId);
      $this->html()->addClass($this->getName()."_class");
      $this->html()->setAttrib('value', htmlspecialchars((string)$this->getLabel()));
      if($renderKey == null){
         $this->renderedId++;
      }
      return $this->html();
//      return parent::control($renderKey);
   }

   /**
    * Metoda vrací label
    * @return string
    */
   public function label($renderKey = null, $after = false) {
      return null;
   }

   public function  __toString() {
      return (string)$this->control();
   }
}
?>
