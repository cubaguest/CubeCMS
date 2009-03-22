<?php
/**
 * Třída pro práci s hledáním v článcích a kategoriích, každý modul ji
 * implementuje zvlášť, ale obsahuje vlastní metody pro zjednodušení přístupu
 * a vytvoření jednotlivých implementací
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.9.3 $Revision: $
 * @author        $Author: $ $Date:$
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro vyhledávání v modulech
 */
class Search {
   /**
    * Indexy pole s výsledky hledání
    */
   const RESULT_IDEX_CATEGORY    = 'category';
   const RESULT_IDEX_ARTICLE     = 'article';
   const RESULT_IDEX_URL         = 'url';
   const RESULT_IDEX_TEXT        = 'text';

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
    * @param string $article -- (option) název článku
    */
   public function addResult($category, $url, $text, $article = null) {
      $resultArr = array(
         self::RESULT_IDEX_CATEGORY => $category,
         self::RESULT_IDEX_URL => (string)$url,
         self::RESULT_IDEX_TEXT => $text,
         self::RESULT_IDEX_ARTICLE => $article);

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
    * Factore metoda pro nasatvení parametrů hledacího modulu
    * @param string $searchString -- hledaný řetězec
    * @param int $page -- číslo stránky
    */
   public static function factory($searchString, $page = 1) {
      self::setSearchString($searchString);
      self::$searchPage = $page;
   }

   /**
    * Metoda nastavuje řetězec pro vyhledávání
    * @param string $string -- hledaný řetězec
    */
   public static function setSearchString($string){
      self::$searchString = addslashes($string);
   }

   /**
    * Metoda vrací pole s výsledky hledání
    * @return array
    */
   public static function getResults() {
      return self::$searchResults;
   }

}
?>
