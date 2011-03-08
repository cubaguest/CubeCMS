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
   const R_I_NAME       = 'name';
   const R_I_LINK       = 'link';
   const R_I_TEXT       = 'text';
   const R_I_RELEVATION = 'relevation';
   const R_I_CAT_LINK   = 'cat_link';
   const R_I_CAT_NAME   = 'cat_name';
   const R_I_WEB_LINK   = 'web_link';
   const R_I_WEB_NAME   = 'web_name';
   
   /**
    * Sloupce s relevací u záznamů
    */
   const COLUMN_RELEVATION = 'relevation';
   
   /**
    * Řetězec, který se hledá
    * @var string
    */
   protected static $searchString = null;
   
   /**
    * Pole s hledanými slovy
    * @var array
    */
   protected static $searchWords = array();
   
   /**
    * Pole s výsledky hledání
    * @var array
    */
   protected static $searchResults = array();
   
   /**
    * Systémový objekt modulu
    * @var Category
    */
   protected $category = null;
   
   /**
    * Objekt odkazu dané kategorie
    * @var Url_Link_Module
    */
   private $link = null;
   
   /**
    * Délka řetězce ve výsledku
    */
   private static $resultLen = VVE_SEARCH_RESULT_LENGHT;

   private $baseRelev = 0;

   /**
    * Konstruktor
    */
   public function  __construct(Category $category = null, $baseRelevance = 0) {
      if($category !== null) {
         $this->category = $category;
         $this->link = new Url_Link_Module(true);
         $this->link->category($category->getUrlKey());
         
         $routesClass = ucfirst($category->getModule()->getName())."_Routes";
         $this->link->setModuleRoutes(new $routesClass(null));
      } else {
         $this->link = new Url_Link(true);
         $this->link->category();
      }
      $this->baseRelev = $baseRelevance;
   }
   
   /**
    * Metoda proo hledání v modulu
    */
   public function runSearch() {}
   
   /**
    * MEtoda přidává výsledek do pole výsledků
    * @param string $url -- odkaz na výsledek
    * @param string $text -- text výsledku
    * @param float $relevance -- relevance výsledku
    * @param string $article -- (option) název článku
    */
   public function addResult($name, $url, $text, $relevance = 0.1, $catName = null, $catLink = null) {
      if($catName === null) $catName = $this->getCategory()->getName();
      if($catLink === null) $catLink = $this->link()->clear();
      $resultArr = array(
              self::R_I_NAME => $name,
              self::R_I_LINK => (string)$url,
              self::R_I_TEXT => $this->createShortText((string)$text),
              self::R_I_RELEVATION => $relevance+$this->baseRelev,
              self::R_I_CAT_NAME => $catName,
              self::R_I_CAT_LINK => (string)$catLink,
              self::R_I_WEB_LINK => (string)$this->link()->clear(true),
              self::R_I_WEB_NAME => VVE_WEB_NAME);
      array_push(self::$searchResults, $resultArr);
   }

   /**
    * Metoda provede ořez a zkrácení textu
    * @param string $text
    * @return string
    */
   private function createShortText($text) {
      $text = strip_tags($text);
      $text = preg_replace("/[[:space:]]+/", ' ', $text);
      // ořez na délku
      $textLen = mb_strlen($text);
      $preStr = $postStr = '...';
      if($textLen > self::$resultLen) {
         // nalezení hledaného slova
         foreach (self::$searchWords as $w){
            $posOfFirst = mb_stripos($text, $w);
            if($posOfFirst !== false) break;
         }
         // start pozice textu
         $startOfString = (int)($posOfFirst - round((self::$resultLen)/2));// 15 protože bude konečný ořez
         if($startOfString <= 0) {
            $startOfString = 0;
            $preStr = null;
         } else {
            $startOfString = mb_stripos($text, ' ', $startOfString)+1;
         }
         if($startOfString+self::$resultLen > $textLen) {
            $end = $textLen;
         } else {
            $end = $startOfString+self::$resultLen;
         }
         $endOfString = @mb_stripos($text, ' ', $end);

         if($endOfString == 1 OR $endOfString === false) {
            $endOfString = $textLen;
            $postStr = null;
            //znovu najdeme mezeru
            $startOfString = mb_stripos($text, ' ', $textLen-self::$resultLen-15)+1;
         }
         $text = $preStr.mb_substr($text, $startOfString, $endOfString-$startOfString).$postStr;
      }
      return $text;
   }
   
   /**
    * Metoda vrací hledaný řetězec
    * @return string
    */
   public function getSearchString() {
      return self::$searchString;
   }
   
   /**
    * Metoda vrací objekt kategorie
    * @return Category
    */
   public function category() {
      return $this->category;
   }

   /**
    * Metoda vrací objekt kategorie
    * @return Category
    * @deprecated see category()
    */
   public function getCategory() {
      return $this->category;
   }
   
   /**
    * Metoda vrací odkaz na vytvořenou kategorii
    * @return Url_Link_Module
    */
   public function link() {
      return clone $this->link;
   }
   
   /**
    * Factore metoda pro nasatvení parametrů hledacího modulu
    * @param string $searchString -- hledaný řetězec
    * @param int $page -- číslo stránky
    */
   public static function factory($searchString) {
      // tady přijde které PDO je použito -- kvůli FullTEXT MySQL je tohle:
      $searchString = preg_replace('/\+/i', ' +' , $searchString);
      self::$searchString = $searchString;
      // vytvoření pole s hledanými prvky
      $removeChars = array('+', '-', '"', '(', ')', '~', '*');
      self::$searchWords = preg_split('/[ ]+/', trim(str_replace($removeChars, ' ', $searchString)));
   }
   
   /**
    * Metoda vrací pole s výsledky hledání
    * @return array
    */
   public static function getResults() {
      return self::$searchResults;
   }

   /**
    * Metoda nasatví pole s výsledky hledání -- pro kešování
    * @param array $results -- pole s výsledky
    */
   public static function setResults($results) {
      self::$searchResults = $results;
   }

   /**
    * Metoda přidá výsledek hledání -- musí být ve správném formátu !!!!
    * @param array $result -- pole s výsledky
    */
   public static function addExternalResult($result) {
      array_push(self::$searchResults, $result);
   }
   
   /**
    * Metoda seřadí výsledky podle relevance
    */
   public static function sortResults($results) {
      /**
       * Privátní metoda pro porovnávání relevance
       * @param array $a -- pole výsledku a
       * @param array $b -- pole výsledku a
       */
      function cmpResult($a, $b) {
         return strcmp($b[Search::R_I_RELEVATION], $a[Search::R_I_RELEVATION]);
      }
      usort($results, 'cmpResult');
      return $results;
   }
   
   public static function prepareResultsForView($results) {
      
      $searchWordsRegexps = array();
      foreach (self::$searchWords as $word) {
         if(strlen($word) <= 3) continue;
         $word = ltrim($word);
         // normální slovo
         array_push($searchWordsRegexps, '/('.$word.')/i');
         // bez diakritiky
         $wordAscii = vve_to_ascii($word);
         if(!in_array('/('.$wordAscii.')/i', $searchWordsRegexps)) {
            array_push($searchWordsRegexps, '/('.$wordAscii.')/i');
         }
      }
      // zvýraznění
      function prepareResults(&$text, $key, $words) {
         $text[Search::R_I_TEXT] = preg_replace($words['regexps'], "<".VVE_SEARCH_HIGHLIGHT_TAG.">\\1</".VVE_SEARCH_HIGHLIGHT_TAG.">", $text[Search::R_I_TEXT]);
      }
      array_walk($results, 'prepareResults', array('words' => self::$searchWords ,'regexps' => $searchWordsRegexps));
      return $results;
   }
   
   /**
    * Metoda vrací počet výsledků hledání
    * @return integer -- počet výsledků
    */
   public static function getNumResults() {
      return count(self::$searchResults);
   }

   /**
    *Metoda nastaví délku výsledku
    * @param integer $len -- délka
    */
   public static function setResultLenght($len){
      self::$resultLen = $len;
   }
}
?>