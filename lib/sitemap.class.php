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
	const SITEMAP_SITE_DEFAULT_PRIORITY = 0.1;
	
	/**
	 * Objekt s odkazem na kategorii
	 * @var Links
	 */
	private $link = null;
	
	/**
	 * Frekvence změn na stránce
	 * @var string
	 */
	private $changeFreq = self::SITEMAP_SITE_CHANGE_YEARLY;
	
	/**
	 * Priorita stránky v mapě
	 * @var float
	 */
	private $priority = self::SITEMAP_SITE_DEFAULT_PRIORITY;

   /**
    * Pole s položkami
    * @var array
    */
   private $itemsCurrentArray = array();

	/**
	 * Proměná obsahuje pole s položkama
	 * @var array
	 */
	private static $items = array();
		
	/**
	 * Konstruktor -- vytvoří prostředí pro práci se sitemap
	 *
	 * @param Module -- objekt modulu
	 */
	function __construct(Links $link, $changefreq = self::SITEMAP_SITE_CHANGE_YEARLY, $priority = 0.5) {
      $this->link = $link;
		$this->changeFreq = $changefreq;
		$this->priority = $priority;
	}

   /**
    * Při zrušení objektu zařadíme aktuální položky do celkového pole
    */
   public function  __destruct() {
      self::$items = array_merge(self::$items, $this->itemsCurrentArray);
   }

	/**
	 * Metoda spouští proceduru pro přidávání položek do sitemap
	 *
	 */
	public function run() {
		$this->addItem($this->getLink(), null, $this->changeFreq, $this->priority);
      filectime('./index.php');
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
	public function addItem($url, $name, $lastChange = null, $frequency = null ,$priority = null) {
		// pokud je datum v budoucnosti nastavím aktuální
      if($lastChange > time()){
         $lastChange = time();
      }
      if($lastChange != null){
         $date = new DateTime(date(DATE_ISO8601,(int)$lastChange));
         $lastChange = $date->format('c');
      }
		if($frequency == null){
			$frequency = $this->changeFreq;
		}
		if($priority == null){
			$priority = $this->priority;
		}
   	array_push($this->itemsCurrentArray, array('loc' => (string)$url,
									   'lastmod' => $lastChange,
									   'changefreq' => $frequency,
									   'priority'=>$priority,
                              'name' => $name));
	}

   /**
	 * Metoda přidává položku kategorie do siteampy
	 *
	 * @param integer -- čas poslední změny (timestamp)
    * @param float -- (option) o kolik se má snížit priorita článků
	 */
   public function addCategoryItem($lastChange, $priorityForArticleDown = 0.1) {
      // pokud je datum v budoucnosti nastavím aktuální
      if($lastChange > time()){
         $lastChange = time();
      }
      $date = new DateTime(date(DATE_ISO8601,$lastChange));
      $lastChange = $date->format('c');
      array_push(self::$items, array('loc' => (string)$this->getLink(),
									   'lastmod' => $lastChange,
									   'changefreq' => $this->changeFreq,
									   'priority'=>$this->priority));
      $this->priority = $this->priority-$priorityForArticleDown;
   }

	/**
	 * Meotda vrací objekt modulu
	 *
	 * @return Module -- objekt modulu
	 */
	public function getModule() {
      return AppCore::getSelectedModule();
	}
	
	/**
	 * Metoda vrací objekt pro přístup k db
	 *
	 * @return DbInterface -- objekt databáze
	 */
	public function getDb() {
      return AppCore::getDbConnector();
	}
	
	/**
	 * Metoda vrací objekt odkazu
	 * @return Links -- objekt odkazu
	 */
	public function getLink() {
		return $this->link;
	}
	
	/**
	 * Metoda vygeneruje mapu webu
	 *
	 * @param string -- pro jaký vahledávač má být mapa generována
	 * @return string -- bufer s vygenerovanou mapou
	 */
	public static function generateMap($mapType = 'xml'){
      // Pro Google a Seznam
      if($mapType == 'xml'){
			ob_start();
			header('Content-type: text/xml');
			echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
//			echo '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'."\n";
			echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
			foreach (self::$items as $i){
				echo '<url>'."\n";
				foreach ($i as $index => $_i){
					if (!$_i OR $index == 'name') continue;
               // float čísla s tečkou a stejnou přesností
               if(is_float($_i)){
                  $_i = sprintf("%1.4F", $_i);
               }
               echo "<$index>" . self::codeXML(trim((string)$_i)) . "</$index>\n";
				}
				echo "</url>\n";
			}
			echo '</urlset>';
         ob_flush();
			return ob_get_clean();
		} 
//		Pro yahoo
		else if($mapType == 'txt'){
			ob_start();
			header('Content-type: text/plain');
			foreach (self::$items as $i)
			{
				echo $i['loc'] . "\n";
			}
         ob_flush();
			return ob_get_clean();
		}
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
   public function getCurrentMapArray() {
      return $this->itemsCurrentArray;
   }
}
?>