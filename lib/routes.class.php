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
	 * Oddělovač mezi cestou a článkem (route-article)
	 * @var string
	 */
	const ROUTE_SEPARATOR = '_';
	
	/**
	 * Pole s cestami
	 * @var array
	 */
	private $routes = array();
	
	/**
	 * Proměná se zvolenou cestou
	 * @var string
	 */
	private $route = null;
	
	/**
	 * Proměná s article, která se vrací zpět
	 * @var string
	 */
	private $article = null;

	/**
	 * Konstruktor třídy
	 *
	 * @param Article -- objekt článku (article)
	 */
	function __construct(Article &$article) {
		$this->article = $article;
		$this->initRoutes();
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
	private function getArticleObj() {
		return $this->article;
	}
	
	/**
	 * Metoda přidává cestu do seznamu cest
	 * @param string -- název cesty
	 */
	final public function addRoute($route) {
		array_push($this->routes, $route);
	}
	
	/**
	 * Metoda vrací použitou cestu
	 * @return string -- název cesty
	 */
	final public function getRoute() {
		$routeName = $isRoute = null;

		if($this->getArticleObj()->getArticle() != null){
			foreach ($this->routes as $route) {
				if ($this->getArticleObj()->getArticle() > $route.self::ROUTE_SEPARATOR AND
					substr_compare($this->getArticleObj()->getArticle(), $route.self::ROUTE_SEPARATOR, 0, strlen($route)+1) == 0){
						$routeName = $route;
						break;
				}
			}
			//		Nastavení nového článku - pouze článek
			if($routeName != null AND !empty($this->routes)){
				$this->route = $routeName;
				$this->getArticleObj()->setArticle(substr($this->getArticleObj()->getArticle(), strlen($route)+1));
				$this->getArticleObj()->setRoute(true);
			}
		}
		return $this->route;
	}
}

?>