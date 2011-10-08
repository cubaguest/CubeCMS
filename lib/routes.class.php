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
    * Routa pro nastavení kategorie
    */
   const MODULE_SETTINGS = 'settings';
   const MODULE_METADATA = 'metadata';

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
   function __construct($urlRequest, Category_Core $category = null, $routeVars = array()) {
      $this->urlRequest = $urlRequest;
      if(!empty ($routeVars)){
         foreach ($routeVars as $key => $value) {
            $this->{$key} = $value;
         }
      }
      
      $this->addRoute(self::MODULE_SETTINGS, 'settings', 'viewSettings', 'settings/'); // nastavení vzhledu modulu
      $this->addRoute(self::MODULE_METADATA, 'metadata', 'viewMetadata', 'metadata/'); // nastavení metadat modulu
      $this->initRoutes();
      if($category != null AND $category->haveFeed()){
         $this->addRoute('feed', null, 'main', '{type}.xml');
      }
      $normalRespClass = null;
      if(isset ($this->routes['normal'])){ // některé moduly mají v sobě. Je nutné dostat tuto cestu vždy na konec
         $normalRespClass = $this->routes['normal']['respondClass'];
         unset ($this->routes['normal']);
      }
      $this->addRoute('normal', null, 'main', null, $normalRespClass); // základní cesta
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
         $matches = array();
         $rege = preg_replace(array(
            "/::([a-z0-9_-]+)::/",
            "/:\?:([a-z0-9_-]+):\?:/",
            "/\//",
         ), array(
            "(?P<$1>[a-z0-9_-]+)",
            "(?:(?P<$1>[a-z0-9_-]+)\/?)?",
            "/+",
         ), (string)$route['regexp']);

         if(preg_match("/^".$rege."\/?$/i", $this->urlRequest, $matches)) {
            $this->selectedRoute = $routeName;
            foreach ($matches as $key => $value) {
               if(is_int($key)) unset ($matches[$key]);
            }
            $this->routeParams = $matches;
            return true;
         }
      }
   }

   /**
    * Metoda přidává cestu do seznamu cest
    * @param string -- název cesty
    */
   final public function addRoute($name, $regexp, $action, $replacement, $respondClass = null) {
      $rege = addcslashes($regexp, '/');
      $act = array('method' => $action, 'class' => null);
      // jestli bude existovat externí napojení
      if(strpos($action, '::') !== false){
         preg_match("/(?:(?P<class>\w+)::)?(?P<method>\w+)/", $action, $act);
      }

      $this->routes[$name] = array('name' => $name,
          'regexp' => $rege,
          'route' => $regexp,
          'actionCtrl' => $act['method'],
          'actionClass' => $act['class'],
          'replacement' => $replacement,
          'respondClass' =>  $respondClass
         );
   }

   /**
    * Metoda registruje cesty z daného modulu
    * @param string $module -- název modulu
    */
   final protected function registerModule($module, $routeVars = array())
   {
      $className = ucfirst($module).'_Routes';
      if(class_exists($className)){
         $mr = new $className($this->urlRequest, null, $routeVars);
         $this->routes = array_merge($this->routes, $mr->getRoutes());
      }
   }

   /**
    * metoda vrátí všechny cesty modulu
    * @return array 
    */
   final public function getRoutes()
   {
      return $this->routes;
   }

      /**
    * Metoda vrací typ odpovědi nastavené pro danou cestu (konstanty RESPOND_)
    * @return const RESPOND_
    */
   final public function getRespondClass(){
      return $this->routes[$this->selectedRoute]['respondClass'];
   }

   /**
    * Metoda vrací název použité cesty
    * @return string -- název cesty
    */
   final public function getRouteName() {
      return $this->selectedRoute;
   }
   
   /**
    * Metoda nastaví parametr pro cestu (POZOR! Url_Link jej přepíše)
    * @param string $param -- název parametr
    * @param string $value -- hodnota parametru
    * @return Routes -- sám sebe
    */
   final public function setRouteParam($param, $value){
      $this->routeParams[$name] = $value;
      return $this;
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
    * Metoda vrací název třídy pro danou metodu kontroleru
    * @return string
    */
   public function getClassName(){
      if($this->selectedRoute != null){
         return $this->routes[$this->selectedRoute]['actionClass'];
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