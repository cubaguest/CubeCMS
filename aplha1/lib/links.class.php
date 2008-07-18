<?php
/**
 * Třída pro obsluhu odkazů
 *
 */

class Links {
	
	
	
//	Základní proměné s parametry původní adresy
//	private $transferProtocol;
//	private $serverName;
//	private $scriptName;
//	private $requestUri;
//	private $queryString;
//	private $separator = "&amp;";

//	Parametry předávané v enginu
//	private $params = array();
//	private static $lang = null;
//	private $category = null;
//	private $action = null;
//	private $article = null;

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
		
//		$this->phpSelf=$_SERVER["PHP_SELF"];
//		$this->scriptName=$_SERVER["PHP_SELF"];
//		$this->serverName=$_SERVER["SERVER_NAME"];
//		$this->requestUri=urldecode($_SERVER["REQUEST_URI"]);
//		$this->queryString=urldecode($_SERVER["QUERY_STRING"]);
		//$this->queryString=str_replace("&and;", "&", $this->queryString);
//		$this->transferProtocol=$protocol;
//		$this->action = $_GET["action"];
//		$this->article = $_GET["article"];
//		$this->lang = $defaultLang;
//		if($infillParams){
//			$this->infillParamsArray();
//		}
//		Links::$lang = null;

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
		
		$tmpParamsArray = array();
		$tmpParamsArray = explode(self::URL_PARAMETRES_SEPARATOR_IN_URL, $queryString);
		foreach ($tmpParamsArray as $fullParam) {
			$tmpParam = explode(self::URL_SEP_PARAM_VALUE, $fullParam);
			
			if(!in_array($tmpParam[0], self::$protectedParams)){
				self::$selectedParams[$tmpParam[0]] = $tmpParam[1];
			}
		}
	}
	
	
	/*
	 * VEŘEJNÉ METODY
	 */
	
	/**
	 * Metoda nastavuje název kategorie (klíč)
	 * @param string -- klíč kategorie
	 */
	public function category($catName){
		$this->category = $catName;
		return $this;
	}
	
	/**
	 * Metoda nastavuje název article
	 * @param string -- jméno článku
	 */
	public function article($article = null){
		$this->article = $article;
		return $this;
	}

	/**
	 * Metoda nastavuje název action
	 * @param string -- jméno akce (action např. edit, show atd.)
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
	 * 
	 *
	 */
	public function param($param = null, $value, $thisParamOnly = false)
	{
		if($thisParamOnly OR $param == null){
			$this->paramsArray = null;
		}
		if($param != null){
			$this->paramsArray[$param] = $value;
		}
		
		return $this;
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
		return http_build_query($this->paramsArray, '', self::URL_PARAMETRES_SEPARATOR);
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
	
	
	
	
	/**
	 * //TODO Staré!! patří přefiltrovat, co bude a co nebude potřeba
	 */

    /**
	 * Privatni funkce nastavi vytvoří základní pole s prvky url adresy
	 * ("nazev" => "hodnota")
	 * @deprecated
	 */
//	private function infillParamsArray()
//	{
//		$paramsArray = array();
//		$paramsAndKeysArray = array();
//		$tmpArray = array();
//
//		if($this->queryString != null){
//			$paramsAndKeysArray = explode("&", $this->queryString);
//
//			foreach ($paramsAndKeysArray as $keysWithValues) {
//
//				$tmpArray = explode("=", $keysWithValues);
//				if($tmpArray[0] != "category" AND $tmpArray[0] != "action" AND $tmpArray[0] != "article"AND $tmpArray[0] != "lang"){
//					$this->params[$tmpArray[0]] = $tmpArray[1];
//				} else if($tmpArray[0] == "categroy"){
//					$this->category = $tmpArray[1];
//				} else if($tmpArray[0] == "action"){
//					$this->action = $tmpArray[1];
//				} else if($tmpArray[0] == "article"){
//					$this->article = $tmpArray[1];
//				} else if($tmpArray[0] == "lang"){
//
//				}
//			}
//		}
//
////		echo "<pre>";
////		print_r($this->params);
////		echo "</pre>";
//	}
//
//	/**
//	 * Funkce vymění zadané parametry za nové
//	 *
//	 * @param array -- pole parametrů a hodnot, které se mají vyměnit ("key" => "value")
//	 * @param bool -- jestli se má vrítit celý link i s adresou serveru a protokolu (výchozí: false)
//	 * @return string -- url adresa se změněnými parametry
//	 * @deprecated
//	 */
//	function changeParams($newParamsArray, $returnlUrl = true)
//	{
//		foreach ($newParamsArray as $newParamKey => $newParamValue) {
//			$this->changeParam($newParamKey, $newParamValue, false);
//		}
//
//		if($returnlUrl){
//			return $this->completeUrl();
//		} else {
//			return true;
//		}
//	}
//
//	/**
//	 * Funkce změní zadaný parametr na novou hodnotu
//	 *
//	 * @param string -- parametr, který se má upravit
//	 * @param string -- nová hodnota parametru
//	 * @deprecated
//	 */
//	function changeParam($param, $value, $retunValue = true)
//	{
//		if($retunValue){
//			$tmpArray = $this->params;
//		}
//
//		$this->params[$param] = $value;
//
////		echo "<pre>";
////		print_r($this->params);
////		echo "</pre>";
//
//		if($retunValue){
//			$return = $this->completeUrl();
//		} else {
//			$return = true;
//		}
//
//		if($retunValue){
//			$this->params = $tmpArray;
//		}
//
//		return $return;
//
//	}
//
//	/**
//	 * Vnitřní funkce vygeneruje adresu z pole
//	 *
//	 * @return string -- vygenerovaná url adresa
//	 */
//	private function completeUrl()
//	{
//		$url = $this->getOnlyCategoryUrl(false);
//		if($this->article != null){
//			$url.=$this->article."/";
//		}
//		if($this->action != null){
//			$url.=$this->action."/";
//		}
//
//		$url = ereg_replace("//$", "/", $url);
//		if(!empty($this->params)){
////		if($this->params != null){
//			$url.= $this->createParamsForUrl();
//		}
//
//		return $url;
//	}
//
//	/**
//	 * Funkce vraci adresu server az k hlavnimu souboru, ale bez hlavniho souboru
//	 * tedy http://www.test.cz/admin/
//	 *
//	 * @return string -- cesta k hlavnimu scriptu
//	 */
////	function getMainWebDir()
////	{
////		$positionLastChar=strrpos($this->scriptName, "/");
////		$url=substr($this->scriptName, 0, $positionLastChar);
////		$result = $this->transferProtocol."://".$this->serverName.$url;
////
////		return $result;
////	}
//
//	/**
//	 * Funkce vrací adresu k hlavnímu souboru webu
//	 *
//	 * @return string -- cesta k index souboru
//	 */
//	function getMainWebDirWithIndexFile()
//	{
//		$result = $this->transferProtocol."://".$this->serverName.$this->scriptName;
//
//		return $result;
//	}
//
//
//	/**
//	 * Funkce nastaví jazyk v url
//	 *
//	 * @param string -- jazyková zkratka
//	 */
//	static function setLang($lang)
//	{
//		Links::$lang = $lang;
//	}
//
//	/**
//	 * Funkce vrací jazyk v url
//	 *
//	 * @return string -- jazyková zkratka
//	 */
//	static function getLang()
//	{
//		return Links::$lang;
//	}
//
//	/**
//	 * Funkce nastaví klíč kategorie v url
//	 *
//	 * @param string -- klíč kategorie (etc. o-nas)
//	 */
//	function setCategoryKey($category)
//	{
//		$this->category = $category;
//	}
//
//	/**
//	 * Funkce vrací url adresu se změněnou kategorií
//	 *
//	 * @param string -- klíč kategorie
//	 * @return string -- url ke kategorii se změněným klíčem
//	 */
//	function getWithChangeCategoryKey($categoryKey)
//	{
//		if(Links::$lang == null){
//			$return = $this->getMainWebDir()."/".$categoryKey."/";
//		} else {
//			$return = $this->getMainWebDir()."/".Links::$lang."/".$categoryKey."/";
//		}
//		return $return;
//	}
//
//	/**
//	 * Funkce vrací pouze url ke kategorii s jazykem
//	 *
//	 * @param bool -- jestli se mají obrazit i ostatní parametry
//	 * @return string -- pouze url kategorie a jazyk
//	 */
//	function getOnlyCategoryUrl($withParams = false)
//	{
//		if(Links::$lang == null){
//			$return = $this->getMainWebDir()."/".$this->category."/";
//		} else {
//			$return = $this->getMainWebDir()."/".Links::$lang."/".$this->category."/";
//		}
//
//		if($withParams AND !empty($this->params)){
//			$return .= $this->createParamsForUrl();
//		}
//		return $return;
//	}
//
//	/**
//	 * Funkce vrací odkaz se zadaným klíčem článku
//	 *
//	 * @param string -- klíč článku
//	 * @param bool -- jestli se mají obrazit i ostatní parametry
//	 * @return string -- url s klíčem
//	 */
//	function getWithArticleKey($articleKey, $withParams = false)
//	{
//		$return = $this->getOnlyCategoryUrl(false).$articleKey."/";
//
//		if($withParams AND !empty($this->params)){
//			$return .= $this->createParamsForUrl();
//		}
//
//		return $return;
//	}
//
//	/**
//	 * Funkce vrací odkaz se zadaným klíčem článku
//	 *
//	 * @param string -- klíč článku
//	 * @param bool -- jestli se mají obrazit i ostatní parametry
//	 * @return string -- url s klíčem
//	 */
//	function getWithAction($action,$withParams = false)
//	{
//		$action = substr($action, 0, 5);
//		$return = $this->getOnlyCategoryUrl(false).$action."/";
//
//		if($withParams AND !empty($this->params)){
//			$return .= $this->createParamsForUrl();
//		}
//
//		return $return;
//	}
//
//	/**
//	 * Funkce vrací odkaz se zadaným klíčem článku a akcí
//	 *
//	 * @param string -- klíč článku
//	 * @param string -- název akce která se má předat
//	 * @param bool -- jestli se mají obrazit i ostatní parametry
//	 * @return string -- url s klíčem a akcí
//	 */
//	function getWithArticleKeyAndAction($articleKey, $actionName, $withParams = false)
//	{
//		$return = $this->getWithArticleKey($articleKey,false).$actionName."/";
//		if($withParams AND !empty($this->params)){
//			$return .= $this->createParamsForUrl();
//		}
//		return $return;
//	}
//
//	/**
//	 * Funkce vrací odkaz se zadaným klíčem článku, akcí a zadané parametry
//	 *
//	 * @param string -- klíč článku
//	 * @param string -- název akce která se má předat
//	 * @param array -- pole s parametry, které se mají přidat
//	 * @param bool -- jestli se mají obrazit i ostatní parametry
//	 * @return string -- url s klíčem a akcí
//	 */
//	function getWithArticleKeyAndActionAndParams($articleKey, $actionName, $paramsArray, $withParams = false)
//	{
//		$tmpParams = $this->params;
//
//		$this->changeParams($paramsArray, false);
//
//
//
//		$return = $this->getWithArticleKey($articleKey,false).$actionName."/".$this->createParamsForUrl();
//
//		if($withParams AND !empty($this->params)){
//			$return .= $this->createParamsForUrl();
//		}
//
//		$this->params = $tmpParams;
//		return $return;
//	}
//
//	/**
//	 * Vrací současnou url adresu (kompletní)
//	 *
//	 * @return string -- url adresa
//	 */
//	function getThisAddress()
//	{
//		return $this->completeUrl();
//	}
//
//	/**
//	 * Vnitřní funkce, vygeneruje ostatní url parametry, které nejsou přenášeny přes adresářovou strukturu v url
//	 *
//	 * @return string -- část url s parametrama
//	 */
//	private function createParamsForUrl()
//	{
//		$urlParams = "?".http_build_query($this->params,'',$this->separator);
//
//		return $urlParams;
//	}
//
//	/**
//	 * Funkce vyvola presmerovani stranky na pozadovany odkaz
//	 * Pokud neni odkaz definovan, vyvola se znovunacteni zobrazene stranky
//	 * prikazem "header" a pote UKONCI script!!
//	 *
//	 * @param string -- adresa odkazu, kam se ma presmerovat (nepovine)
//	 * @param bool -- odstrani akci z url
//	 * @param bool -- odstrani clanek, akci a ostatni parametry z url
//	 * @param bool -- odtsrani ostatni parametry z url
//	 * @param bool -- odstrani kategroii, clanek, akci a ostatni parametry z url
//	 */
//	function getSendHeader($link=null, $withoutAction = false, $withoutArticle=false, $withoutParams = false, $withoutCategory = false){
//
//		if($withoutCategory){
//			$this->category = null;
////			$withoutArticle = true;
//		}
//		if($withoutArticle){
//			$this->article = null;
////			$withoutParams = true;
////			$withoutAction = true;
//		}
//		if($withoutAction){
//			$this->action = null;
//		}
//		if($withoutParams){
//			$this->params = array();
//		}
//
//		if ($link == null){
//			$link = $this->getThisAddress();
//
//			header("location: ".$link);
//		} else {
////			pokud je na začátku přenosový protokol
//			if(substr($link, 0, strlen($this->transferProtocol)) == $this->transferProtocol){
//				header("location: ".$link);
//			} else {
//				header("location: ".$this->transferProtocol."://".$link);
//			}
//		}
//		exit;
//	}
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