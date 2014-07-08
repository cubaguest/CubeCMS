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
   const COLUMN_IS_NEW = 0;
   const COLUMN_IS_FROM_DB = 1;
   const COLUMN_IS_CHANGED = 2;
   
   protected $columns = array();
   protected $externColumns = array();
   protected $pKeyValue = null;
   protected $fromDb = false;

   /**
    * Model záznamu
    * @var Model_ORM  
    */
   protected $model; 
   
   public function __construct($columns, $fromDb = false, $model = null)
   {
      $this->fromDb = $fromDb;
      $this->columns = $columns;
      foreach ($this->columns as $name => $params) {
         if($params['lang']){
            $this->columns[$name]['value'] = new Model_ORM_LangCell();
         }
         if(!$fromDb && isset($this->columns[$name]['default'])){
            if((string)$this->columns[$name]['default'] == "CURRENT_TIMESTAMP"){
               $this->columns[$name]['value'] = date(DATE_ISO8601);
            } else {
               $this->columns[$name]['value'] = $this->columns[$name]['default'];
            }
         }
      }
      $this->model = $model;
   }
   
   protected function getColsFunc($funcname)
   {
      return false;
   }
   
   public function __call($name, $args)
   {
      $funcName = strtolower(str_replace('get', '', $name));
      $colname = $this->getColsFunc($funcName);
      if($colname){
         return $this->$colname;
      }
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
         if ((is_object($value) AND is_object($this->columns[$collName]['value']) 
               AND spl_object_hash($value) == spl_object_hash($this->columns[$collName]['value']))
               OR $value == $this->columns[$collName]['value']) {
            return;
         }
      }
      if (isset($this->columns[$collName])) {
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

         if(isset($this->columns[$collName]['pdoparam'])){
            if ($this->columns[$collName]['pdoparam'] == PDO::PARAM_BOOL) {
               $value = (bool) $value;
            } else if ($this->columns[$collName]['pdoparam'] == PDO::PARAM_INT) {
               $value = (int) $value;
            }
         }

         if($this->columns[$collName]['lang'] == true){
            if(! $this->columns[$collName]['value'] instanceof Model_ORM_LangCell){
               $this->columns[$collName]['value'] = new Model_ORM_LangCell();
            }
            if (is_array($value)){
               $this->columns[$collName]['value'] = new Model_ORM_LangCell($value);
            } else {
               $this->columns[$collName]['value']->addValue(Locales::getLang(), $value);
            }
         } else {
            $this->columns[$collName]['value'] = $value;
         }

         if ($this->fromDb == true AND $this->columns[$collName]['changed'] == 0) {
            $this->columns[$collName]['changed'] = -1; // from db
         } else {
            $this->columns[$collName]['changed'] = 1; // changed in app
         }
      } else {
         // tady detekce jazyka
         $collLen = strlen($collName);
         if ($collLen > 3 && $collName[$collLen - 3] == '_') {
            $lang = substr($collName, -2);
            $collName = substr($collName, 0, $collLen-3);
            
            if(!isset ($this->columns[$collName])){ // není sloupce z této tabulky
               $this->columns[$collName] = Model_ORM::getDefaultColumnParams();
               $this->columns[$collName]['extern'] = true;
            }
            if (!($this->columns[$collName]['value'] instanceof Model_ORM_LangCell)) {
               $this->columns[$collName]['value'] = new Model_ORM_LangCell();
            }
            
            $this->columns[$collName]['value']->addValue($lang, $value);
            
            if ($this->fromDb == true AND $this->columns[$collName]['changed'] == 0) {
               $this->columns[$collName]['changed'] = -1;
            } else {
               $this->columns[$collName]['changed'] = 1;
            }
         } else {
            // externí sloupce, např. s joinů
            $this->columns[$collName] = Model_ORM::getDefaultColumnParams();
            $this->columns[$collName]['value'] = $value;
            $this->columns[$collName]['extern'] = true;
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
   
   /**
    * Metoda nastavi hodnotu primárního klíče (internal function) !!!
    * @return int 
    */
   public function _setPK($val)
   {
      $this->pKeyValue = $val;
      foreach ($this->columns as &$coll) {
         if($coll['pk'] == true){
            $coll['value'] = $val;
         }
         // change column is from db
         $coll['changed'] = -1;
      }
      $this->fromDb = true;
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
   
   /**
    * Metoda nastaví že záznam je nový
    * @return bool
    */
   public function setNew()
   {
      $this->fromDb = false;
      foreach ($this->columns as &$col) {
         $col['changed'] = 1;
         if($col['pk'] == true){
            $col['value'] = null;
         }
      }
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

   /**
    * Metoda vrací obsah záznamu jako asociativní pole
    * @return array
    */
   public function toArray()
   {
      $array = array();

      foreach($this as $column => $properties){
         $array[$column] = $properties['value'];
      }
      return $array;
   }
   
   /* implementace rozhraní */

   /**
    * Metoda pro nastavení hodnoty prvku při přístupu přes pole
    * @param string $offset -- název proměnné
    * @param mixed $value -- hodnota prvku
    */
   public function offsetSet($offset, $value)
   {
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

   /**
    * Metoda uloží data daného záznamu
    */
   public function save()
   {
      if($this->model != null){
         return $this->model->save($this);
      }
      return false;
   }

   /**
    * Metoda pro serializaci
    */
   public function __sleep()
   {
      // remove DateTime obj
      foreach ($this->columns as &$col) {
         if($col['value'] instanceof DateTime){
            $col['value'] = $col['value']->format(DATE_ISO8601);
         }
      }
      // remove DateTime obj
      foreach ($this->externColumns as &$col) {
         if($col['value'] instanceof DateTime){
            $col['value'] = $col['value']->format(DATE_ISO8601);
         }
      }

      return array('columns', 'externColumns', 'pKeyValue', 'fromDb', 'model');
   }
}
