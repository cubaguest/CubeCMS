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
   public function controll() {
      if(!$this->isValid AND $this->isPopulated) {
         $this->html()->addClass('formError');
      }

      $values = $this->getUnfilteredValues();
      $this->html()->addClass($this->getName()."_class");
      if($this->isMultiLang()) {
         $cnt = null;
         foreach ($this->getLangs() as $langKey => $langLabel) {
            $container = new Html_Element('p', $this->html());
            $this->html()->clearContent();
            $this->html()->setAttrib('lang', $langKey);
            if($this->isDimensional()){
               $this->html()->setAttrib('name', $this->getName().'['.$this->dimensional.']['.$langKey.']');
               $this->html()->setAttrib('id', $this->getName()."_".$this->dimensional.'_'.$langKey);
               $container->setAttrib('id', $this->getName()."_".$this->dimensional.'_container_'.$langKey);
//               $container->addClass($this->getName()."_".$this->dimensional."_container_class");
               $this->html()->addContent(htmlspecialchars($values[$this->dimensional][$langKey]));
            } else {
               $this->html()->setAttrib('name', $this->getName().'['.$langKey.']');
               $this->html()->setAttrib('id', $this->getName().'_'.$langKey);
               $container->setAttrib('id', $this->getName().'_container_'.$langKey);
               $this->html()->addContent(htmlspecialchars($values[$langKey]));
            }
            $container->addClass("elem_container_class");
            $container->setAttrib('lang', $langKey);

            $cnt .= $container;
         }
         return $cnt;
      } else {
         if($this->isDimensional()){
            $this->html()->setAttrib('name', $this->getName()."[".$this->dimensional."]");
            $this->html()->setAttrib('id', $this->getName()."_".$this->dimensional);
         } else {
            $this->html()->setAttrib('name', $this->getName());
            $this->html()->setAttrib('id', $this->getName());
         }
         $this->html()->clearContent(); // vymazání obsahu elementu jinak se duplikuje
         if(is_array($values) AND isset($values[$this->dimensional])) {
            $this->html()->addContent((string)$values[$this->dimensional]);
         } else {
            $this->html()->addContent((string)$values);
         }
      }
      return $this->html();
   }
}
?>
