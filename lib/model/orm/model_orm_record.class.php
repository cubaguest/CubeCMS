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

class Model_ORM_Record implements ArrayAccess, Countable, Iterator {
   protected $columns = array();

   protected $externColumns = array();

   protected $pKeyValue = null;

   protected $fromDb = false;

   public function  __construct($columns, $fromDb = false) {
      $this->fromDb = $fromDb;
      if($fromDb == true){
         foreach ($columns as &$col) {
            unset ($col['valueLoaded']);
         }
      }
      $this->columns = $columns;
   }

   /**
    * Magic pro nastavení slupce
    * @param <type> $name
    * @param <type> $value
    */
   public function  __set($collName, $value) {
      if(!isset ($this->columns[$collName])){
         // tady detekce jazyka
         if ($collName[strlen($collName)-3] == '_') {
            $lang = substr($collName, strrpos($collName, '_')+1);
            $collName = substr($collName, 0, strrpos($collName, '_'));
            if(!($this->columns[$collName]['value'] instanceof Model_ORM_LangCell)) {
               $this->columns[$collName]['value'] = new Model_ORM_LangCell();
               $this->columns[$collName]['valueLoaded'] = new Model_ORM_LangCell();
            }
            $this->columns[$collName]['value']->addValue($lang, $value);
            $this->columns[$collName]['valueLoaded']->addValue($lang, $value);
         } else {
            $this->columns[$collName] = array('value' => $value, 'valueLoaded' => $value, 'extern' => true); // externí sloupce, např. s joinů
         }
      } else {
         // tady kontroly sloupců a přetypování na správné hodnoty
         if($this->fromDb == true AND !isset ($this->columns[$collName]['valueLoaded'])){
            if($this->columns[$collName]['pdoparam'] == PDO::PARAM_BOOL){
               $value = (bool)$value;
            } else if($this->columns[$collName]['pdoparam'] == PDO::PARAM_INT){
               $value = (int)$value;
            }

            $this->columns[$collName]['valueLoaded'] = $value;
         }
         $this->columns[$collName]['value'] = $value;
         if($this->columns[$collName]['pk'] == true){// primary key (jazykové nejsou pk)
            $this->pKeyValue = $value;
         }
      }
   }

   /**
    * Magic pro vybrání hodnoty slupce
    * @param <type> $name
    * @param <type> $value
    */
   public function  __get($collName) {
      // tady kontroly sloupců
      if(isset ($this->columns[$collName])){
         return $this->columns[$collName]['value'];
      }
//      else if(isset ($this->externColumns[$collName])){
//         return $this->externColumns[$collName];
//      }
      return null;
   }

   public function getPK() {
      return $this->pKeyValue;
   }

   public function getColumns() {
      return $this->columns;
   }

   /**
    * Metoda vrací jestli se jedná o nový záznam
    * @return bool
    */
   public function isNew() {
      if($this->pKeyValue == null){
         return true;
      }
      return false;
   }

   public function mapArray($array) {
//      var_dump($array);flush();
      foreach ($array as $column => $value) {
//         echo "col: ".$column.' val: '.$value.', ';
         // zde asij kontroly typů
         if(is_array($value) OR is_object($value)){
            $value = serialize($value);
         }
         if(isset ($this->columns[$column])){
            $this->columns[$column]['value'] = $value;
         }
      }
   }

   /* implementace rozhraní */

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
      return isset ($this->columns[$offset]);
   }

   /**
    * Metoda odstranění prvku při přístupu přes pole
    * @param string $offset -- název prvku
    */
   public function offsetUnset($offset) {
      unset ($this->columns[$offset]);
   }

   /**
    * Metoda pro vrácení hodnoty prvku při přístupu přes pole
    * @param string $offset -- název proměnné
    * @return mixed -- hodnota prvku
    */
   public function offsetGet($offset) {
      return $this->__get($offset);
   }

   /**
    * Metoda pro počítání prvků
    * @return int -- počet prvků
    */
   public function count() {
      return count($this->columns);
   }

   /**
    * Metody pro posun po prvcích pomocí foreach
    */
   public function rewind() {
      reset($this->columns);
    }

    public function current() {
      return current($this->columns);
    }

    public function key() {
      return key($this->columns);
    }

    public function next() {
      next($this->columns);
    }

    public function valid() {
       return ! is_null(key($this->columns));
    }

}
?>