<?php

/**
 * Abstraktní třída pro Db Model typu PDO.
 * Tříta pro vytvoření modelu, přistupujícího k databázi. Umožňuje základní práce
 * s databází.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: dbmodel.class.php 615 2009-06-09 13:05:12Z jakub $ VVE3.9.2 $Revision: 615 $
 * @author			$Author: jakub $ $Date: 2009-06-09 15:05:12 +0200 (Út, 09 čen 2009) $
 * 						$LastChangedBy: jakub $ $LastChangedDate: 2009-06-09 15:05:12 +0200 (Út, 09 čen 2009) $
 * @abstract 		Abstraktní třída pro vytvoření modelu pro práci s databází
 */
class Model_ORM_Record implements ArrayAccess, Countable, Iterator {

   protected $columns = array();
   protected $externColumns = array();
   protected $pKeyValue = null;
   protected $fromDb = false;

   public function __construct($columns, $fromDb = false)
   {
      $this->fromDb = $fromDb;
      foreach ($columns as &$column) {
         if($column['lang']){
            $column['value'] = new Model_ORM_LangCell();
         }
      }
      $this->columns = $columns;
   }

   /**
    * Magic pro nastavení slupce
    * @param <type> $name
    * @param <type> $value
    */
   public function __set($collName, $value)
   {
      // kontrola jestli byla provedena změna
      if (isset($this->columns[$collName]) AND $this->columns[$collName]['changed'] != 0) {
         if ((is_object($value) AND is_object($this->columns[$collName]['value']) AND spl_object_hash($value) == spl_object_hash($this->columns[$collName]['value']))
            OR $value == $this->columns[$collName]['value']) {
            return;
         }
      }

      if (!isset($this->columns[$collName])) {
         // tady detekce jazyka
         $collLen = strlen($collName);
         if (isset($collName[$collLen - 3]) && $collName[$collLen - 3] == '_') {
            $pos = strrpos($collName, '_');
            $lang = substr($collName, $pos + 1,$collLen-1-$pos);
            $collName = substr($collName, 0, $pos);
            if(!isset ($this->columns[$collName])){ // není sloupce z této tabulky
               $this->columns[$collName] = Model_ORM::getDefaultColumnParams();
               $this->columns[$collName]['extern'] = true;
            }
            if (!($this->columns[$collName]['value'] instanceof Model_ORM_LangCell)) {
               $this->columns[$collName]['value'] = new Model_ORM_LangCell();
            }
            $this->columns[$collName]['value']->addValue($lang, $value);
            if ($this->fromDb == true AND ($this->columns[$collName]['changed'] == 0)) {
               $this->columns[$collName]['changed'] = -1;
            } else {
               $this->columns[$collName]['changed'] = 1;
            }
         } else {
            // externí sloupce, např. s joinů
            $this->columns[$collName] = Model_ORM::getDefaultColumnParams();
            $this->columns[$collName]['value'] = $value;
         }
      } else {
         // primary key (jazykové nejsou pk)
         if (isset($this->columns[$collName]['pk']) AND $this->columns[$collName]['pk'] == true) {
            if($value === null){
               $this->fromDb = false;
               $this->pKeyValue = $value;
               // changed all colls to new value
               foreach ($this->columns as &$coll) {
                  $coll['changed'] = 1;
               }
            } else {
               $this->pKeyValue = $value;
            }
         }
         
         if (isset($this->columns[$collName]['pdoparam']) AND $this->columns[$collName]['pdoparam'] == PDO::PARAM_BOOL) {
            $value = (bool) $value;
         } else if (isset($this->columns[$collName]['pdoparam']) AND $this->columns[$collName]['pdoparam'] == PDO::PARAM_INT) {
            $value = (int) $value;
         }
         if ($this->columns[$collName]['lang'] == true && !is_array($value)){
            $this->columns[$collName]['value']->addValue(Locales::getLang(), $value);
         } else {
            $this->columns[$collName]['value'] = $value;
         }
         
         if ($this->fromDb == true AND $this->columns[$collName]['changed'] == 0) {
            $this->columns[$collName]['changed'] = -1; // from db
         } else {
            $this->columns[$collName]['changed'] = 1; // changed in app
         }
      }
   }

   /**
    * Magic pro vybrání hodnoty slupce
    * @param string $collName -- název sloupce
    */
   public function &__get($collName)
   {
      // tady kontroly sloupců
      if (isset($this->columns[$collName])) {
         if($this->columns[$collName]['lang'] == true){
            $this->columns[$collName]['changed'] = 1;
         }
         return $this->columns[$collName]['value'];
      }
      $var = null;
      return $var;
   }

   /**
    * Metoda zjišťuje jestli daný prvek existuje
    * @param string $name -- název prvku
    * @return boolena
    */
   public function __isset($collName)
   {
      return isset($this->columns[$collName]);
   }

   public function getPK()
   {
      return $this->pKeyValue;
   }

   public function getColumns()
   {
      return $this->columns;
   }

   /**
    * Metoda vrací jestli se jedná o nový záznam
    * @return bool
    */
   public function isNew()
   {
      return!$this->fromDb;
   }

   public function mapArray($array)
   {
//      var_dump($array);flush();
      foreach ($array as $column => $value) {
//         echo "col: ".$column.' val: '.$value.', ';
         // zde asij kontroly typů
         if (is_array($value) OR is_object($value)) {
            $value = serialize($value);
         }
         if (isset($this->columns[$column])) {
            $this->$column = $value;
         }
      }
   }

   /* implementace rozhraní */

   /**
    * Metoda pro nastavení hodnoty prvku při přístupu přes pole
    * @param string $offset -- název proměnné
    * @param mixed $value -- hodnota prvku
    */
   public function offsetSet($offset, $value)
   {
      var_dump('offsetset');
      $this->__set($offset, $value);
   }

   /**
    * Metoda zjišťuje existenci prvku při přístupu přes pole
    * @param string $offset -- název proměnné
    * @return boolean
    */
   public function offsetExists($offset)
   {
      return isset($this->columns[$offset]);
   }

   /**
    * Metoda odstranění prvku při přístupu přes pole
    * @param string $offset -- název prvku
    */
   public function offsetUnset($offset)
   {
      unset($this->columns[$offset]);
   }

   /**
    * Metoda pro vrácení hodnoty prvku při přístupu přes pole
    * @param string $offset -- název proměnné
    * @return mixed -- hodnota prvku
    */
   public function offsetGet($offset)
   {
      return $this->__get($offset);
   }

   /**
    * Metoda pro počítání prvků
    * @return int -- počet prvků
    */
   public function count()
   {
      return count($this->columns);
   }

   /**
    * Metody pro posun po prvcích pomocí foreach
    */
   public function rewind()
   {
      reset($this->columns);
   }

   public function current()
   {
      return current($this->columns);
   }

   public function key()
   {
      return key($this->columns);
   }

   public function next()
   {
      next($this->columns);
   }

   public function valid()
   {
      return!is_null(key($this->columns));
   }

}
?>