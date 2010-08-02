<?php
/** 
 * Třída Komponenty pro práci se scrolováním stránek.
 * Třída obsahuje metody pro práci s posunováním stránek. Je určena hlavně
 * ke posunování mezi více stránkami (např. novinky). Obsahuje také vlastní šablonu,
 * kterou lze jednoduše vložit do modulu, popřípadě si vytořit vlastní.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída Komponenty pro skrolování po seznamu
 */

class Component_Scroll extends Component {
   /**
    * Výchozí první stránka
    */
   const DEFAULT_START_PAGE = 1;

   /**
    * Konfigurační volba s počtem všech záznamů
    */
   const CONFIG_CNT_ALL_RECORDS = 'count_all_records';

   /**
    * Konfigurační volba s počtem záznamů na stránce
    */
   const CONFIG_RECORDS_ON_PAGE = 'records_on_page';

   /**
    * Konfigurační volba se číslem záznamu který se má zobrazit
    */
   const CONFIG_START_RECORD = 'start_record';

   /**
    * Konfigurační volba se číslem počáteční stránky
    */
   const CONFIG_START_PAGE = 'start_page';

   /**
    * Konfigurační volba jesli se zobrazuje pospátku
    */
   const CONFIG_BACKWARD = 'backward';

   /**
    * Konstanta výchozí stránky - první strana
    */
   const PAGE_FIRST = 'first';
   /**
    * Konstanta výchozí stránky - prostřední strana
    */
   const PAGE_MIDDLE = 'middle';
   /**
    * Konstanta výchozí stránky - poslední strana
    */
   const PAGE_LAST = 'last';

   /**
    * Aktuální stránka
    *
    * @var integer
    */
   private $selectPage = 1;

   /**
    * POčet stran
    * @var integer
    */
   private $countAllPages = 0;

   protected $config = array('page_param' => 'page',
           self::CONFIG_RECORDS_ON_PAGE => 10,
           'disable_prevnext_before_end' => true,
           self::CONFIG_BACKWARD => false,
           'neighbours_pages' => 4,
           'neighbours_separator' => '&nbsp;',
           self::CONFIG_CNT_ALL_RECORDS => 0,
           self::CONFIG_START_RECORD => null,
           self::CONFIG_START_PAGE => self::PAGE_FIRST,
           'tpl_file' => 'scroll.phtml');

   /**
    * Metoda inicializace, je spuštěna pří vytvoření objektu
    */
   protected function init() {
   }

   /**
    * Metoda nastaveí číslo stránky
    */
   private function setPageNumber() {
      // zjištění výchozí stránky
      $defaulParam = self::DEFAULT_START_PAGE;
      switch ($this->getConfig(self::CONFIG_START_PAGE)) {
         case self::PAGE_MIDDLE:
            $defaulParam = $this->countAllPages/2;
            break;
         case self::PAGE_LAST:
            $defaulParam = $this->countAllPages;
            break;
         case self::PAGE_FIRST:
         default:
            $defaulParam = self::DEFAULT_START_PAGE;
            if(is_int($this->getConfig(self::CONFIG_START_PAGE))) $defaulParam = $this->getConfig(self::CONFIG_START_PAGE);
            break;
      }
      $this->selectPage = (int)$this->pageLink()->getParam($this->getConfig('page_param'), $defaulParam);
   }

   /**
    * Metoda nastaví číslo stránky
    */
   public function setCurPageNumber($page) {
      $this->selectPage = $page;
   }

   /**
    * Metoda vrací číslo stránky
    * @return int číslo aktuální stránky
    */
   public function getCurPageNumber() {
      return $this->selectPage;
   }

   /**
    * Metoda pro vypočítá údaje
    */
   private function compute() {
      $this->countAllPages();
      $this->setPageNumber();
      $this->countStartRecord();
   }

   /**
    * Metoda vrací číslo záznamu od kterého se má začít
    */
   public function getStartRecord() {
      if($this->getConfig(self::CONFIG_START_RECORD) === null) {
         $this->compute();
      }
      return $this->getConfig(self::CONFIG_START_RECORD);
   }

   /**
    * Metoda vrací počet záznamů na stránce
    */
   public function getRecordsOnPage() {
      return $this->getConfig(self::CONFIG_RECORDS_ON_PAGE);
   }

   /**
    * Vnitřní metoda vypočítá startovací pozici záznamu
    */
   private function countStartRecord() {
      $this->setConfig(self::CONFIG_START_RECORD, 0);
      if($this->getConfig(self::CONFIG_CNT_ALL_RECORDS) > $this->getRecordsOnPage()) {
         if($this->getConfig(self::CONFIG_BACKWARD) == false) {
            $this->setConfig(self::CONFIG_START_RECORD, ($this->selectPage-1)*$this->getRecordsOnPage());
         } else {
            $sr = ($this->countAllPages-$this->selectPage)*$this->getRecordsOnPage();
            $this->setConfig(self::CONFIG_START_RECORD, $sr);
         }
      }
   }

   /**
    * Metoda vypočítá počet stránek
    */
   private function countAllPages() {
      if($this->getConfig(self::CONFIG_CNT_ALL_RECORDS) != 0 AND $this->getRecordsOnPage() > 0) {
         $this->countAllPages = ceil($this->getConfig(self::CONFIG_CNT_ALL_RECORDS)/$this->getRecordsOnPage());
      }
   }

   /**
    * Spuštění pluginu
    * @param mixed $params -- parametry epluginu (pokud je třeba)
    */
   public function mainController() {
   }

   /**
    * Metoda nastaví id šablony pro výpis
    * @param integer -- id šablony (jakékoliv)
    */
   public function mainView() {
      $this->whichButtons();
      $this->whichNeighbourButtons();
      $this->template()->selectedPage = $this->selectPage;
      $this->template()->countAllPages = $this->countAllPages;
      $this->template()->addTplFile($this->getConfig('tpl_file'));
   }

   /**
    * Metoda vypočítá které tlačítka jsou zobrazeny
    * Vnitřní metoda
    */
   private function whichButtons() {
      if ((int)$this->selectPage != 1) {
         $this->template()->activeButtonBegin = true;
         if (((int)$this->selectPage != 2 AND $this->getConfig('disable_prevnext_before_end') !== true)
                 OR $this->getConfig('disable_prevnext_before_end') == true) {
            $this->template()->activeButtonPrevious = true;
         }
      }
      if ((int)$this->selectPage != $this->countAllPages AND $this->countAllPages != 0) {
         $this->template()->activeButtonEnd = true;
         if (((int)$this->selectPage != $this->countAllPages-1 AND $this->getConfig('disable_prevnext_before_end') !== true)
                 OR $this->getConfig('disable_prevnext_before_end') == true) {
            $this->template()->activeButtonNext = true;
         }
      }
   }

   /**
    * Metoda vypočítá pole kolem ukazovacího prvku
    */
   private function whichNeighbourButtons() {
      $this->template()->pagesLeftSideArray = array();
      $this->template()->pagesRightSideArray = array();
      for ($i = max(1, $this->selectPage - $this->getConfig('neighbours_pages')); $i < $this->selectPage; $i++ ) {
         $this->template()->pagesLeftSideArray[$i] = $i;
      }
      for ($i = $this->selectPage + 1; $i <= min($this->selectPage  + $this->getConfig('neighbours_pages'), $this->countAllPages); $i++ ) {
         $this->template()->pagesRightSideArray[$i] = $i;
      }
      if (min($this->selectPage + $this->getConfig('neighbours_pages'), $this->countAllPages) != $this->countAllPages) {
         $this->template()->pagesRightSideArray[$i-1] .= $this->getConfig('neighbours_separator');
      };
   }
}
?>