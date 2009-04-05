<?php
/**
 * Třída Epluginu pro práci se scrolováním stránek.
 * Třída obsahuje metody pro práci s posunováním stránek. Je určena hlavně
 * ke posunování mezi více stránkami (např. novinky). Obsahuje také vlastní šablonu,
 * kterou lze jednoduše vložit do modulu, popřípadě si vytořit vlastní.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída EPluginu pro práci se scrolovátky
 */

class ScrollEplugin extends Eplugin {
    /**
     * Regulérní výraz pro parsování parametru s číslem stránky
     */
   const URL_PARAM_PATTERN = '([0-9]+)';

   /**
    * Výchozí počáteční záznam
    * @var integer
    */
   const DEFAULT_START_RECORD = 0;

   /**
    * Konstanta s oddělovačem sousedů
    * @var string
    */
   const NEIGHBOURS_SEPARATOR = ' ';

   /**
    * Výchozí první stránka
    * @var integer
    */
   const DEFAULT_START_PAGE = 1;


   /**
    * Název primární šablony s posunovátky
    * @var string
    */
   protected $templateFile = 'scroll.tpl';

   /**
    * Proměná obsahuje celkový počet záznamů v db
    * @var integer
    */
   private $allRecordsCount = 0;

   /**
    * Počet zobrazených sousedních stránek
    * @var integer
    */
   private $neighbourPages = 4;

   /**
    * Název parametru, který je přenášen v url se stránkou
    * @var string
    */
   private $urlParamName = "page";

   /**
    * Objekt url parametru s číslem stránky
    * @var UrlParam
    */
   private $param = null;

   /**
    * Proměné, které tlačítka jsou aktivní
    * @var boolean
    */
   private $activeButtonBegin = false;
   private $activeButtonNext = false;
   private $activeButtonBack = false;
   private $activeButtonEnd = false;

   /**
    * Počet záznamů na stránce
    * @var integer
    */
   private $countOnPage = 1;

   /**
    * Počáteční záznam
    * @var integer
    */
   private $numberStartRecord = 0;

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

   /**
    * Pole s odkazy na stránky na levé straně
    *
    * @var array
    */
   private $pagesLeftSideArray = array();

   /**
    * Pole s odkazy na stránky na pravé straně
    * @var array
    */
   private $pagesRightSideArray = array();

   /**
    * Pole s linky pro tlačítka
    * @var array
    */
   private $buttonsLinks = array();

   /**
    * Pole s popisky
    * @var array
    */
   private $labelsArray = array();

   /**
    * Metoda inicializace, je spuštěna pří vytvoření objektu
    */
   protected function init(){
      // nastavení objektu parametru
      $this->param = new UrlParam($this->urlParamName, self::URL_PARAM_PATTERN);
      //	Nčtení stránky z url
      $this->getPageFromUrl();
      $this->createLabels();
   }

   /**
    * Metoda nastavuje celkový počet záznamů v db
    *
    * @param integer -- celkový počet záznamů
    */
   public function setCountAllRecords($records){
      $this->allRecordsCount = $records;
      //		Výpočet jednotlivých prvku
      $this->countStartRecord();
      $this->countButtons();
      $this->countPagesButtons();
      $this->createButtonsLinks();
   }

   /**
    * Metoda nastavuje parametr, kterým se přenáší stránka v url
    * @param string
    */
   public function setUrlParam($urlParam) {
      $this->urlParamName = $urlParam;
      $this->param = new UrlParam($this->urlParamName, self::URL_PARAM_PATTERN);
      //	Znovunačtení stránky z url
      $this->getPageFromUrl();
   }

   /**
    * Metoda nastavuje stránku z url
    */
   private function getPageFromUrl() {
      if($this->param->isValue()){
         $this->selectPage = $this->param->getValue();
      } else {
         $this->selectPage = self::DEFAULT_START_PAGE;
      }
   }

   /**
    * Funkce nastaví počet záznamů na stránce
    *
    * @param int -- počet záznamů na stránce
    */
   public function setCountRecordsOnPage($value = 0){
      $this->countOnPage = $value;
   }

   /**
    * Vnitřní metoda vypočítá startovací pozici záznamu
    */
   private function countStartRecord(){
      if($this->allRecordsCount > $this->countOnPage){
         $this->numberStartRecord=(($this->selectPage-1)*$this->countOnPage);
      } else {
         $this->numberStartRecord=self::DEFAULT_START_RECORD;
         $this->selectPage = self::DEFAULT_START_PAGE;
      }
   }

   /**
    * Metoda vypočítá které tlačítka jsou zobrazeny
    * Vnitřní metoda
    */
   private function countButtons(){
      if ($this->selectPage != 1){
         $this->activeButtonBegin = true;
         //	if ($this->selectPage != 2){
         $this->activeButtonNext = true;
         //	}
      }
      //	Výpočet všech stránek
      $countAllPagesRemainder = 0;
      if($this->allRecordsCount != 0 AND $this->countOnPage){
         $this->countAllPages=floor($this->allRecordsCount/$this->countOnPage);
         $countAllPagesRemainder=$this->allRecordsCount % $this->countOnPage;
      }
      if ($countAllPagesRemainder > 0){
         $this->countAllPages++;
      }
      if ($this->selectPage != $this->countAllPages AND $this->countAllPages != 0){
         $this->activeButtonEnd = true;
         //	if ($this->selectPage != $this->countAllPages-1){
         $this->activeButtonBack = true;
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
      for ($i = max(1, $this->selectPage - $this->neighbourPages); $i < $this->selectPage; $i++ ){
         //    	echo $i;
         $this->pagesLeftSideArray[$i]["name"] = $i;
         $leftsepar = null;
         $this->pagesLeftSideArray[$i]["link"] = $this->getLinks()->param($this->param->setValue($i));
      }
      for ($i = $this->selectPage + 1; $i <= min($this->selectPage  + $this->neighbourPages, $this->countAllPages); $i++ ){
         $this->pagesRightSideArray[$i]["name"] = $i;
         $rightsepar = null;
         $this->pagesRightSideArray[$i]["link"] = $this->getLinks()->param($this->param->setValue($i));
      }
      if (min($this->selectPage + $this->neighbourPages, $this->countAllPages) != $this->countAllPages){
         $this->pagesRightSideArray[$i-1]["name"] .= self::NEIGHBOURS_SEPARATOR;
      };
   }

   /**
    * Metoda nastavuje odkazy na tlačítka
    */
   private function createButtonsLinks(){
      if($this->isButtonBegin()){
         $this->buttonsLinks["begin"]=$this->getLinks()->param($this->param->setValue(1));
      }
      if($this->isButtonNext()){
         $this->buttonsLinks["next"]=$this->getLinks()->param($this->param->setValue($this->selectPage-1));
      }
      if($this->isButtonBack()){
         $this->buttonsLinks["back"]=$this->getLinks()->param($this->param->setValue($this->selectPage+1));
      }
      if($this->isButtonEnd()){
         $this->buttonsLinks["end"]=$this->getLinks()->param($this->param->setValue($this->countAllPages));
      }
   }

   /**
    * Metoda vygeneruje popisky do pole
    */
   private function createLabels(){
      //Popisky tlačítek
      $this->labelsArray["begin"] = _("Začátek");
      $this->labelsArray["next"] = _("Další");
      $this->labelsArray["back"] = _("Předchozí");
      $this->labelsArray["end"] = _("Konec");
   }

   /**
    * Metoda vrací jestli je tlačítko "Na začátek" aktivní
    *
    * @return boolean -- true pokud je tlačítko aktivní
    */
   public function isButtonBegin(){
      return $this->activeButtonBegin;
   }

   /**
    * Metoda vrací jestli je tlačítko "Další" aktivní
    *
    * @return boolean -- true pokud je tlačítko aktivní
    */
   public function isButtonNext(){
      return $this->activeButtonNext;
   }

   /**
    * Metoda vrací jestli je tlačítko "Předchozí" aktivní
    *
    * @return boolean -- true pokud je tlačítko aktivní
    */
   public function isButtonBack(){
      return $this->activeButtonBack;
   }

   /**
    * Metoda vrací jestli je tlačítko "Na konec" aktivní
    *
    * @return boolean -- true pokud je tlačítko aktivní
    */
   public function isButtonEnd(){
      return $this->activeButtonEnd;
   }

   /**
    * Metoda vrací od kterého záznamu se vypisuje
    *
    * @return integer -- strt record
    */
   public function getStartRecord(){
      return $this->numberStartRecord;
   }

   /**
    * Metoda vrací počet záznamů na stránce
    * @return ineteger -- počet záznamů
    */
   public function getCountRecords(){
      return $this->countOnPage;
   }

   /**
    * Metoda obstarává přiřazení proměných do šablony
    */
   protected function assignTpl(){
      //	Zapnutí tlačítek
      $this->toTpl("BUTTON_BEGIN", $this->isButtonBegin());
      $this->toTpl("BUTTON_NEXT", $this->isButtonNext());
      $this->toTpl("BUTTON_BACK", $this->isButtonBack());
      $this->toTpl("BUTTON_END", $this->isButtonEnd());
      //	Odkazy tlačítek
      $this->toTpl("SCROLL_BUTTONS_LINKS", $this->buttonsLinks);
      //	Data o pozici scrolování
      $this->toTpl("SCROLL_SELECTED_PAGE", $this->selectPage);
      $this->toTpl("SCROLL_ALL_PAGES", $this->countAllPages);
      //	popisky
      $this->toTpl("SCROLL_LABELS_ARRAY", $this->labelsArray);
      //	Sousedé u pozice
      $this->toTpl("SCROLL_LEFT_SIDE_DATA_ARRAY", $this->pagesLeftSideArray);
      $this->toTpl("SCROLL_RIGHT_SIDE_DATA_ARRAY", $this->pagesRightSideArray);
   }
}
?>