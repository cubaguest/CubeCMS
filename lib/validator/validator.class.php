<?php
/**
 * Třída pro validaci prvků
 * Třída obsluhuje zakladní třídu pro tvorbu validátorů.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro validaci
 */

class Validator {
   /**
    * Proměná s výsledkem validátoru
    * @var boolean
    */
   protected $isValid = true;

   /**
    * Proměná s hodnotou
    * @var mixed
    */
   protected $values = null;

	/**
	 * Konstruktor nastaví základní parametry
	 */
	public function  __construct($values = null) {
      $this->setValues($values);
   }

   /**
    * Metoda nastaví hodnoty pro validaci
    * @param <type> $values
    * @return Validator
    */
   final public function setValues($values){
      $this->values = $values;
      return $this;
   }

   /**
    * Metoda vrací jestli je validátor validní
    * @return boolean
    */
   final public function isValid() {
      $this->validate();
      return $this->isValid;
   }

   /**
    * Metoda provede validaci
    */
   public function validate() {}
}
?>
