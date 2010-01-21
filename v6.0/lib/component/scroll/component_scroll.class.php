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
    * Aktuální stránka
    *
    * @var integer
    */
   private $selectPage = 1;

   /**
    * Počet všech stránek
    *
    * @var ineteger
    */
   private $countAllPages = 1;

   protected $config = array('page_param' => 'page',
                             self::CONFIG_RECORDS_ON_PAGE => 10,
                             'neighbours_pages' => 4,
                             'neighbours_separator' => '&nbsp;',
                             self::CONFIG_CNT_ALL_RECORDS => 0,
                             self::CONFIG_START_RECORD => 0,
                             'tpl_file' => 'scroll.phtml');

   /**
    * Metoda inicializace, je spuštěna pří vytvoření objektu
    */
   protected function init(){
   }

   /**
    * Metoda nastaveí číslo stránky
    */
   private function setPageNumber() {
      $this->selectPage = $this->pageLink()->getParam($this->getConfig('page_param'), 1);
   }

   /**
    * Metoda nastaví číslo stránky
    */
   public function setCurPageNumber($page) {
      $this->selectPage = $page;
   }

   /**
    * Metoda pro spouštění některých akcí přímo v kontroleru
    */
   public function runCtrlPart() {
      $this->setPageNumber();
      $this->countStartRecord();
   }

   /**
    * Vnitřní metoda vypočítá startovací pozici záznamu
    */
   private function countStartRecord(){
      if($this->getConfig(self::CONFIG_CNT_ALL_RECORDS) > $this->getConfig(self::CONFIG_RECORDS_ON_PAGE)){
         $this->setConfig(self::CONFIG_START_RECORD, ($this->selectPage-1)*$this->getConfig(self::CONFIG_RECORDS_ON_PAGE));
      } else {
         $this->selectPage = self::DEFAULT_START_PAGE;
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
      $this->countButtons();
      $this->countPagesButtons();
      $this->template()->selectedPage = $this->selectPage;
      $this->template()->addTplFile($this->getConfig('tpl_file'));
   }

   /**
    * Metoda vypočítá které tlačítka jsou zobrazeny
    * Vnitřní metoda
    */
   private function countButtons(){
      if ($this->selectPage != 1){
         $this->template()->activeButtonBegin = true;
         //	if ($this->selectPage != 2){
         $this->template()->activeButtonPrevious = true;
         //	}
      }
      //	Výpočet všech stránek
      $countAllPagesRemainder = 0;
      if($this->getConfig(self::CONFIG_CNT_ALL_RECORDS) != 0 AND $this->getConfig(self::CONFIG_RECORDS_ON_PAGE)){
         $this->template()->countAllPages=floor($this->getConfig(self::CONFIG_CNT_ALL_RECORDS)/$this->getConfig(self::CONFIG_RECORDS_ON_PAGE));
         $countAllPagesRemainder=$this->getConfig(self::CONFIG_CNT_ALL_RECORDS) % $this->getConfig(self::CONFIG_RECORDS_ON_PAGE);
      }
      if ($countAllPagesRemainder > 0){
         $this->template()->countAllPages++;
      }
      if ($this->selectPage != $this->template()->countAllPages AND $this->template()->countAllPages != 0){
         $this->template()->activeButtonEnd = true;
         //	if ($this->selectPage != $this->countAllPages-1){
         $this->template()->activeButtonNext = true;
         //	}
      }
   }

   /**
    * Metoda vypočítá pole kolem ukazovacího prvku
    */
   private function countPagesButtons() {
      //	if (max(1, $this->selectPage - $this->neighbourPages) != 1){
      //	$leftsepar = self::NEIGHBOURS_SEPARATOR;
      //	}
      $this->template()->pagesLeftSideArray = array();
      $this->template()->pagesRightSideArray = array();
      for ($i = max(1, $this->selectPage - $this->getConfig('neighbours_pages')); $i < $this->selectPage; $i++ ){
         $this->template()->pagesLeftSideArray[$i] = $i;
      }
      for ($i = $this->selectPage + 1; $i <= min($this->selectPage  + $this->getConfig('neighbours_pages'), $this->template()->countAllPages); $i++ ){
         $this->template()->pagesRightSideArray[$i] = $i;
      }
      if (min($this->selectPage + $this->getConfig('neighbours_pages'), $this->template()->countAllPages) != $this->template()->countAllPages){
         $this->template()->pagesRightSideArray[$i-1] .= $this->getConfig('neighbours_separator');
      };
   }
}
?>