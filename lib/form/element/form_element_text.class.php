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
class Form_Element_Text extends Form_Element {

   protected function init() {
      $this->htmlElement = new Html_Element('input');
      $this->htmlElementLabel = new Html_Element('label');
   }

   /**
    * Metoda naplní prvek
    */
//   public function populate($method = 'post') {
//      switch ($method) {
//         case 'get':
//            $this->values = $_GET[$this->formElementPrefix.$this->elementName];
//            break;
//         default:
//            $this->values = $_POST[$this->formElementPrefix.$this->elementName];
//            break;
//      }
//      $this->isPopulated = true;
//
//      // validace prvku
//      foreach ($this->validators as $validator) {
//         if(!$validator->validate($this)){
//            $this->isValid = false;
//         }
//      }
//   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll() {
      $this->html()->setAttrib('name', $this->getName());
      $this->html()->setAttrib('type', 'text');
      $this->html()->setAttrib('id', $this->getName());
      $this->html()->setAttrib('value', '');
      return $this->html();
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
