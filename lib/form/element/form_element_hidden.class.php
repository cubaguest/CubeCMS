<?php
/**
 * Třída pro obsluhu INPUT prvku typu HIDDEN
 * Třída implementující objekt pro obsluhu INPUT prvkuu typu HEDDEN. Umožňuje kontrolu
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
class Form_Element_Hidden extends Form_Element {
   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function control($renderKey = null) {
      $this->html()->setAttrib('type', 'hidden');
      return parent::control($renderKey);
   }

   /**
    * Metoda vrací popisek k prvku (html element label)
    * @return string
    */
   public function label($renderKey = null, $after = false) {
      return (string)null;
   }

   /**
    * Metoda vrací subpopisek
    * @return string -- řetězec z html elementu
    */
   public function subLabel($renderKey = null) {
      return (string)null;
   }
   
   public function labelValidations($renderKey = null) {
      return (string)null;
   }
}
?>
