<?php
/**
 * Třída pro obsluhu prvku typu TEXTAREA
 * Třída implementující objekt pro obsluhu prvku typu TEXTAREA. Umožňuje kontrolu
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
class Form_Element_TextArea extends Form_Element_Text {
   protected function init() {
      $this->htmlElement = new Html_Element('textarea');
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function control($renderKey = null) {
      $rKey = $renderKey != null ? $renderKey : $this->renderedId;
      if(!$this->isValid AND $this->isPopulated) {
         $this->html()->addClass($this->cssClasses['error']);
         if(!self::$elementFocused){ $this->html()->setAttrib('autofocus','autofocus'); self::$elementFocused = true;}
      }

      $values = $this->getUnfilteredValues();
      $this->html()->addClass($this->getName()."_class");
      $cnt = null;
      if($this->isMultiLang()) {
         foreach ($this->getLangs() as $langKey => $langLabel) {
            $container = clone $this->containerElement;
            $container->addContent($this->html());
            $this->html()->clearContent();
            $this->html()->setAttrib('lang', $langKey);
            if($this->isDimensional()){
               $this->html()->setAttrib('name', $this->getName().'['.$this->dimensional.']['.$langKey.']');
               $this->html()->setAttrib('id', $this->getName().'_'.$rKey."_".$this->dimensional.'_'.$langKey);
               $container->setAttrib('id', $this->getName().'_'.$rKey."_".$this->dimensional.'_container_'.$langKey);
               $this->html()->addContent(htmlspecialchars($values[$this->dimensional][$langKey]));
            } else {
               $this->html()->setAttrib('name', $this->getName().'['.$langKey.']');
               $this->html()->setAttrib('id', $this->getName().'_'.$rKey.'_'.$langKey);
               $container->setAttrib('id', $this->getName().'_'.$rKey.'_container_'.$langKey);
               $this->html()->addContent(htmlspecialchars($values[$langKey]));
            }
            $container->addClass($this->cssClasses['elemContainer']);
            $container->setAttrib('lang', $langKey);
            $cnt .= $container;
         }
      } else {
         if($this->isDimensional()){
            $this->html()->setAttrib('name', $this->getName()."[".$this->dimensional."]");
            $this->html()->setAttrib('id', $this->getName().'_'.$rKey."_".$this->dimensional);
         } else {
            $this->html()->setAttrib('name', $this->getName());
            $this->html()->setAttrib('id', $this->getName().'_'.$rKey);
            $this->renderedId++;
         }
         $this->html()->clearContent(); // vymazání obsahu elementu jinak se duplikuje
         if(is_array($values) AND isset($values[$this->dimensional])) {
            $this->html()->addContent((string)$values[$this->dimensional]);
         } else {
            $this->html()->addContent((string)$values);
         }
         $cnt = $this->html();
      }
      if($renderKey == null){
         $this->renderedId++;
      }
      return $cnt;
   }
}
