<?php
/**
 * Objekt sloužící jako šablony z výstupu PDO. Slouží především pro vybrání jazyků
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 5.1.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro vytvoření objektu pro přístup k datům
 */
class Model_LangContainer implements ArrayAccess, Countable, Iterator {
/**
 * Pole s prvky objektu
 * @var array
 */
   private $values = array();

   /**
    * Jestli se má vytvářet pole jazyků nebo vybrat zvolený jazyk
    * @var bool
    */
   private $allLangs = false;

   /**
    * Aktuální pozice ukazatele na prvek
    * @var int
    */
    private $position = 0;

   /**
    * Konstruktor
    * @param bool $allLangs -- jestli má být vytvořeno pole se všemi jazyky,
    * nebo jen výchozí jazyk
    */
   function  __construct($allLangs = false) {
      $this->allLangs = $allLangs;
   }

   /**
    * Magická metoda která nastavuje prvek objektu
    * @param string $name -- název prvku
    * @param mixed $value -- hodnota prvku
    */
   public function  __set($name,  $value) {
      $matches = array();
      if ((bool)preg_match("/^(.*)_([a-z]{2})$/i", $name, $matches)) {
         if(!isset ($this->values[$matches[1]])
             OR !($this->values[$matches[1]] instanceof Model_LangContainer_LangColumn)) {
            $this->values[$matches[1]] = new Model_LangContainer_LangColumn();
         }
         $this->values[$matches[1]]->addValue($matches[2], $value);
      }
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
      return isset($this->container[$offset]);
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
    * Metoda pro počítání prvků
    * @return int -- počet prvků
    */
   public function count() {
      return count($this->values);
   }

   /**
    * Metody pro posun po prvcích pomocí foreach
    */
   public function rewind() {
      reset($this->values);
    }

    public function current() {
      return current($this->values);
    }

    public function key() {
      return key($this->values);
    }

    public function next() {
      next($this->values);
    }

    public function valid() {
        return $this->current() !== false;
    }
}
/**
 * Podpůrná třda pro práci s jazykovými sloupci v Model_LangContainer
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 5.1.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro vytvoření objektu pro přístup k datům
 */
class Model_LangContainer_LangColumn implements ArrayAccess, Countable {
/**
 * Pole s prvky objektu
 * @var array
 */
   private $values = array();

   /**
    * Konstruktor
    */
   function  __construct() {
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
      return isset($this->container[$offset]);
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
      if(gettype($this->values[Locale::getLang()]) == 'string'
        AND $this->values[Locale::getLang()] != null) {
         return $this->values[Locale::getLang()];
      } else if(gettype($this->values[Locale::getDefaultLang()]) == 'string'
        AND $this->values[Locale::getDefaultLang()] != null) {
         return $this->values[Locale::getDefaultLang()];
      } else {
         return null;
      }
   }
}
?>