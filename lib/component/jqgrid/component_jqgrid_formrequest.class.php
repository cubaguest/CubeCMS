<?php

/**
 * Třída pro vytvoření požadavku pro naplnění jqgrid
 */
class Component_JqGrid_FormRequest {
   const REQUEST_TYPE   = 'oper';
   const REQUEST_ID     = 'id';

   const REQUEST_TYPE_ADD     = 'add';
   const REQUEST_TYPE_EDIT    = 'edit';
   const REQUEST_TYPE_DELETE  = 'del';

   const REQUEST_ID_SEPARATOR = ',';


   private $isRequest = false;


   private $type = null;
   private $ids = array();

   private $requestData = null;

   public function __construct() {
      if (isset($_POST[self::REQUEST_TYPE])) {
         $this->isRequest = true;
         $this->type = $_POST[self::REQUEST_TYPE];
         $this->requestData = $_POST;
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

}
?>
