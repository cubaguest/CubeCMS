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
	 * url část vybrané cesty
	 * @var string
	 */
	private static $currentRouteUrlPart = null;

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
	 * @param integer $id -- id cesty
     * @param bool $predefined -- o jaký druh cesty se jedná (předdefinovaná x uživatelská)
	 */
	public static function setCurrentRouteId($id, $predefined = false){
		//		Pokud je předdefinovaná cesta
		if($predefined){
			self::$currentRouteId = $id;
			self::$currentRouteIsPredefined = true;
		}
		//		je použita výchozí cesta
		else {
			self::$currentRouteId = $id;
			self::$currentRouteIsPredefined = false;
		}
	}

	/**
	 * Metoda, která nastavuje cesty
	 *
	 */
	function initRoutes(){}
	
	/**
	 * Metoda přidává cestu do seznamu cest
	 * @param string -- název cesty
	 */
	final public function addRoute($id, $routeName, $label) {
		$this->routes[$id] = array(self::ROUTE_NAME => $routeName, self::ROUTE_LABEl => $label);
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
     * Metoda vrací informace o předdefinované cestě
     * @param integer $id -- id routy
     * @return array -- pole s informacemi o routě
     */
    public function getPredefRoute($id) {
        if(isset($this->routes[$id])){
            $arr = array();
            
            $arr[0] = $this->routes[$id][self::ROUTE_LABEl];
            $arr[1] = $id;

            return $arr;
        }
    }
}

?>