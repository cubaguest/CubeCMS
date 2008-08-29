<?php
/**
 * Třída pro obsluhu odkazů
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	Links class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: links.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída pro práci s odkazy
 */

class Links {
	/**
	 * Oddělovač prametrů odkazu
	 * @var string
	 */
	const URL_PARAMETRES_SEPARATOR = '&amp;';

	/**
	 * Oddělovač prametrů odkazu a samotného odkazu
	 * @var string
	 */
	const URL_SEPARATOR_LINK_PARAMS = '?';
	
	/**
	 * Oddělovač parametrů v url
	 * @var string
	 */
	const URL_PARAMETRES_SEPARATOR_IN_URL = '&';
	
	/**
	 * Oddělovač parametr/hodnota
	 * @var string
	 */
	const URL_SEP_PARAM_VALUE = '=';
	
	/**
	 * Přenosový protokol
	 * @var string
	 */
	const TRANSFER_PROTOCOL = 'http://';
	
	/**
	 * $GET proměná s jazykovou mutací
	 * @var string
	 */
	const GET_LANG = 'lang';

	/**
	 * $GET proměná s kategorií
	 * @var string
	 */
	const GET_CATEGORY = 'category';
	
	/**
	 * $GET proměná s článkem (article)
	 * @var string
	 */
	const GET_ARTICLE = 'article';
	
	/**
	 * $GET proměná s akcí (action)
	 * @var string
	 */
	const GET_ACTION = 'action';
	
	/**
	 * Oddělovač prametrů hlavní url
	 * @var string
	 */
	const COOL_URL_SEPARATOR = '/';
	
	/**
	 * Proměná s typem přenosového protokolu
	 * @var string
	 */
	private static $user_transfer_protocol = null;
	
	/**
	 * Proměná s jazykovou mutací
	 * @var string
	 */
	private static $selectedlang = null;
	
	/**
	 * Proměnná s kategorií
	 * @var string
	 */
	private static $selectedCategory = null;

	/**
	 * Proměnná s kategorií
	 * @var string
	 */
	private static $selectedAricle = null;

	/**
	 * Proměnná s kategorií
	 * @var string
	 */
	private static $selectedAction = null;
	
	/**
	 * Proměnná s kategorií
	 * @var string
	 */
	private static $selectedParams = array();
	
	/**
	 * Adresa serveru (např. www.seznam.cz)
	 * @var string
	 */
	private static $serverName = null;
	
	/**
	 * Jméno scriptu
	 * @var string
	 */
	private static $scriptName = null;
	
	/**
	 * root Adresa webu
	 * @var string
	 */ 
	private static $webUrl = null;
	
	/**
	 * Proměnná s názvem jazyka
	 * @var string
	 */
	private $lang = null;
	
	/**
	 * Proměnná s názvem kategorie
	 * @var string
	 */
	private $category = null;
	
	/**
	 * Proměná s názvem článku (article key)
	 * @var string
	 */
	private $article = null;

	/**
	 * Proměná s názvem akce (action)
	 * @var string
	 */
	private $action = null;
	
	/**
	 * Jestli se má tvořit nový odkaz od začátku
	 * @var boolean
	 */
	private $clearLink = false;
	
	/**
	 * Pole s ostatními parametry v url
	 * @var array
	 */
	private $paramsArray = array();
	
	/**
	 * Proměná obsahuje jestli se má dané stránka načíst znova
	 * @var boolean
	 */
	private $reloadPage = false;
	
	/**
	 * Chráněné parametry systému
	 * @var array
	 */
	private static $protectedParams = array("lang", "action", "category", "article");
	
	/**
	 * Konstruktor nastaví základní adresy a přenosový protokol
	 *
	 * @param string -- přenosový protokol (výchozí: http)
	 */
	function __construct($clear = false) {
		$this->clearLink = $clear;	

		$this->_init();
	}
	
	/*
	 * STATICKÉ METODY
	 */
	
	/**
	 * Metoda nastavuje základní proměnné
	 */
	public static function factory(){
//		nastavení jazyku
		if(isset($_GET[self::GET_LANG])){
			self::$selectedlang = $_GET[self::GET_LANG];
		}

//		nastavení kategorie
		if(isset($_GET[self::GET_CATEGORY])){
			self::$selectedCategory = $_GET[self::GET_CATEGORY];
		}

//		nastavení článku
		if(isset($_GET[self::GET_ARTICLE])){
			self::$selectedAricle = $_GET[self::GET_ARTICLE];
		}

//		nastavení akce
		if(isset($_GET[self::GET_ACTION])){
			self::$selectedAction = $_GET[self::GET_ACTION];
		}
		
//TODO	načtení ostatních parametrů 

//		Název serveru
		self::$serverName = $_SERVER["SERVER_NAME"];
		
//		Název scriptu
		self::$scriptName = $_SERVER["SCRIPT_NAME"];

//		nastavení adresáře webu
		$positionLastChar=strrpos(self::$scriptName, "/");
		self::$webUrl=substr(self::$scriptName, 0, $positionLastChar);
		
//		načtení parametrů
		self::_infilParams();
	}
	
	/**
	 * metoda nastavuje transportní protokol
	 * @param string -- přenosový protokol (např. http://)
	 */
	public static function setTransferProtocol($protocol) {
		self::$user_transfer_protocol = $protocol;
	}
	
	/**
	 * Metoda vrací přenosový protokol
	 * @return string -- přenosový protokol
	 */
	public static function getTransferProtocol() {
		if(self::$user_transfer_protocol == null){
			return self::TRANSFER_PROTOCOL;
		} else {
			return self::$user_transfer_protocol;
		}
	}
	
	/**
	 * Metoda vrací adresu k web aplikaci
	 */
	public static function getMainWebDir() {
		return self::getTransferProtocol().self::$serverName.self::$webUrl.self::COOL_URL_SEPARATOR;
	}
	
	/**
	 * Metoda načte parametry v url do pole
	 */
	private static function _infilParams()
	{
		$queryString = $_SERVER["QUERY_STRING"];
		
		if($queryString != null){
			$tmpParamsArray = array();
			$tmpParamsArray = explode(self::URL_PARAMETRES_SEPARATOR_IN_URL, $queryString);
			foreach ($tmpParamsArray as $fullParam) {
				$tmpParam = explode(self::URL_SEP_PARAM_VALUE, $fullParam);
					
				if(!in_array($tmpParam[0], self::$protectedParams)){
					self::$selectedParams[$tmpParam[0]] = $tmpParam[1];
				}
			}
		}
	}
	
	
	/*
	 * VEŘEJNÉ METODY
	 */
	
	/**
	 * Metoda nastavuje název kategorie (klíč)
	 * @param string -- klíč kategorie
	 * 
	 * @return Links -- objket Links
	 */
	public function category($catName = null){
		$this->category = $catName;
		return $this;
	}
	
	/**
	 * Metoda nastavuje název article
	 * @param string -- jméno článku
	 * 
	 * @return Links -- objket Links
	 */
	public function article($article = null){
		$this->article = $article;
		return $this;
	}

	/**
	 * Metoda nastavuje název action
	 * @param string -- jméno akce (action např. edit, show atd.)
	 * 
	 * @return Links -- objket Links
	 */
	public function action($action = null){
//		if($action == null){
//			$this->action = self::$selectedAction;
//		} else {
			$this->action = $action;
//		}
		return $this;
	}
	
	/**
	 * Metoda nastavuje prametry do url (je li jich více)
	 * @param array -- pole parametrů (název=>hodnota)
	 * @param boolean -- jestli se mají použít i ostatní parametry
	 * @param array -- pole parametrů, které se mají odstranit //TODO zatím neimplementováno!!
	 * 
	 * @return Links -- objket Links
	 * TODO není implementována, možná nebude třeba
	 */
	public function params($params = null, $thisParamOnly = false) {
		
		if($thisParamOnly OR $params == null){
			$this->paramsArray = null;
		}
		
		if($params != null){
			foreach ($params as $param => $value) {
				$this->param($param, $value);
			}
		}
		

		return $this;
	}
	
	/**
	 * Metoda nastavuje parametr do url
	 * @param string -- název parametru
	 * @param string -- hodnota parametru
	 *
	 * @return Links -- objket Links
	 */
	public function param($param = null, $value, $thisParamOnly = false)
	{
		//TODO dodělat ošetření nevalidních znaků
		if($thisParamOnly OR $param == null){
			$this->paramsArray = null;
		}
		if($param != null){
			$this->paramsArray[$param] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Metoda odstraňuje zadaný parametr z url
	 * 
	 * @param string -- název parametru, který se odebere
	 */
	public function withoutParam($param){
	
		if(isset($this->paramsArray[$param])){
			unset($this->paramsArray[$param]);
		}
		
		return $this;
	}
	
	
	/**
	 * Metoda nastavuje znovunahrání stránky
	 * @param string -- externí odkaz na který se má přesměrovat (option)
	 */
	public function reload($link = null){
		if($link == null){
			header("location: ".$this);
		} else {
			header("location: ".(string)$link);
		}
		exit();
	}
	
	
	
	/*
	 * PRIVÁTNÍ METODY
	 */

	/**
	 * Metoda inicializuje odkazy
	 *
	 */
	private function _init()
	{
		$this->lang = self::$selectedlang;
		if(!$this->clearLink){
			$this->category = self::$selectedCategory;
			$this->article = self::$selectedAricle;
			$this->action = self::$selectedAction;
			$this->paramsArray = self::$selectedParams;
		}
	}
	
	/**
	 * Metoda složí dohromady parametry pro url
	 */
	private function completeUrlParams()
	{
//		return http_build_query($this->paramsArray, '', self::URL_PARAMETRES_SEPARATOR);
		return http_build_query($this->paramsArray);
	}
		
	/**
	 * Metoda vrací kategorii
	 * @return string -- název kategorie (klíč)
	 */
	private function getCategory() {
		if($this->category != null){
			return $this->category.self::COOL_URL_SEPARATOR;
		} else {
//			return self::$selectedCategory.self::COOL_URL_SEPARATOR;
			return null;
		}
	}
	
	/**
	 * Metoda vrací část článek (article) pro url
	 * @param string -- článek (article)
	 */
	private function getArticle() {
		if($this->article != null){
			return $this->article.self::COOL_URL_SEPARATOR;
		} else {
			return null;
		}
	}
	
	/**
	 * Metoda vrací část akce (action) pro url
	 * @param string -- akce (action)
	 */
	private function getAction() {
		if($this->action != null){
			return $this->action.self::COOL_URL_SEPARATOR;
		} else {
			return null;
		}
	}
	
	/*
	 * MAGICKÉ METODY
	 */
	
   /**
     * Metoda převede objekt na řetězec
     *
     * @return string -- objekt jako řetězec
     */
    public function __toString()
    {
    	$returnString = self::getMainWebDir();
        if($this->lang != null){
        	$returnString.=$this->lang.self::COOL_URL_SEPARATOR;
        }
        if($this->getCategory() != null){
        	$returnString.=$this->getCategory();
        }
        if($this->getArticle() != null){
        	$returnString.=$this->getArticle();
        }
        if($this->getAction() != null){
        	$returnString.=$this->getAction();
        }
        
        if(!empty($this->paramsArray)){
        	$returnString.=self::URL_SEPARATOR_LINK_PARAMS.$this->completeUrlParams();
        }
    	
    	return $returnString;
    }
}
//echo("PHP_SELF: ".$_SERVER["PHP_SELF"]."<br>");
//echo("SERVER_NAME: ".$_SERVER["SERVER_NAME"]."<br>");
//echo("QUERY_STRING: ".$_SERVER["QUERY_STRING"]."<br>");
//echo("DOCUMENT_ROOT: ".$_SERVER["DOCUMENT_ROOT"]."<br>");
//echo("SCRIPT_FILENAME: ".$_SERVER["SCRIPT_FILENAME"]."<br>");
//echo("SCRIPT_NAME: ".$_SERVER["SCRIPT_NAME"]."<br>");
//echo("REQUEST_URI: ".$_SERVER["REQUEST_URI"]."<br>");
//
//PHP_SELF: /skrznaskrz/admin/index.php
//SERVER_NAME: dev.vypecky.info
//QUERY_STRING: category=10
//DOCUMENT_ROOT: /var/www/dev/htdocs/
//SCRIPT_FILENAME: /var/www/dev/htdocs/skrznaskrz/admin/index.php
//SCRIPT_NAME: /skrznaskrz/admin/index.php
//REQUEST_URI: /skrznaskrz/admin/index.php?category=10
//
//PHP_SELF: /newwebvypecky/index.php
//SERVER_NAME: dev.vypecky.info
//QUERY_STRING: category=9&action=show
//DOCUMENT_ROOT: /var/www/dev/htdocs/
//SCRIPT_FILENAME: /var/www/dev/htdocs/newwebvypecky/index.php
//SCRIPT_NAME: /newwebvypecky/index.php
//REQUEST_URI: /newwebvypecky/index.php?category=9&action=show
//REQUEST_URI: /index.php?cat=2
//	+--http://sprava.vypecky.info/index.php?cat=2
//REQUEST_URI: /
//	+--http://sprava.vypecky.info/

?>