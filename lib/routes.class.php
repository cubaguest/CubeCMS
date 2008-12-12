<?php
/**
 * Třída pro obsluhu cest(routes).
 * Třida je určena k zjišťování a volby cesty pro kontroler a viewer. 
 * Také slouží pro generování vlastních cest jednotlivých modulů.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: routes.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída pro obsluhu cest modulu
 */

class Routes {
	/**
	 * Prefix pro id cesty u předdefinovaných cest
	 * @var char
	 */
	const PRETEFINED_ROUTES_ID_PREFIX = 'p';

	/**
	 * Oddělovač mezi cestou a článkem (route-article)
	 * @var string
	 */
	const ROUTE_URL_ID_SEPARATOR = '-';

	/**
	 * Prvek s názvem cesty pro kontroler
	 * @var string
	 */
	const ROUTE_NAME = 'name';

	/**
	 * Prvek s popisem cesty pro jazyky
	 * @var string
	 */
	const ROUTE_LABEl = 'label';

	/**
	 * Název routy kontroleru při použití výchozí routy
	 * @var string
	 */
	const ROUTE_NOTPREDEFINED_CONTROLER = 'rdefault';

	/**
	 * ID vybrané cesty
	 * @var integer
	 */
	private static $currentRouteId = null;

	/**
	 * Jestli je cesta předdefinována nebo ne
	 * @var boolean
	 */
	private static $currentRouteIsPredefined = false;

	/**
	 * Pole s cestami
	 * @var array
	 */
	private $routes = array();
	
	/**
	 * Proměná se zvolenou cestou
	 * @var string
	 */
//	private static $route = null;

	/**
	 * Proměná obsahuje id routy
	 * @var integer
	 */
//	private static $routeId = 0;

	/**
	 * Proměné obsahuje, jesli byla nastavena cesta
	 * @var boolean
	 */
//	private static $isRoute = false;

	/**
	 * Proměná s article, která se vrací zpět
	 * @var string
	 */
//	private $article = null;

	/**
	 * Konstruktor třídy
	 *
	 * @param Article -- objekt článku (article)
	 */
	function __construct() {
//		$this->article = $article;
		$this->initRoutes();
	}

	/**
	 * Nastavuje aktuální cestu
	 * @param string $id -- id cesty (může být s prefixem)
	 */
	public static function setCurrentRoute($id){
		$matches = array();
		$pattern = '^'.self::PRETEFINED_ROUTES_ID_PREFIX.'([0-9]+)';
		//		Pokud je předdefinovaná cesta
		if(eregi($pattern, $id, $matches)){
			self::$currentRouteId = $matches[1];
			self::$currentRouteIsPredefined = true;
		}
		//		je použita výchozí cesta
		else if(eregi('([0-9]+)', $id, $matches)) {
			self::$currentRouteId = $matches[1];
			self::$currentRouteIsPredefined = false;
		}
	}

	/**
	 * Metoda, která nastavuje cesty
	 *
	 */
	function initRoutes(){}
	
	/**
	 * Metoda vrací objekt k článku (article)
	 * @return Article -- objekt článku
	 */
	private function getArticle() {
		return $this->article;
	}
	
	/**
	 * Metoda přidává cestu do seznamu cest
	 * @param string -- název cesty
	 */
	final public function addRoute($id, $routeName, $label) {
		$route = array($id => array(self::ROUTE_NAME => $routeName, self::ROUTE_LABEl => $label));
		array_push($this->routes, $route);
	}
	
	/**
	 * Metoda vrací použitou cestu
	 * @return string -- název cesty
	 */
	final public function getRoute() {
		if(self::$currentRouteIsPredefined){
			return $this->routes[self::$currentRouteId][self::ROUTE_NAME];
		} else {
			return self::ROUTE_NOTPREDEFINED_CONTROLER;
		}
	}

	/**
	 * Metoda vrací true pokud je nastaveno cesta
	 * @return boolean -- true pokud je cesta nastavena
	 */
	public function isRoute() {
		if(self::$currentRouteId != null){
			return true;
		}
		return false;
	}

	/**
	 * Metoda přeparsuje article a vybere z něj cestu a vrátí article bez cesty
	 * @param string $article -- řetězec článku
	 */
//	public static function parseRoute($article) {
////		Pokud je zadána routa
//		$regs = array();
//		if(eregi('^([a-zA-Z0-9\-]+)_([a-zA-Z0-9\-]+)$', $article, $regs)){
//			$routesDetail = array();
////			pokud je zadáno idčko routy
//			if(eregi('^([a-zA-Z0-9\-]+)-([0-9]+)$', $regs[1], $routesDetail)){
//				self::$route = $routesDetail[1];
//				self::$routeId = $routesDetail[2];
//			} else {
//				self::$route = $regs[1];
//			}
//			$article = $regs[2];
//			self::$isRoute = true;
//		} else {
//			self::$isRoute = false;
//		}
//		return $article;
//	}
}

?>