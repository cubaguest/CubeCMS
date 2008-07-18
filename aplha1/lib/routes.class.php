<?php
/**
 * Třída pro obsluhu cest
 *
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
		$routeName = null;
		if($this->getArticleObj()->getArticle() != null){
			foreach ($this->routes as $route) {
				$routeLen = strlen($route);
				$isRoute = substr_compare($this->getArticleObj()->getArticle(), $route.self::ROUTE_SEPARATOR, 0, $routeLen+1);
				//			echo "<br> compare: ".$isRoute." art: ".$this->getArticleObj()->getArticle()." comp: ".$route.self::ROUTE_SEPARATOR." route: ".$route."<br>";
				if($isRoute == 0){
					$routeName = $route;
					break;
				}
			}
			//TODO nekorektní počítání při nulovém počtu cest
			//		Nastavení nového článku - pouze článek
			if($isRoute == 0){
				$this->route = $routeName;
				$this->getArticleObj()->setArticle(substr($this->getArticleObj()->getArticle(), $routeLen+1));
			}
		}
		return $this->route;
	}
	
	
	
}

?>