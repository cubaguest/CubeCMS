<?php
/**
 * Třída pro práci s odkazy.
 * Třída pro tvorbu a práci s odkazy aplikace, umožňuje jejich pohodlnou 
 * tvorbu a validaci, popřípadě změnu jednotlivých parametrů. Umožňuje také 
 * přímé přesměrování na zvolený (vytvořený) odkaz pomocí klauzule redirect.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.5.0 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro práci s odkazy
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
	 * Oddělovatč mezi názvem článku a id článku
	 * @var char
	 */
	const URL_SEP_ARTICLE_ID = '-';
	/**
	 *
	 * Oddělovatč mezi názvem kategorie a id kateogire
	 * @var char
	 */
	const URL_SEP_CAT_ID = '-';

	/**
	 * Názec souboru pro stahování dat
	 * @var string
	 */
	const DOWNLOAD_FILE = 'download.php';
	
	/**
	 * Název parametru v url, přo který se přenáší název stahovanéh souboru
	 * @var string
	 */
	const DOWNLOAD_FILE_FILE_PARAM = 'file';
	
	/**
	 * Název parametru v url, přo který se přenáší adresář stahovanéh souboru
	 * @var string
	 */
	const DOWNLOAD_FILE_DIR_PARAM = 'url';

	/**
	 * Název položky pole se jménem
	 * @var string
	 */
	const LINK_ARRAY_ITEM_NAME = 'name';

	/**
	 * Název položky pole s idčkem
	 * @var string
	 */
	const LINK_ARRAY_ITEM_ID = 'id';

	/**
	 * Proměná s typem přenosového protokolu
	 * @var string
	 */
	private static $user_transfer_protocol = null;
	
	/**
	 * Proměná s jazykovou mutací
	 * @var string
	 */
	private static $selectedLang = null;
	
	/**
	 * Proměnná s kategorií
	 * @var string
	 */
	private static $selectedCategory = array();

	/**
	 * Proměnná s cestou
	 * @var string
	 */
	private static $selectedRoute = null;

	/**
	 * Proměnná s kategorií
	 * @var string
	 */
	private static $selectedAricle = array();

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
	 * Proměná s vybraným typem média
	 * @var string
	 */
	private static $selectedMedia = null;
	
	/**
	 * Proměnná s názvem jazyka
	 * @var string
	 */
	private $lang = null;
	
	/**
	 * Proměnná s názvem kategorie
	 * @var string
	 */
	private $category = array();
	
	/**
	 * Proměná s názvem článku (article key)
	 * @var string
	 */
	private $article = array();

	/**
	 * Proměná s názvem akce (action)
	 * @var string
	 */
	private $action = null;
	
	/**
	 * Proměná s názvem média
	 * @var string
	 */
	private $mediaType = null;

	/**
	 * Proměná s názvem souboru
	 * @var string
	 */
	private $file = null;
	
	/**
	 * Jestli se má tvořit nový odkaz od začátku
	 * @var boolean
	 */
//	private $clearLink = false;

	/**
	 * Jestli se má tvořit nový odkaz k rootu webu
	 * @var boolean
	 */
	private $onlyWebRoot = false;
	
	/**
	 * Jesti se má vrátit relativní cesta nebo celá
	 * @var boolean
	 */
//	private $relativePath = false;
	
	/**
	 * Pole s ostatními parametry v url
	 * @var array
	 */
	private $paramsArray = array();
	
	/**
	 * Proměná obsahuje jestli se má dané stránka načíst znova
	 * @var boolean
	 */
//	private $reloadPage = false;
	
	/**
	 * Chráněné parametry systému
	 * @var array
	 */
//	private static $protectedParams = array("lang", "action", "category", "article", "media");
	
	/**
	 * Konstruktor nastaví základní adresy a přenosový protokol
	 *
	 * @param boolean -- (option)true pokud má být vrácen čistý link jenom s kategorií(pokud je vybrána) a jazykem
	 * @param boolean -- (option)true pokud má být vráce naprosto čistý link (web root)
	 * 
	 */
	function __construct($clear = false, $onlyWebRoot = false) {
		$this->clearLink = $clear;	
		$this->onlyWebRoot = $onlyWebRoot;
		$this->_init();
	}
	
	/*
	 * STATICKÉ METODY
	 */
	
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
			return UrlRequest::TRANSFER_PROTOCOL;
		} else {
			return self::$user_transfer_protocol;
		}
	}
	
	/**
	 * Metoda vrací adresu k web aplikaci
	 */
//	public static function getMainWebDir() {
//		return self::getTransferProtocol().self::$serverName.self::$webUrl.self::COOL_URL_SEPARATOR;
//	}
	
	/**
	 * Metoda načte parametry v url do pole
	 */
	private static function _infilParams()
	{
//		$queryString = $_SERVER["QUERY_STRING"];
//		if($queryString != null){
//			$tmpParamsArray = array();
//			$tmpParamsArray = explode(self::URL_PARAMETRES_SEPARATOR_IN_URL, $queryString);
//			foreach ($tmpParamsArray as $fullParam) {
//				$tmpParam = explode(self::URL_SEP_PARAM_VALUE, $fullParam);
//
//				if(!in_array($tmpParam[0], self::$protectedParams) AND isset($tmpParam[0]) AND isset($tmpParam[1])){
//					self::$selectedParams[$tmpParam[0]] = $tmpParam[1];
//				}
//			}
//		}
	}
	
	/**
	 * Metoda nastaví článek
	 * @param string -- článek
	 */
	public static function setUrlCategory($cat, $id) {
		self::$selectedCategory[self::LINK_ARRAY_ITEM_NAME] = $cat;
		self::$selectedCategory[self::LINK_ARRAY_ITEM_ID] = $id;
	}

	/**
	 * Metoda nastaví článek
	 * @param string -- článek
	 */
	public static function setUrlRoute($route) {
		self::$selectedRoute = $route;
	}

	/**
	 * Metoda nastaví článek
	 * @param string -- článek
	 */
	public static function setUrlArticle($article) {
		self::$selectedAricle = $article;
	}

	/**
	 * Metoda nastaví článek
	 * @param string -- článek
	 */
	public static function setUrlLang($lang) {
		self::$selectedLang = $lang;
	}

	/**
	 * Metoda nastaví akci
	 * @param string -- článek
	 */
	public static function setUrlAction($action) {
		self::$selectedAction = $action;
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
	public function category($catName = null, $catId = null){
		if($catName != null AND $catId != null){
			$this->category[self::LINK_ARRAY_ITEM_NAME] = $catName;
			$this->category[self::LINK_ARRAY_ITEM_ID] = $catId;
		}
		return $this;
	}
	
	/**
	 * Metoda nastavuje název article
	 * @param string -- jméno článku
	 * 
	 * @return Links -- objket Links
	 */
	public function article($article = null, $idArticle = null){

//		Pokud je zadáno id, je přenášen article podle id článku a né podle urlklíče
		if($article != null AND $idArticle != null AND is_numeric($idArticle)){
			$this->article = $this->createArticleLink($article, $idArticle);
		} else {
			$this->article = $article;
		}
		return $this;
	}

	/**
	 * Metoda nastavuje typ media
	 * @param string -- jméno media
	 * 
	 * @return Links -- objket Links
	 */
	public function media($media = null){
		$this->mediaType = $media;
		return $this;
	}

	/**
	 * Metoda nastavuje parametr nazev epluginu
	 * @param string -- jméno epluginu
	 * 
	 * @return Links -- objket Links
	 */
	public function file($file = null){
		$this->file = $file;
		return $this;
	}

	/**
	 * Metoda nastavuje název action
	 * @param string -- jméno akce (action např. edit, show atd.)
	 * 
	 * @return Links -- objket Links
	 */
	public function action($action = null){
		$this->action = $action;
		return $this;
	}

	/**
	 * Metoda nastavuje název lokalizace
	 * @param string -- jméno jazyka (action např. cs, en, de atd.)
	 * 
	 * @return Links -- objket Links
	 */
	public function lang($lang = null){
		$this->lang = $lang;
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
	public function param($param, $value, $thisParamOnly = false)
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
		if(!$this->onlyWebRoot){
			$this->lang = self::$selectedLang;
			$this->category = self::$selectedCategory;
			if(!$this->clearLink){
				$this->article = self::$selectedAricle;
				$this->action = self::$selectedAction;
				$this->paramsArray = self::$selectedParams;
				$this->mediaType = self::$selectedMedia;
			}
		}
	}
	
	/**
	 * Metoda složí dohromady parametry pro url
	 */
	private function completeUrlParams()
	{
		return http_build_query($this->paramsArray);
	}
		
	/**
	 * Metoda vrací kategorii
	 * @return string -- název kategorie (klíč)
	 */
	private function getCategory() {
		if(!empty ($this->category)){
			$categoryName = $this->category[self::LINK_ARRAY_ITEM_NAME];

			$utf = new TextCtrlHelper();
			$categoryName = $utf->utf2ascii($categoryName);

			return $categoryName.self::URL_SEP_CAT_ID
				.$this->category[self::LINK_ARRAY_ITEM_ID].UrlRequest::URL_SEPARATOR;
		}
//		else {
//			if(!$this->trueClearLink){
//				return Category::getUrlKey().UrlRequest::URL_SEPARATOR;
//			} else {
				return null;
//			}
//		}
	}
	
	/**
	 * Metoda vrací část článek (article) pro url
	 * @param string -- článek (article)
	 */
	private function getArticle() {
		if($this->article != null){
			return $this->article.UrlRequest::URL_SEPARATOR;
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
			return $this->action.UrlRequest::URL_SEPARATOR;
		} else {
			return null;
		}
	}

	/**
	 * Metoda vrací část lang pro url
	 * @param string -- lang
	 */
	private function getLang() {
		if($this->lang != null){
			return $this->lang.UrlRequest::URL_SEPARATOR;
		} else {
			return null;
		}
	}

	/**
	 * Metoda doplní část media
	 */
	private function getMedia() {
		if($this->mediaType != null){
//			$this->param(self::GET_MEDIA, $this->mediaType);
		}
	}

	/**
	 * Metoda doplní část s názvem souboru
	 */
	private function getFile() {
		if($this->file != null){
			return $this->file;
		} else {
			return null;
		}
	}

	/**
	 * Metoda vytvoří podobu článek pro url adresu s integorovaným id
	 * @param string $article -- název článku
	 * @param integer $idArticle -- id článku
	 */
	private function createArticleLink($article, $idArticle) {
		$textHelp = new TextCtrlHelper();

		$articleAscii = $textHelp->utf2ascii($article);

		$newArticle = $articleAscii.self::URL_SEP_ARTICLE_ID.$idArticle;
		return $newArticle;
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
//    	if(!$this->relativePath){
			$returnString = UrlRequest::getBaseWebDir();
//    	} else {
//    		$returnString = './';
//    	}
        
    	if($this->lang != null){
        	$returnString.=$this->getLang();
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
        
        if($this->getFile() != null){
        	$returnString.=$this->getFile();
        }
        
        $this->getMedia();
        
        if(!empty($this->paramsArray)){
        	$returnString.=self::URL_SEPARATOR_LINK_PARAMS.$this->completeUrlParams();
        }
    	
    	return $returnString;
    }
    
    /**
     * Metoda vrací link pro stažení souboru pomocí specílního dwsouboru
     * @param string -- cesta ks ouboru
     * @param string -- název souboru
     */
    public function getLinkToDownloadFile($dir, $file) {
		 $dwLink = UrlRequest::getBaseWebDir().self::DOWNLOAD_FILE.self::URL_SEPARATOR_LINK_PARAMS.
    		self::DOWNLOAD_FILE_DIR_PARAM.self::URL_SEP_PARAM_VALUE.urlencode($dir).
    		self::URL_PARAMETRES_SEPARATOR.self::DOWNLOAD_FILE_FILE_PARAM.self::URL_SEP_PARAM_VALUE.
    		$file;
    		
    	return $dwLink;	
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