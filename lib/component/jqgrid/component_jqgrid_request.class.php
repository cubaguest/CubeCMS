<?php

/**
 * Třída pro vytvoření požadavku pro naplnění jqgrid
 */
class Component_JqGrid_Request {
   const REQUEST_ROWS = 'rows';
   const REQUEST_PAGE = 'page';
   const REQUEST_SORT_FIELD = 'sidx';
   const REQUEST_SORT_ORD = 'sord';
   const REQUEST_SEARCH = '_search';
   const REQUEST_FILTERS = 'filters';
   const REQUEST_SEARCH_FIELD = 'searchField';
   const REQUEST_SEARCH_STRING = 'searchString';
   const REQUEST_SEARCH_OPERATOR = 'searchOper';

   const SEARCH_EQUAL = 'eq';
   const SEARCH_NOT_EQUAL = 'ne';
   const SEARCH_CONTAIN = 'cn';
   const SEARCH_NOT_CONTAIN = 'nc';
   const SEARCH_BY_COLS = 'scols';
   const SEARCH_BY_FILTERS = 'sf';
   
   public $rows = 1000;
   public $page = 1;
   public $orderField = null;
   public $order = 'asc';
   private $isSearch = false;

   private $searchFiled = null;
   private $searchOper = null;
   private $searchString = null;
   private $filterGroup = 'AND';

   private static $searchAvailOper = array('eq', 'ne', 'cn', 'nc');

   public function __construct() {
      if (isset($_POST[self::REQUEST_SEARCH]) AND $_POST[self::REQUEST_SEARCH] == 'true') {
         $this->isSearch = true;
         if(isset($_POST[self::REQUEST_FILTERS]) && $_POST[self::REQUEST_FILTERS] != null){
            // hledání podle filtrů
            $this->searchOper = self::SEARCH_BY_FILTERS;
            
            
         } else if(isset($_POST[self::REQUEST_SEARCH_OPERATOR])){
            // hledání podle jednoho kritéria
            $this->searchFiled = $_POST[self::REQUEST_SEARCH_FIELD];
            if(in_array($_POST[self::REQUEST_SEARCH_OPERATOR], self::$searchAvailOper)){
               $this->searchOper = $_POST[self::REQUEST_SEARCH_OPERATOR];
            } else {
               $this->searchOper = self::SEARCH_CONTAIN;
            }
            $this->searchString = $_POST[self::REQUEST_SEARCH_STRING];
         } else {
            // hledání podle sloupců
            $this->searchOper = self::SEARCH_BY_COLS;
            $this->searchString = array();
            $this->searchFiled = array();
            foreach ($_POST as $key => $value) {
               if($key != self::REQUEST_PAGE && $key != self::REQUEST_ROWS
                  && $key != self::REQUEST_SEARCH && $key != self::REQUEST_SEARCH_FIELD 
                  && $key != self::REQUEST_SEARCH_OPERATOR && $key != self::REQUEST_SEARCH_STRING 
                  && $key != self::REQUEST_SORT_FIELD && $key != self::REQUEST_SORT_ORD ){
                  
                  $this->searchString[$key] = $value;
                  $this->searchFiled[] = $key;
               }
            }
         }
      }
      if(isset ($_POST[self::REQUEST_ROWS]))
         $this->rows = (int)$_POST[self::REQUEST_ROWS];
      if(isset ($_POST[self::REQUEST_PAGE]))
         $this->page = (int)$_POST[self::REQUEST_PAGE];
      if(isset ($_POST[self::REQUEST_SORT_ORD]))
         $this->order = $_POST[self::REQUEST_SORT_ORD];
      if(isset ($_POST[self::REQUEST_SORT_FIELD]))
         $this->orderField = $_POST[self::REQUEST_SORT_FIELD];
   }

   /**
    * Metoda vrací jestli se jedná o hledání
    * @return boolean
    */
   public function isSearch() {
      return $this->isSearch;
   }

   /**
    * Metoda nastaví výchozí sloupec podle kterého se řadí
    * @param string $field -- název sloupce
    */
   public function setDefaultOrderField($field, $order = 'asc') {
      if($this->orderField == null || !isset ($_POST[self::REQUEST_SORT_FIELD])){
         $this->orderField = $field;
      }
      if(!isset ($_POST[self::REQUEST_SORT_ORD])){
         $this->order = $order;
      }
   }

   public function searchType() {
      return $this->searchOper;
   }

   public function searchField() {
      return $this->searchFiled;
   }

   public function searchString($colname = null) {
      if(is_array($this->searchString) && isset($this->searchString[$colname])){
         return $this->searchString[$colname];
      }
      return $this->searchString;

   }
   
   public function isSearchCol($colname) {
      return isset($this->searchString[$colname]);
   }
}
?>
