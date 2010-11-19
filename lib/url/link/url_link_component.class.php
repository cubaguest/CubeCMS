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

class Url_Link_Component extends Url_Link {
   /**
    * Část URL s komponentou
    */
   const URL_PART = 'component';

   /**
    * Proměná obsahuje název komponenty
    * @var string
    */
   private $componentName = null;

   /**
    * Proměnná obsahuje jestli se jedná pouze a akci komponenty
    * @var <type>
    */
   private $isOnlyComponentAction = false;

   /**
    * Název akce komponenty
    * @var string
    */
   private $actionName = null;

   /**
    * Typ výstupu s akce komponenty
    * @var string
    */
   private $outputType = null;

   /*
    * VEŘEJNÉ METODY
    */

    /**
     * Konstruktor
     * @param string $componentName -- název komponenty
     * @param bool $clear -- jestli je odkaz prázdný
     */
   function __construct($componentName, $clear = false) {
      $this->componentName = $componentName;
      parent::__construct($clear);
   }

   /**
    * Metoda vrátí odkaz na danou akci komponenty, nejčastěji ajax akce
    * @param string $action -- název akce
    * @param string $outputType -- typ výstupu
    * @return Url_Link_Component -- objekt Ulr odkazu
    */
   public function onlyAction($action, $outputType = 'html') {
      $this->isOnlyComponentAction = true;
      $this->actionName = $action;
      $this->outputType = $outputType;
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
      $this->paramsArray = self::$currentParams;
   }

   /*
    * MAGICKÉ METODY
    */
   public function  __toString() {
      if($this->isOnlyComponentAction) {
         return Url_Request::getBaseWebDir().self::URL_PART.URL_SEPARATOR
         .strtolower($this->componentName).URL_SEPARATOR.Locales::getLang()
         .URL_SEPARATOR.$this->category.URL_SEPARATOR.$this->actionName.'.'.$this->outputType;
      } else {
         return parent::__toString();
      }
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