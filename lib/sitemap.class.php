<?php
/**
 * Třída pro generování sitemapy.
 * Třída generuje mapu webu v požadovaném formátu. Podporovány jsou formát pro 
 * google (seznam) a yahoo. Je většinou volána zvlášť a využívá soubor sitemap.class.php 
 * v modulech pro generování pro generování data poslední změny atd.
 * 
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: sitemap.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
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
	 * Proměná obsahuje objekt modulu
	 * @var Module
	 */
	private $module = null;
	
	/**
	 * Proměná obsahuje DbConnector
	 * @var DbInterface
	 */
	private $db = null;
	
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
	 * Proměná obsahuje pole s položkama
	 * @var array
	 */
	private static $items = array();
		
	/**
	 * Konstruktor -- vytvoří prostředí pro práci se sitemap
	 *
	 * @param Module -- objekt modulu
	 */
	function __construct(Module $module, Links $link, $changefreq, $priority) {
		$this->module = $module;
		$this->db = AppCore::getDbConnector();
		$this->link = $link;
		
		$this->changeFreq = $changefreq;
		$this->priority = $priority;
	}
	
	/**
	 * Metoda spouští proceduru pro přidávání položek do sitemap
	 *
	 */
	public function run() {
		$this->addItem($this->getLink(), filectime('./index.php'), self::SITEMAP_SITE_CHANGE_YEARLY, self::SITEMAP_SITE_DEFAULT_PRIORITY);
	}
	
	/**
	 * Metoda přidává položku do siteamp
	 *
	 * @param string -- odkaz
	 * @param integer -- čas poslední změny (timestamp)
	 * @param string -- četnost změny (kostanta SITEMAP_SITE_CHANGE_...)
	 * @param float -- priorita (0 - 1)
	 */
	public function addItem($url, $lastChange, $frequency = null ,$priority = null) {
		$date = new DateTime(date(DATE_ISO8601,$lastChange));
		
		if($frequency == null){
			$frequency = $this->changeFreq;
		}
		if($priority == null){
			$priority = $this->priority;
		}
		
		array_push(self::$items, array('loc' => $url,
									   'lastmod' => $date->format(DATE_ISO8601),
									   'changefreq' => $frequency,
									   'priority'=>$priority));
	}
	
	/**
	 * Meotda vrací objekt modulu
	 *
	 * @return Module -- objekt modulu
	 */
	public function getModule() {
		return $this->module;
	}
	
	/**
	 * Metoda vrací objekt pro přístup k db
	 *
	 * @return DbInterface -- objekt databáze
	 */
	public function getDb() {
		return $this->db;
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
	public static function generateMap($mapType = 'google'){
		if($mapType == 'google'){
			ob_start();
			header('Content-type: text/xml');
			echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
//			echo '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'."\n";
			echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
			foreach (self::$items as $i)
			{
				echo '<url>';
				foreach ($i as $index => $_i)
				{
					if (!$_i) continue;
					echo "<$index>" . self::codeXML($_i) . "</$index>\n";
				}
				echo "</url>\n";
			}
			echo '</urlset>';
			return ob_get_clean();
		} 
//		Pro yahoo
		else if($mapType == 'yahoo'){
			ob_start();
			header('Content-type: text/plain');
			foreach (self::$items as $i)
			{
				echo $i['loc'] . "\n";
			}
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
	
}
?>