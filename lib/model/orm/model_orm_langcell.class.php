<?php
/**
 * Abstraktní třída pro Db Model typu PDO.
 * Tříta pro vytvoření modelu, přistupujícího k databázi. Umožňuje základní práce
 * s databází.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: dbmodel.class.php 615 2009-06-09 13:05:12Z jakub $ VVE3.9.2 $Revision: 615 $
 * @author			$Author: jakub $ $Date: 2009-06-09 15:05:12 +0200 (Út, 09 čen 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-06-09 15:05:12 +0200 (Út, 09 čen 2009) $
 * @abstract 		Abstraktní třída pro vytvoření modelu pro práci s databází
 */

class Model_ORM_LangCell implements ArrayAccess, Countable, Iterator {
   /**
 * Pole s prvky objektu
 * @var array
 */
   private $values = array();

   /**
    * Konstruktor
    */
   function  __construct($values = false) {
      if(is_array($values) && !empty ($values)){
         $this->values = $values;
      } else {
         $this->values = array_fill_keys(Locales::getAppLangs(), null);
      }
   }

   /**
    * Magická metoda která nastavuje prvek objektu
    * @param string $name -- název prvku
    * @param mixed $value -- hodnota prvku
    */
   public function  __set($name,  $value) {
      $this->values[$name] = $value;
   }

   /**
    * Metoda vrací název proměné objektu při přímém přístupu
    * @param string $name -- název proměnné
    * @return mixed
    */
   public function  &__get($name) {
      return $this->values[$name];
   }

   /**
    * Metoda zjišťuje jestli daný prvek existuje
    * @param string $name -- název prvku
    * @return boolena
    */
   public function  __isset($name) {
      return isset ($this->values[$name]);
   }

   /**
    * Metoda odstraní zadaný prvek
    * @param string $name -- název prvku
    */
   public function  __unset($name) {
      unset ($this->values[$name]);
   }

   /**
    * Metoda pro nastavení hodnoty prvku při přístupu přes pole
    * @param string $offset -- název proměnné
    * @param mixed $value -- hodnota prvku
    */
   public function offsetSet($offset, $value) {
      $this->__set($offset, $value);
   }

   /**
    * Metoda zjišťuje existenci prvku při přístupu přes pole
    * @param string $offset -- název proměnné
    * @return boolean
    */
   public function offsetExists($offset) {
      return isset($this->values[$offset]);
   }

   /**
    * Metoda odstranění prvku při přístupu přes pole
    * @param string $offset -- název prvku
    */
   public function offsetUnset($offset) {
      unset($this->container[$offset]);
   }

   /**
    * Metoda pro vrácení hodnoty prvku při přístupu přes pole
    * @param string $offset -- název proměnné
    * @return mixed -- hodnota prvku
    */
   public function offsetGet($offset) {
      return isset($this->values[$offset]) ? $this->values[$offset] : null;
   }

   /**
    * Metoda přidá k jazyku hodnotu
    * @param string $lang -- jazyk
    * @param mixed $value -- hodnota
    */
   public function addValue($lang, $value) {
      $this->values[$lang] = $value;
   }

   /**
    * Metoda pro počítání prvků
    * @return int -- počet prvků
    */
   public function count() {
      return count($this->values);
   }

   /**
    * Magická metoda vrací proměnnou jako řetězec podle zvoleného jazyka
    * @return string -- řetězec
    */
   public function  __toString() {
      if(isset ($this->values[Locales::getLang()]) && gettype($this->values[Locales::getLang()]) == 'string'
        && $this->values[Locales::getLang()] != ''|null) {
         return $this->values[Locales::getLang()];
      }
      // vrací se výchozí jazyk -- není protože jazyk může být vybrán jenom jeden
//      else if(gettype($this->values[Locales::getDefaultLang()]) == 'string'
//        AND $this->values[Locales::getDefaultLang()] != ''|null) {
//         return $this->values[Locales::getDefaultLang()];
//      }
      else {
         return (string)null;
      }
   }

   /**
    * Přesun ukazettele na tačátek pole
    * @return string
    */
   public function rewind() {
      return reset($this->values);
   }

   /**
    * Vrací aktuální prvek
    * @return string
    */
   public function current() {
      return current($this->values);
   }

   /**
    * Vrací jazykový název aktuálního prvku
    * @return string
    */
   public function key() {
      return key($this->values);
   }

   /**
    * Vrací následující prvek
    * @return string
    */
   public function next() {
      return next($this->values);
   }

   /**
    * Testuje jestli je prvek validní
    * @return bool
    */
   public function valid() {
      return key($this->values) !== null;
   }

   public function getArray()
   {
      return $this->values;
   }
}
