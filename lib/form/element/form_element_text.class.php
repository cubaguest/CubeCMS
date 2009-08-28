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
   /**
    * Metoda naplní prvek
    */
   public function populate($method = 'post') {
      switch ($method) {
         case 'get':
            $this->values = $_GET[$this->elementPrefix.$this->elementName];
            break;
         default:
            $this->values = $_POST[$this->elementPrefix.$this->elementName];
            break;
      }
      $this->isPopulated = true;

      // validace prvku
      foreach ($this->validators as $validator) {
         if(!$validator->validate($this)){
            $this->isValid = false;
         }
      }
   }
}
?>
