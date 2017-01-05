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
 * @version    	$Id: $ VVE 6.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text
 */
class Form_Element_Text extends Form_Element {

   /**
    *
    * @var string
    */
   protected $elemType = 'text';

   /**
   * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
   * @return string
   */
   public function control($renderKey = null) {
      $this->html()->setAttrib('type', $this->elemType);
      return parent::control($renderKey = null);
   }
   
   /**
   * Metoda nastaví typ prvku
   * @return string
   */
   public function setType($type = 'text') {
      $this->elemType = $type;
      return $this;
   }
}
