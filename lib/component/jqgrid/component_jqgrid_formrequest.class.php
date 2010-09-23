<?php

/**
 * Třída pro vytvoření požadavku pro naplnění jqgrid - formuláře
 */
class Component_JqGrid_FormRequest extends Object implements ArrayAccess, Countable, Iterator {
   const REQUEST_TYPE = 'oper';
   const REQUEST_ID = 'id';

   const REQUEST_TYPE_ADD = 'add';
   const REQUEST_TYPE_EDIT = 'edit';
   const REQUEST_TYPE_DELETE = 'del';

   const REQUEST_ID_SEPARATOR = ',';


   private $isRequest = false;
   private $type = null;
   private $ids = array();
   private $requestData = null;

   private $index = null;

   public function __construct() {
      if (isset($_POST[self::REQUEST_TYPE])) {
         $this->isRequest = true;
         $this->type = $_POST[self::REQUEST_TYPE];
         $this->requestData = $_POST;
         $this->checkDataTypes();
         unset($this->requestData[self::REQUEST_TYPE]);
      }
   }

   /**
    * Metoda vrací typ požadavku
    * @return string
    */
   public function getRequest() {
      return $this->type;
   }

   /* OVERLOADING */

   public function __set($name, $value) {
      $this->requestData[$name] = $value;
   }

   public function __get($name) {
      if (array_key_exists($name, $this->requestData)) {
         return $this->requestData[$name];
      }
      return null;
   }

   /**  As of PHP 5.1.0  */
   public function __isset($name) {
      return isset($this->requestData[$name]);
   }

   /**  As of PHP 5.1.0  */
   public function __unset($name) {
      unset($this->requestData[$name]);
   }

   /**
    * Metoda vrací pole s odeslanými id
    * @return array
    */
   public function getIds() {
      return explode(self::REQUEST_ID_SEPARATOR, $this->id);
   }

   /** implementace ArrayAcces */

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
      return isset($this->requestData[$offset]);
   }

   /**
    * Metoda odstranění prvku při přístupu přes pole
    * @param string $offset -- název prvku
    */
   public function offsetUnset($offset) {
      unset($this->requestData[$offset]);
   }

   /**
    * Metoda pro vrácení hodnoty prvku při přístupu přes pole
    * @param string $offset -- název proměnné
    * @return mixed -- hodnota prvku
    */
   public function offsetGet($offset) {
      return isset($this->requestData[$offset]) ? $this->requestData[$offset] : null;
   }

   /**
    * Metoda pro počítání prvků
    * @return int -- počet prvků
    */
   public function count() {
      return count($this->requestData);
   }

   /**
    * Metody pro posun po prvcích pomocí foreach
    */
   public function rewind() {
      reset($this->requestData);
   }

   public function current() {
      return current($this->requestData);
   }

   public function key() {
      return key($this->requestData);
   }

   public function next() {
      next($this->requestData);
   }

   public function valid() {
      return ! is_null(key($this->requestData));
   }

   private function checkDataTypes() {
      foreach ($this->requestData as $key => $data) {
         if(ctype_digit($data)){
            $this->requestData[$key] = (int)$data;
         } else if($data == 'true'){
            $this->requestData[$key] = true;
         } else if($data == 'false'){
            $this->requestData[$key] = false;
         } 
      }
   }

}
?>
