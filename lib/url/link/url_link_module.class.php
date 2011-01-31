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
 * 						$LastChangedBy: jakub $ $LastChangedDate: 2009-08-28 15:44:00 +0200 (Pá, 28 srp 2009) $
 * @abstract 		Třída pro práci s odkazy
 */
class Url_Link_Module extends Url_Link {

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
    * @return Url_Link_Module -- objket Links
    */
   public function route($name = null, $params = array())
   {
      if ($name == null) {
         $this->route = null;
      } else {
         $route = $this->routes->getRoute($name);
         $routeReplacement = $route['replacement'];
         if ($routeReplacement != null | '') {
            $params = array_merge($this->routes->getRouteParams(), $params);
            foreach ($params as $pname => $pvalue) {
               $routeReplacement = str_replace("{" . $pname . "}", $pvalue, $routeReplacement);
            }
            // odstranění nepovinných parametrů, které nebyly zadány
            $routeReplacement = preg_replace(
                  array("/\([^{]*\{+[^{]*\}+[^{]*\)/i", "/[()]+/i"),
                  array("", ""),
                  $routeReplacement);
            $this->route = $routeReplacement;
         } else {
            $this->route = $route['regexp'];
         }
      }
      return $this;
   }

   /**
    * Metoda odstraní všechny parametry v odkazu
    * @return Url_Link_Module -- sám sebe
    */
   public function clear($withOutCategory = false)
   {
      $this->route()->rmParam();
      parent::clear($withOutCategory);
      return $this;
   }

   /*
    * PRIVÁTNÍ METODY
    */

   /**
    * Metoda inicializuje odkazy
    *
    */
   protected function _init()
   {
      $this->lang = self::$currentlang;
      $this->category = self::$currentCategory;
      $this->route = self::$currentRoute;
      $this->paramsArray = self::$currentParams;
   }

   /**
    * Metoda přidá cesty do objektu linků
    * @param Routes $routes -- cesty modulu
    */
   public function setModuleRoutes(Routes $routes)
   {
      $this->routes = $routes;
   }

   /**
    * Metoda vrací objekt s cestami modulu
    * @return Routes
    */
   public function getRoutes()
   {
      return $this->routes;
   }

   /*
    * MAGICKÉ METODY
    */
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