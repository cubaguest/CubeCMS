<?php
/**
 * Description of UrlRequest
 * Třída slouží pro parsování a obsluhu požadavků v URL adrese.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.5.0 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract		Třída pro obsluhu a UrlReqestu
 */
class UrlRequest {
	/**
	 * Název parametru s typem media
	 * @var string
	 */
	const PARAM_MEDIA_TYPE_PREFIX = 'media';

	/**
	 * Typ media pro web
	 * @var string
	 */
	const MEDIA_TYPE_WWW	= 'www';

	/**
	 * Typ media pro web
	 * @var string
	 */
	const MEDIA_TYPE_SITEMAP	= 'sitemap';

	/**
	 * Typ media pro web
	 * @var string
	 */
	const MEDIA_TYPE_PRINT	= 'print';

	/**
	 * Přenosový protokol
	 * @var string
	 */
	const TRANSFER_PROTOCOL = 'http://';

	/**
	 * $GET proměná s název epluginu
	 * @var string
	 */
	const GET_EPLUGIN_NAME = 'eplugin';

	/**
	 * $GET proměná s název jspluginu
	 * @var string
	 */
	const GET_JSPLUGIN_NAME = 'jsplugin';

	/**
	 * Oddělovač prametrů hlavní url
	 * @var string
	 */
	const URL_SEPARATOR = '/';

	/**
	 * Obsahuje typ media
	 * @var array
	 */
	private static $media = null;

	/**
	 * Obsahuje současnou url adresu
	 * @var string
	 */
	private static $currentUrl = null;

	/**
	 * Základní URL adresa aplikace
	 * @var string
	 */
	private static $baseWebUrl = null;

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
	 * Objekt zvoleného modulu
	 * @var Module
	 */
	private $module = null;

	/**
	 * Objekt akce modulu
	 * @var Action
	 */
	private $moduleAction = null;

	/**
	 * Objekt cest modulu
	 * @var Routes
	 */
	private $moduleRoutes = null;

	/**
	 * Konstruktor
	 */
	public function  __construct(Action $action, Routes $routes) {
		$this->module = AppCore::getSelectedModule();
		$this->moduleAction = $action;
		$this->moduleRoutes = $routes;
	}

	/**
	 * Metoda inicializuje požadavky v URL
	 */
	public static function factory() {
//		Vytvoření url
		self::createUrl();

//		Parsování url
		self::parseUrl();
	}

	/**
	 * Metoda parsuje celou url do pole s jednotlivými proměnými
	 */
	private static function parseUrl() {
//		Rozdělíme řetězec podle separátorů
		$urlItems = array();

//		Odstranění posledního lomítka
		$url = ereg_replace("^(.*)/$", "\\1", self::$currentUrl);

		$urlItems = explode(URL_SEPARATOR, $url);

		reset($urlItems);
		$matches = array();

//		Kontrola jazyka
		if(eregi('^([a-zA-Z]{2})$', pos($urlItems), $matches)){
			Locale::setLang($matches[1]);
			Links::setUrlLang($matches[1]);
			next($urlItems);
		}


//		Kontrola jestli je zadána kategorie FORMAT: nazev-id
		if(eregi('^([a-zA-Z0-9\-]+)-([0-9]+)$', pos($urlItems), $matches)){
			Category::setCurrentCategory($matches[2]);
			Links::setUrlCategory($matches[1], $matches[2]);
			next($urlItems);
		}

		//		Načetní článku FORMAT: "nazev-id" a popřpadě cesty FORMAT: "nazev-id"
		if(eregi('^([a-zA-Z0-9\-]+)-([a-z]?[0-9]+)$', pos($urlItems))){
			//		posun prvku
//			next($urlItems);
			$matches = array();

			$articleRegepx = '^([a-zA-Z0-9\-]+)'.Article::ARTICLE_URL_SEPARATOR.'([0-9]+)$';
			//		Kontrola jesli je zadán článek (je první před routou, i když je v url dále)FORMAT: nazev-id
			if(next($urlItems) AND eregi($articleRegepx, pos($urlItems), $matches)){
				//			Je zadána cesta i článek
				//			Načtení článku
				Article::setCurrentArticle($matches[2]);
				Links::setUrlArticle($matches[0]);

				//			načtení routy FORMAT: nazev-id nebo nazev-{char}id
				$matches = array();
				prev($urlItems); // posun zpět na předchozí prvek
				$regex = '^([a-zA-Z0-9\-]+)'.Routes::ROUTE_URL_ID_SEPARATOR.'([a-z]?[0-9]+)$';
				if(eregi($regex, pos($urlItems), $matches)){
					Routes::setCurrentRoute($matches[2]);
					Links::setUrlRoute($matches[0]);
				}
			} else {

				//			načtení pouze článku
//				prev($urlItems); // posun zpět na předchozí prvek
				if(eregi('^([a-zA-Z0-9\-]+)-([0-9]+)$', pos($urlItems), $matches)){
					Article::setCurrentArticle($matches[2]);
					Links::setUrlArticle($matches[0]);
				}
			}
			next($urlItems);
		}

//		Načtení akce FORMAT: nazev_{action name (např. char)}-id_item
		$matches = array();
		$regex = '^([a-zA-Z0-9\-]+)'.Action::ACTION_URL_LABEL_TYPE_SEP.'([a-zA-Z]+)'.Action::ACTION_URL_TYPE_ID_SEP.'([0-9]+)$';
		if(eregi($regex, pos($urlItems), $matches)){

			Action::setAction($matches[2], $matches[3]);
			Links::setUrlAction($matches[0]);
			next($urlItems);
		}

//		Načtení typu media FORMAT: media{typ media např www,print}
		$matches = array();
		$expresion = '^'.self::PARAM_MEDIA_TYPE_PREFIX.'([a-zA-Z]+)$';
		if(eregi($expresion, pos($urlItems), $matches)){
			self::$media = $matches[1];
			next($urlItems);
		}

//		echo "tady".pos($urlItems);
//
//		if(eregi('^([a-zA-Z0-9\-]+)-([a-z]?[0-9]+)$', pos($urlItems), $matches)){
//			self::$route[self::URL_ITEM_FULL_NAME] = $matches[0];
//			self::$route[self::URL_ITEM_NAME] = $matches[1];
//			self::$route[self::URL_ITEM_ID] = $matches[2];
//		}

// echo pos($urlItems);
//		echo '<pre>';
//		print_r($urlItems);
//		echo '</pre>';
	}

	/**
	 * Metoda vytvoří url a uloží ji do $currentUrl
	 */
	private static function createUrl() {
//		echo '<pre>';
//		echo $_SERVER['PHP_SELF'].'<br>';
//		echo $_SERVER['DOCUMENT_ROOT'].'<br>';
//		echo $_SERVER['REQUEST_URI'].'<br>';
//		echo $_SERVER['SCRIPT_NAME'].'<br>';
//		echo AppCore::getAppWebDir().'<br>';
//		echo '</pre>';

		$fullUrl = $_SERVER['REQUEST_URI'];
		self::$scriptName = $_SERVER["SCRIPT_NAME"];
		self::$serverName = $_SERVER["SERVER_NAME"];

//		Najdeme co je cesta k aplikaci a co je předaná url
		self::$currentUrl = substr($fullUrl, strpos(self::$scriptName, AppCore::APP_MAIN_FILE));

//		Vytvoříme základní URL cestu k aplikaci
		$positionLastChar=strrpos(self::$scriptName, self::URL_SEPARATOR);
		self::$baseWebUrl=self::TRANSFER_PROTOCOL.self::$serverName.substr(self::$scriptName, 0, $positionLastChar).self::URL_SEPARATOR;
	}


	/**
	 * Metoda vrací základní URL cestu k aplikaci
	 * @return string
	 */
	public static function getBaseWebDir() {
		return self::$baseWebUrl;
	}

	/**
	 * Metoda vrací typ média pro načtenou stránku
	 * @return string
	 */
	public static function getMediaType() {
		return self::$media;
	}

	/**
	 * Metoda zevolí typ kontroleru modulu
	 */
	public function choseController() {
//		Vyvoříme objekt článku kvůli zjišťování přítomnosti článku
		$article = new Article();

//		pokud není akce
		if(!$this->moduleAction->isAction()){
//			pokud je vybrán článek
			if($article->isArticle()){
				$action = strtolower($this->moduleAction->getDefaultArticleAction());
			}
//			Pokud není vybrán článek
			else {
				$action = strtolower(AppCore::MODULE_MAIN_CONTROLLER_PREFIX);
			}
		}
//		Pokud je vybrána akce
		else {
			$action = $this->moduleAction->getSelectedAction();
		}

//		Přiřazení routy
		if($this->moduleRoutes->isRoute()){
			$action = ucfirst($action);
			$action = $this->moduleRoutes->getRoute().$action;
		}

		return $action;
	}

	/**
	 * Metoda zjišťuje jestli byl nastaven index na eplugin
	 *
	 * @return boolean -- true pokud se má zpracovávat eplugin
	 */
	public static function isEplugin() {
		if(isset($_GET[self::GET_EPLUGIN_NAME])){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Metoda vrací název zvoleného epluginu
	 *
	 * @return string -- název epluginu
	 */
	public static function getSelEpluginName() {
		return rawurldecode($_GET[self::GET_EPLUGIN_NAME]);
	}

	/**
	 * Metoda zjišťuje jestli byl nastaven index na jsplugin
	 *
	 * @return boolean -- true pokud se má zpracovávat jsplugin
	 */
	public static function isJsplugin() {
		if(isset($_GET[self::GET_JSPLUGIN_NAME])){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Metoda vrací název zvoleného jspluginu
	 *
	 * @return string -- název jspluginu
	 */
	public static function getSelJspluginName() {
		return rawurldecode($_GET[self::GET_JSPLUGIN_NAME]);
	}
}
?>
