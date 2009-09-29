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
      
      
      // tady bude if při multilang
      if($this->isMultiLang()){
         $cnt = null;
         foreach ($this->getLangs() as $langKey => $langLabel) {
            $this->html()->setAttrib('name', $this->getName().'['.$langKey.']');
            $this->html()->setAttrib('id', $this->getName().'['.$langKey.']');
            $cnt .= $this->html();
         }
         // script pro vybrání jazyka
         $script = new Html_Element('script');
         $script->setAttrib('type', "text/javascript");
         $script->addContent('formSwitchLang("'.$this->getName()."[".Locale::getLang().']");');


         return $cnt.$script;
      } else {
         $this->html()->setAttrib('id', $this->getName());
         $this->html()->setAttrib('name', $this->getName());
         $this->html()->addContent($this->getValues());
      }


      return $this->html();
   }

   /**
    * Metoda vrací popisek k prvku (html element label)
    * @return string
    */
   public function label() {
      $this->htmlLabel()->addContent($this->formElementLabel);
      if(!$this->isValid AND $this->isPopulated) {
         $this->htmlLabel()->addClass('formErrorLabel');
         $this->html()->addClass('formError');
      }

      if($this->isMultilang()){
         Template::addJS('./jscripts/formswitchlangs.js');

         $cnt = $langButtons = null;
         foreach ($this->getLangs() as $langKey => $langLabel) {
            $this->htmlLabel()->clearContent();
            $this->htmlLabel()->addContent($this->formElementLabel);
            $this->htmlLabel()->setAttrib('for', $this->getName().'['.$langKey.']');
            $this->htmlLabel()->setAttrib('id', "label_".$this->getName().'['.$langKey.']');

            $a = new Html_Element('a', $langLabel);
            $a->setAttrib('href', "#");
            $a->setAttrib('title', $langLabel);
            $a->setAttrib('onclick', "return formSwitchLang('".$this->getName()."[".$langKey."]');");
            $langButtons .= $a.(new Html_Element('br'));

            $cnt .= $this->htmlLabel().'<br />';
         }
         return $cnt.(new Html_Element('br')).$langButtons;
      } else {
         $this->htmlLabel()->setAttrib('for', $this->getName());
      }

      return (string)$this->htmlLabel();
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

   /**
    * Metoda vyrenderuje celý element i s popiskem
    * @param string $type -- typ renderu (table,null,...)
    */
//   public function render($type = "table") {
//      $string = null;
//      switch ($type) {
//         case 'table':
//         default:
//            $tr1 = new Html_Element('tr');
//            $td1 = new Html_Element('th');
//            $td1->setAttrib('colspan', 2);
//            $td1->addContent($this->label());
//            $tr1->addContent($td1);
//
//            // kontrolní element
//            $td2 = new Html_Element('td');
//            $td2->setAttrib('colspan', 2);
//            $td2->addContent($this->controll());
//            // popisky k validátorům
//            $td2->addContent($this->labelValidations());
//
//            $tr2 = new Html_Element('tr', $td2);
//
//            $string = $tr1.$tr2;
//            break;
//      }
//      return (string)$string;
//   }
}
?>
