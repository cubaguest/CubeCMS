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
/**
 * jestli je element potvrzen
 * @var boolean
 */
   protected $isSubmited = false;

   protected function init() {
      $this->htmlElement = new Html_Element('input');
//      $this->htmlElementLabel = new Html_Element('label');
   }

   /**
    * Metoda naplní prvek
    */
   public function populate($method = 'post') {
      switch ($method) {
         case 'get':
            if(isset ($_GET[$this->getName()])) {
               $this->isSubmited = true;
            }
            break;
         default:
            if(isset ($_POST[$this->getName()])) {
               $this->isSubmited = true;
            }
            break;
      }
      $this->isPopulated = true;
   }

   /**
    * Metoda vrací jestli je potvrzovací tlačítko validní (odesláno)
    * @return boolean -- true pokud je odesláno
    */
   public function isValid() {
      return $this->isSubmited;
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll() {
      $this->html()->setAttrib('name', $this->getName());
      $this->html()->setAttrib('type', 'submit');
      $this->html()->setAttrib('value', $this->getLabel());
      return $this->html();
   }

   /**
    * Metoda vrací label
    * @return string
    */
   public function label() {
      return null;
   }

   public function  __toString() {
      return (string)$this->controll();
   }
}
?>
