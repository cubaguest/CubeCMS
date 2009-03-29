<?php
/**
 * Třída pro práci s hledáním v článcích a kategoriích, každý modul ji
 * implementuje zvlášť, ale obsahuje vlastní metody pro zjednodušení přístupu
 * a vytvoření jednotlivých implementací
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro vyhledávání v modulech
 */
class Search {
   /**
    * Indexy pole s výsledky hledání
    */
   const RESULT_INDEX_CATEGORY    = 'category';
   const RESULT_INDEX_ARTICLE     = 'article';
   const RESULT_INDEX_URL         = 'url';
   const RESULT_INDEX_TEXT        = 'text';
   const RESULT_INDEX_RELEVANCE   = 'relevance';

   /**
    * Pole s itemy, kde je modul použit
    * @var array 
    */
   protected $itemsArray = array();

   /**
    * Řetězec, který se hledá
    * @var string
    */
   protected static $searchString = null;

   /**
    * Která stránka vyhledávání je zobrazena (zatím nepoužito)
    * @var integer
    */
   private static $searchPage = 1;

   /**
    * Pole s výsledky hledání
    * @var array
    */
   protected static $searchResults = array();

   /**
    * Konstruktor
    */
   public function  __construct($itemsArray) {
      $this->itemsArray = $itemsArray;
   }

   /**
    * Metoda proo hledání v modulu
    */
   public function runSearch(){}

   /**
    * Metoda vrací db konektor
    * @return DbInterface
    */
   public function getDb() {
      return AppCore::getDbConnector();
   }

   /**
    * Metoda vrací objekt modulu a jeho aprametrů
    * @return Module -- objekt modulu
    */
   public function getModule() {
      return AppCore::getSelectedModule();
   }

   /**
    * MEtoda přidává výsledek do pole výsledků
    * @param string $category -- název kategorie
    * @param Links $url -- adkaz na výsledek
    * @param string $text -- text výsledku
    * @param float $relevance -- relevance výsledku
    * @param string $article -- (option) název článku
    */
   public function addResult($category, $url, $text, $relevance = 0.1, $article = null) {
      $resultArr = array(
         self::RESULT_INDEX_CATEGORY => $category,
         self::RESULT_INDEX_URL => (string)$url,
         self::RESULT_INDEX_TEXT => $text,
         self::RESULT_INDEX_RELEVANCE => $relevance,
         self::RESULT_INDEX_ARTICLE => $article);

      array_push(self::$searchResults, $resultArr);
   }

   /**
    * Metoda vrací hledaný řetězec
    * @return string
    */
   public function getSearchString() {
      return self::$searchString;
   }

   /**
    * Metoda vrací pole s id items
    * @return array
    */
   public function getItems() {
      return array_keys($this->itemsArray);
   }

   /**
    * Metoda vrací název kategorie podle zadané id items
    * @param integer $idItem
    */
   public function getCategory($idItem) {
      return $this->itemsArray[$idItem][SearchModel::ITEMS_ARRAY_INDEX_CAT_NAME];
   }

   /**
    * Metoda vrací odkaz na vytvořenou kategorii
    * @param integer $idItem -- id items
    * @return Links
    */
   public function getLink($idItem) {
      $link = new Links();
      $link->category($this->itemsArray[$idItem][SearchModel::ITEMS_ARRAY_INDEX_CAT_NAME],
         $this->itemsArray[$idItem][SearchModel::ITEMS_ARRAY_INDEX_CAT_ID]);
      return $link;
   }

   /**
    * Factore metoda pro nasatvení parametrů hledacího modulu
    * @param string $searchString -- hledaný řetězec
    * @param int $page -- číslo stránky
    */
   public static function factory($searchString, $page = 1) {
      self::setSearchString(urldecode($searchString));
      self::$searchPage = $page;
   }

   /**
    * Metoda nastavuje řetězec pro vyhledávání
    * @param string $string -- hledaný řetězec
    */
   public static function setSearchString($string){
      $string = preg_replace('/\+/i', ' +' , $string);
      self::$searchString = $string;
   }

   /**
    * Metoda vrací pole s výsledky hledání
    * @return array
    */
   public static function getResults() {
      self::sortResults();
      //odstranění znaků
      $removeChars = array('+', '-', '"', '(', ')', '~', '*');
      $searchString = str_replace($removeChars, ' ', self::$searchString);
      $searchArray = array();
      $searchArray = preg_split('/[ ]+/', str_replace($removeChars, ' ', self::$searchString));
      // Odstranění prázdných prvků v poli
      foreach ($searchArray as $key => $val) {
         if($val == null OR $val == ''){
            unset ($searchArray[$key]);
         }
      }
      $textHelper = new TextHelper();
      $stringLenght = AppCore::sysConfig()->getOptionValue('result_lenght', 'search');
      $delta = 20;
      $highLightTag = AppCore::sysConfig()->getOptionValue('highlight_tag', 'search');

//      procházení výsledků
      foreach (self::$searchResults as $resultKey => $result) {
         // odstranění html tagů
         $text = strip_tags($result[self::RESULT_INDEX_TEXT]);
         //Jestli se bude vůbec ořezávat
         if(strlen($text) > $stringLenght){
            reset($searchArray);
            $pos = stripos($text, current($searchArray));
            $start = $pos-(($stringLenght)/2);
            if($start < 0){
               $start = 0;
            } else if(($stringLenght+$delta+$start) > strlen($text)){
               $start = strlen($text)-$stringLenght+$delta;
            }
            $text = mb_substr($text, $start, $delta+$stringLenght);
            $text = $textHelper->truncate($text, $stringLenght, '...');
            if($start > 0) {
               $text = '...'.$text;
            }
         }
         foreach ($searchArray as $key => $val) {
            $text = preg_replace('/('.$val.')/i', '<'.$highLightTag.'>\\1</'.$highLightTag.'>', $text);
         }
         self::$searchResults[$resultKey][self::RESULT_INDEX_TEXT] = $text;
      }
      return self::$searchResults;
   }

   /**
    * Metoda seřadí výsledky podle relevance
    */
   private static function sortResults(){
      /**
       * Privátní metoda pro porovnávání relevance
       * @param array $a -- pole výsledku a
       * @param array $b -- pole výsledku a
       */
      function cmpResult($a, $b) {
         return strcmp($b[Search::RESULT_INDEX_RELEVANCE], $a[Search::RESULT_INDEX_RELEVANCE]);
      }
      usort(self::$searchResults, 'cmpResult');
   }

   /**
    * Metoda vrací počet výsledků hledání
    * @return integer -- počet výsledků
    */
   public static function getNumResults() {
      return count(self::$searchResults);
   }
}
?>