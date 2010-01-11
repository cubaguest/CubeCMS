<?php
/**
 * Třída pro generování sitemapy.
 * Třída generuje mapu webu v požadovaném formátu. Podporovány jsou formát pro
 * google (seznam) a yahoo. Je většinou volána zvlášť a využívá soubor sitemap.class.php
 * v modulech pro generování pro generování data poslední změny atd.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro tvorbu sitemap
 */

class SiteMap {
/**
 * Proměné s názvy četností změn
 * @var string
 */
   const SITEMAP_SITE_CHANGE_ALWAYS 	= 'always';
   const SITEMAP_SITE_CHANGE_HOURLY 	= 'hourly';
   const SITEMAP_SITE_CHANGE_DAILY 	= 'daily';
   const SITEMAP_SITE_CHANGE_WEEKLY 	= 'weekly';
   const SITEMAP_SITE_CHANGE_MONTHLY 	= 'monthly';
   const SITEMAP_SITE_CHANGE_YEARLY 	= 'yearly';
   const SITEMAP_SITE_CHANGE_NEVER 	= 'never';

   /**
    * Výchozí priorita obsahu
    * @var float
    */
   const SITEMAP_SITE_DEFAULT_PRIORITY = 0.5;

   /**
    * Pole s položkami
    * @var array
    */
   private $items = array();

   /**
    * Pole se všemi položkami
    * @var array
    */
   private static $itemsAll = array();

   /**
    * Pole s parametry kategorie
    * @var array
    */
   private $catItem = array();

   /**
    * Proměná obsahuje objekt odkazu
    * @var Url_Link_Module
    */
   private $link = null;

   /**
    * Objekt kategorie
    * @var Category
    */
   private $category = null;

   /**
    * Konstruktor -- vytvoří prostředí pro práci se sitemap
    *
    * @param Module -- objekt modulu
    */
   function __construct(Category $category, Routes $routes) {
      $this->category = $category;

      $link = new Url_Link_Module();
      $link->setModuleRoutes($routes);
      $link->category($this->category()->getUrlKey());
      $this->link = $link;
   }

   /**
    * Metoda vrací objekt kategorie
    * @return Category
    */
   final public function category() {
      return $this->category;
   }

   /**
    * Metoda spouští proceduru pro přidávání položek do sitemap
    *
    */
   public function run() {
      
   }

   /**
    * Metoda přidává položku do siteamp
    *
    * @param string -- odkaz
    * @param string -- název
    * @param integer -- čas poslední změny (timestamp)
    * @param string -- četnost změny (kostanta SITEMAP_SITE_CHANGE_...)
    * @param float -- priorita (0 - 1)
    */
   public function addItem($url, $name, DateTime $lastChange = null, $frequency = null ,$priorityDown = 1.0000) {
   // pokud je datum v budoucnosti nastavím aktuální
      $item = array('loc' => (string)$url,'name' => $name);
//      if($lastChange > time()) {
//         $lastChange = time();
//      }

      if($lastChange !== null|false) {
//         $date = new DateTime(date(DATE_ISO8601,(int)$lastChange));
//         $lastChange = $date->format('c');
         $item['lastmod'] = $lastChange->format('c');
      }

      if($frequency != null) {
         $item['changefreq'] = $frequency;
      }
      $item['priority'] = $this->category()->getCatDataObj()->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY}-$priorityDown;
      if($item['priority'] < 0) $item['priority'] = 0;
      array_push($this->items, $item);
   }

   /**
    * Metoda přidává položku kategorie do siteampy
    *
    * @param integer -- čas poslední změny (timestamp)
    * @param float -- (option) o kolik se má snížit priorita článků
    */
   public function addCategoryItem(DateTime $lastChange) {
      $this->catItem = array('loc' => (string)$this->link(),
          'lastmod' => $lastChange->format('c'),
          'changefreq' => $this->category()->getCatDataObj()->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_FREQ},
          'priority'=>$this->category()->getCatDataObj()->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY},
          'name' => $this->category()->getCatDataObj()->{Model_Category::COLUMN_CAT_LABEL});
   }

   /**
    * Metoda vrací objekt odkazu
    * @return Links -- objekt odkazu
    */
   public function link() {
      return clone $this->link;
   }

   /**
    * Metoda vygeneruje mapu webu
    *
    * @param string -- pro jaký vahledávač má být mapa generována
    * @return string -- bufer s vygenerovanou mapou
    */
   public static function generateMap($mapType = 'xml') {
   // Pro Google a Seznam
      if($mapType == 'xml') {
         echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
         //			echo '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'."\n";
         echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
         foreach (self::$itemsAll as $item) {
         // kategorie
            echo '<url>'."\n";
            foreach ($item['cat'] as $index => $_i) {
               if (!$_i OR $index == 'name') continue;
               // float čísla s tečkou a stejnou přesností
               if(is_float($_i) OR is_numeric($_i)) {
                  $_i = sprintf("%1.4F", $_i);
               }
               echo "<$index>" . self::codeXML(trim((string)$_i)) . "</$index>\n";
            }
            echo "</url>\n";
            // články
            if(isset ($item['items']) AND !empty ($item['items'])) {
               foreach ($item['items'] as $it) {
                  echo '<url>'."\n";
                  foreach ($it as $index => $_i) {
                     if (!$_i OR $index == 'name') continue;
                     // float čísla s tečkou a stejnou přesností
                     if(is_float($_i)) {
                        $_i = sprintf("%1.4F", $_i);
                     }
                     echo "<$index>" . self::codeXML(trim((string)$_i)) . "</$index>\n";
                  }
                  echo "</url>\n";
               }
            }
         }
         echo '</urlset>';
      } else if($mapType == 'txt') {
         //		Pro yahoo
            foreach (self::$itemsAll as $item) {
               echo $item['cat']['loc'] . "\n";
               // články
               if(isset ($item['items']) AND !empty ($item['items'])) {
                  foreach ($item['items'] as $it) {
                     echo $it['loc'] . "\n";
                  }
               }
            }
         }
   }

   /**
    * Metoda přidá hlavní stránku
    */
   public static function addMainPage() {
      array_push(self::$itemsAll, array('cat' => array('loc' => (string)Url_Request::getBaseWebDir())));
   }

   /**
    * Metoda zakoduje řetězec do xml
    *
    * @param string -- řetězec
    */
   private static function codeXML($str) {
      $translations = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
      foreach ($translations as $key => $value) {
         $translations[$key] = '&#' . ord($key) . ':';
      }
      $translations[chr(38)] = '&';
      return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/","&#38;",strtr($str,$translations));
   }

   /**
    * Metoda vrací pole článků
    */
   public function createMapArray() {
      $retArr = array();
      $retArr['cat'] = $this->catItem;
      $retArr['items'] = $this->items;
      array_push(self::$itemsAll, $retArr);
      return $retArr;
   }

   public function  __destruct() {
      $this->createMapArray();
   }

   /**
    * Metoda vrací pole článků
    */
   public static function getCurrentItemsArray() {
      return self::$itemsAll;
   }
}
?>
