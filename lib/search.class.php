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
    * Řetězec, který se hledá
    * @var string
    */
   private static $searchString = null;

   /**
    * Která stránka vyhledávání je zobrazena (zatím nepoužito)
    * @var integer
    */
   private static $searchPage = 1;

   /**
    * Pole s výsledky hledání
    * @var array
    */
   private static $searchResults = array();

   /**
    * Metoda proo hledání v modulu
    */
   public function runSearch(){}


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
}
?>
