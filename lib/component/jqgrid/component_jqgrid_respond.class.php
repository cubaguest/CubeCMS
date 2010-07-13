<?php
/**
 * Třída pro vytvoření odpovědi pro jqgrid
 */
class Component_JqGrid_Respond {
    private $recordsOnPage = 15;

    public $page = 1; // current page
    public $total = 1; // total pages
    public $records = 1; // total records
    public $rows = array(); // records

    public function setPage($page) {
       $this->page = $page;
    }

    public function setRecordsOnPage($records) {
       $this->recordsOnPage = $records;
    }

    public function getRecordsOnPage() {
       return $this->recordsOnPage;
    }

    public function setRecords($records) {
       $this->records = $records;
       $this->total = ceil($this->records/$this->recordsOnPage);
    }
}
?>
