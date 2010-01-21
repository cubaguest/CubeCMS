<?php
/**
 * Třída pro obsluhu INPUT prvku typu PASSWORD
 * Třída implementující objekt pro obsluhu INPUT prvkuu typu PASSWORD. Umožňuje kontrolu
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
class Form_Element_Password extends Form_Element {
   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll() {
      $this->html()->setAttrib('type', 'password');
      return parent::controll();
   }
}
?>
