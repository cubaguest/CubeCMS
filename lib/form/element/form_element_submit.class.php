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
   protected function init() {
      $this->htmlElement = new Html_Element('input');
      $this->html()->setAttrib('type', 'submit');
//      $this->htmlElementLabel = new Html_Element('label');
   }

   /**
    * Metoda naplní prvek
    */
//   public function populate() {
//      if(isset ($_REQUEST[$this->getName()])){
//         $this->setValues($_REQUEST[$this->getName()]);
//      }
//      $this->isPopulated = true;
//   }

   /**
    * Metoda vrací jestli je potvrzovací tlačítko validní (odesláno)
    * @return boolean -- true pokud je odesláno
    */
//   public function isValid() {
//      return $this->isSend();
//   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll($renderKey = null) {
      
//      $this->html()->setAttrib('name', $this->getName());
      $this->setValues($this->getLabel());
//      return $this->html();
      return parent::controll($renderKey);
   }

   /**
    * Metoda vrací label
    * @return string
    */
   public function label($renderKey = null, $after = false) {
      return null;
   }

   public function  __toString() {
      return (string)$this->controll();
   }
}
?>
