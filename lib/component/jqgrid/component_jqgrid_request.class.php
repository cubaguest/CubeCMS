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

   private $type = null;
   public $rows = 0;
   public $page = 1;
   public $orderField = null;
   public $order = 'asc';
   private $isSearch = false;

   public function __construct() {
      if (isset($_POST[self::REQUEST_SEARCH]) AND $_POST[self::REQUEST_SEARCH] == 'true') {
         $this->isSearch = true;
      }

      $this->rows = (int)$_POST[self::REQUEST_ROWS];
      $this->page = (int)$_POST[self::REQUEST_PAGE];
      $this->order = $_POST[self::REQUEST_SORT_ORD];
      $this->orderField = $_POST[self::REQUEST_SORT_FIELD];
   }

   public function isSearch() {
      return $this->isSearch;
   }

}
?>
