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

class Url_Link_ModuleRequest extends Url_Link {
   const URL_REQUEST = 'module';
   /*
    * VEŘEJNÉ METODY
    */

    /**
     * Metoda nastaví akci modulu
     * @param string $request -- název akce modulu
     * @param string $output -- typ výstupu (koncovka souboru)
     * @return Url_Link_ModuleRequest
     */
    public function action($request, $output) {
       $this->file($request.".".$output);
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
      $this->lang = self::$currentlang;
      $this->category = self::$currentCategory;
      $this->paramsArray = self::$currentParams;
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
      $returnString = Url_Request::getBaseWebDir().self::URL_REQUEST.URL_SEPARATOR;
      if($this->lang != null) {
         $returnString.=$this->getLang();
      }
      if($this->category != null) {
         $returnString.=$this->getCategory();
      }
//      if($this->getRoute() != null) {
//         $returnString.=$this->getRoute();
//      }
      if($this->file != null) {
         $returnString.=$this->getFile();
      }
      //        Parsovatelné parametry
      if(!empty ($this->paramsArray)) {
         $returnString.=$this->getParams();
      }
      $returnString = $this->repairUrl($returnString);
      return $returnString;
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