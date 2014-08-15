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
      $this->values = array_fill_keys(Locales::getAppLangs(), null);
      if(is_array($values) && !empty ($values)){
         $this->values = array_merge($this->values, $values);
      } 
   }

   /**
    * Magická metoda která nastavuje prvek objektu
    * @param string $name -- název prvku
    * @param mixed $value -- hodnota prvku
    */
   public function  __set($lang,  $value) {
      $this->values[$lang] = $value;
   }

   /**
    * Metoda vrací název proměné objektu při přímém přístupu
    * @param string $name -- název proměnné
    * @return mixed
    */
   public function  &__get($lang) {
      return $this->values[$lang];
   }

   /**
    * Metoda zjišťuje jestli daný prvek existuje
    * @param string $name -- název prvku
    * @return boolena
    */
   public function  __isset($lang) {
      return isset ($this->values[$lang]);
   }

   /**
    * Metoda odstraní zadaný prvek
    * @param string $name -- název prvku
    */
   public function  __unset($lang) {
      unset ($this->values[$lang]);
   }

   /**
    * Metoda pro nastavení hodnoty prvku při přístupu přes pole
    * @param string $offset -- název proměnné
    * @param mixed $value -- hodnota prvku
    */
   public function offsetSet($lang, $value) {
      $this->__set($lang, $value);
   }

   /**
    * Metoda zjišťuje existenci prvku při přístupu přes pole
    * @param string $offset -- název proměnné
    * @return boolean
    */
   public function offsetExists($lang) {
      return isset($this->values[$lang]);
   }

   /**
    * Metoda odstranění prvku při přístupu přes pole
    * @param string $offset -- název prvku
    */
   public function offsetUnset($lang) {
      unset($this->container[$lang]);
   }

   /**
    * Metoda pro vrácení hodnoty prvku při přístupu přes pole
    * @param string $offset -- název proměnné
    * @return mixed -- hodnota prvku
    */
   public function &offsetGet($lang) {
      $str = null;
      return isset($this->values[$lang]) ? $this->values[$lang] : $str;
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
    * Metoda vrací obsah jako pole
    * @return array
    */
   public function toArray()
   {
      return $this->values;
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
   
   public function getValue($lang = false, $backupLang = false)
   {
      if(!$lang ){
         $lang = Locales::getLang();
      }
      
      if(isset($this->values[$lang]) && $this->values[$lang] != null){
         return $this->values[$lang];
      }
      
      if($backupLang && isset($this->values[$backupLang])){
         return $this->values[$backupLang];
      }
      return (string)null;
   }

}
