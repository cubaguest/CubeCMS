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
   const REQUEST_SEARCH_FIELD = 'searchField';
   const REQUEST_SEARCH_STRING = 'searchString';
   const REQUEST_SEARCH_OPERATOR = 'searchOper';

   const SEARCH_EQUAL = 'eq';
   const SEARCH_NOT_EQUAL = 'ne';
   const SEARCH_CONTAIN = 'cn';
   const SEARCH_NOT_CONTAIN = 'nc';


   public $rows = 1000;
   public $page = 1;
   public $orderField = null;
   public $order = 'asc';
   private $isSearch = false;

   private $searchFiled = null;
   private $searchOper = null;
   private $searchString = null;

   private static $searchAvailOper = array('eq', 'ne', 'cn', 'nc');

   public function __construct() {
      if (isset($_POST[self::REQUEST_SEARCH]) AND $_POST[self::REQUEST_SEARCH] == 'true') {
         $this->isSearch = true;
         $this->searchFiled = $_POST[self::REQUEST_SEARCH_FIELD];
         if(in_array($_POST[self::REQUEST_SEARCH_OPERATOR], self::$searchAvailOper)){
            $this->searchOper = $_POST[self::REQUEST_SEARCH_OPERATOR];
         } else {
            $this->searchOper = self::SEARCH_CONTAIN;
         }
         $this->searchString = $_POST[self::REQUEST_SEARCH_STRING];
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

   public function searchString() {
      return $this->searchString;

   }
}
?>
