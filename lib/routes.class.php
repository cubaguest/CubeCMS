<?php
/**
 * Třída pro obsluhu cest(routes).
 * Třida je určena k zjišťování a volby cesty pro kontroler a viewer.
 * Také slouží pro generování vlastních cest jednotlivých modulů.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro obsluhu cest modulu
 */

class Routes {
/**
 * Pole s cestami
 * @var array
 */
   private $routes = array();

   /**
    * Proměnná obsahuje urll část cesty
    * @var string
    */
   private $urlRequest = null;

   /**
    * Název vybrané cesty
    * @var string
    */
   private $selectedRoute = null;

   /**
    * Pole s parametry předanými v url
    * @var array
    */
   private $routeParams = array();

   /**
    * Konstruktor třídy
    */
   function __construct($urlRequest) {
      $this->urlRequest = $urlRequest;
      $this->initRoutes();
//      $this->checkRoutes();
   }

   /**
    * Metoda, která nastavuje cesty
    *
    */
   protected function initRoutes() {}

   /**
    * Metoda kontroluje cesty a vybírá správnou cestu
    */
   public function checkRoutes() {
      foreach ($this->routes as $routeName => $route) {
//         print "route<br>";
//         var_dump($this->urlRequest);
         $matches = array();
         if(preg_match("/^".(string)$route['regexp']."\/?$/i", $this->urlRequest, $matches)) {
            $this->selectedRoute = $routeName;
            $this->routeParams = $matches;
//            var_dump($matches);
//            print "matches<br>";
            return true;
         }
      }
   }

   /**
    * Metoda přidává cestu do seznamu cest
    * @param string -- název cesty
    */
   final public function addRoute($name, $regexp, $action, $replacement) {
      $rege = addcslashes($regexp, '/');

      // přidání povinného parametru
      $rege = preg_replace("/::([a-z0-9_-]+)::/", "(?P<$1>[a-z0-9_-]+)", $rege);
      // přidání nepovinných parametrů
      $rege = preg_replace("/:\?:([a-z0-9_-]+):\?:/", "(?:(?P<$1>[a-z0-9_-]+)\/?)?", $rege);
      $rege = preg_replace("/\//", "/?", $rege);
//      var_dump($rege);
      $this->routes[$name] = array('name' => $name,
          'regexp' => $rege,
          'route' => $regexp,
          'actionCtrl' => $action,
          'replacement' => $replacement);
   }

   /**
    * Metoda vrací název použité cesty
    * @return string -- název cesty
    */
   final public function getRouteName() {
      return $this->selectedRoute;
   }

   /**
    * Metoda vrací true pokud je nastavena cesta a je validní
    * @return boolean -- true pokud je cesta nastavena
    */
   public function isRoute() {
      if($this->selectedRoute === null) {
         return false;
      }
      return true;
   }

   /**
    * Metoda vrací název akce která se má podle cesty provádět
    * @return string
    */
   public function getActionName() {
      if($this->selectedRoute != null){
         return $this->routes[$this->selectedRoute]['actionCtrl'];
      }
      return false;
   }

   /**
    * Metoda vrací pole s parametry předanými v url
    * @return array -- pole parametrů
    */
   public function getRouteParams() {
      return $this->routeParams;
   }

   /**
    * Metoda vrací parametr předaný v url
    * @return string -- parametr
    */
   public function getRouteParam($name, $defaultValue = null) {
      if(isset($this->routeParams[$name])){
         return $this->routeParams[$name];
      } else {
         return $defaultValue;
      }
   }

   /**
    * Metoda vrací zadanou cestu ze seznamu cest
    * @param string $name -- název cesty
    * @return array -- pole s parametry cesty (název, regexp, ...)
    */
   public function getRoute($name) {
      if(isset($this->routes[$name])){
         return $this->routes[$name];
      } else {
         return null;
      }
   }
}
?>