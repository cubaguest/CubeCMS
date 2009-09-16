<?php
/**
 * Třída pro práci s odkazy.
 * Třída pro tvorbu a práci s odkazy aplikace, umožňuje jejich pohodlnou
 * tvorbu a validaci, popřípadě změnu jednotlivých parametrů. Umožňuje také
 * přímé přesměrování na zvolený (vytvořený) odkaz pomocí klauzule redirect.
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id: url_link.class.php 646 2009-08-28 13:44:00Z jakub $ VVE3.9.4 $Revision: 646 $
 * @author			$Author: jakub $ $Date: 2009-08-28 15:44:00 +0200 (Pá, 28 srp 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-08-28 15:44:00 +0200 (Pá, 28 srp 2009) $
 * @abstract 		Třída pro práci s odkazy
 */

class Url_Link_Module extends Url_Link {
   /**
    * Proměná se zvolenou cestou
    * @var string
    */
   private $route = null;

   /**
    * Aktuálně zvolená cesta
    * @var string
    */
   private static $currentRoute = null;

   /**
    * Objekt s cestami modulu
    * @var Routes
    */
   private $routes = null;

   /*
    * VEŘEJNÉ METODY
    */

   /**
    * Metoda nastavuje název a id routy
    * @param string -- název cesty
    * @param array -- pole s parametry pojmenovanými podle cesty
    *
    * @return Links -- objket Links
    */
   public function route($name = null, $params = array()) {
      if($name === null) {
         $this->route = null;
      } else {
         $route = $this->routes->getRoute($name);
         $routeReplacement = $route['replacement'];
         foreach ($params as $pname => $pvalue) {
            $routeReplacement = preg_replace("/{".$pname."}/i", $pvalue, $routeReplacement);
         }
         // odstranění nepoovinných parametrů, které nebyly zadány
         $routeReplacement = preg_replace("/\([^{]*\{+[^{]*\}+[^{]*\)/i", "", $routeReplacement);
         // odstranění nevyplněných nepovinných parametrů
         $routeReplacement = preg_replace("/[()]+/i", "", $routeReplacement);
         $this->route = $routeReplacement;
      }

      return $this;
   }

   /**
    * Metoda nastaví aktuální cestu (routu)
    * @param string $route -- aktuální cesta
    */
   public static function setRoute($route) {
      self::$currentRoute = $route;
   }

   /**
    * Metoda odstraní všechny parametry v odkazu
    * @return Links -- sám sebe
    */
   public function clear($withOutCategory = false) {
      $this->route()->rmParam();
      if($withOutCategory) {
         $this->category();
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
   protected function _init() {
   //      if(!$this->onlyWebRoot) {
      $this->lang = self::$currentlang;
      $this->category = self::$currentCategory;
      $this->route = self::$currentRoute;
      $this->paramsArray = self::$currentParams;
   //         $this->paramsNormalArray = self::$currentParamsNormalArray;
   //         $this->mediaType = Url_Request::getCurrentMediaUrlPart();
   //      }
   }

   /*
    * MAGICKÉ METODY
    */

   /**
    * Metoda převede objekt na řetězec
    *
    * @return string -- objekt jako řetězec
    */
   public function __toString() {
      $returnString = Url_Request::getBaseWebDir();
      if($this->getLang() != null) {
         $returnString.=$this->getLang();
      }
      if($this->getCategory() != null) {
         $returnString.=$this->getCategory();
      }
      if($this->getRoute() != null) {
         $returnString.=$this->getRoute();
      }
      //        Parsovatelné parametry
      if($this->getParams() != null) {
         $returnString.=$this->getParams();
      }
      $returnString = $this->repairUrl($returnString);
      return $returnString;
   }

   /**
    * Metoda vrací část s cestou pro url
    * @param string -- cesta (routa)
    */
   protected function getRoute() {
      if($this->route != null) {
         return $this->route.Url_Request::URL_SEPARATOR;
      } else {
         return null;
      }
   }

   /**
    * Metoda přidá cesty do objektu linků
    * @param Routes $routes -- cesty modulu
    */
   public function setModuleRoutes(Routes $routes) {
      $this->routes = $routes;
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