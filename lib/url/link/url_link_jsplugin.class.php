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

class Url_Link_JsPlugin extends Url_Link {
   const URL_REQUEST = 'jsplugin';

   private $pluginname = null;
   /*
    * VEŘEJNÉ METODY
    */

   /**
    * Konstruktor
    * @param JsPlugin/string $jsplugin -- jsplugin pro který je link volán (vytváří se název)
    */
   function __construct($jsplugin) {
      $this->_init();
      if($jsplugin instanceof JsPlugin){
         $class = strtolower(get_class($jsplugin));
         $this->pluginname = substr($class, strpos($class, '_')+1, strlen($class));
      } else {
         $this->pluginname = $jsplugin;
      }
   }

    /**
     * Metoda nastaví akci modulu
     * @param string $request -- název akce modulu
     * @param string $output -- typ výstupu (koncovka souboru)
     * @return Url_Link_ModuleStatic
     */
    public function action($request, $output) {
       $this->file($request.".".$output);
       return $this;
    }

    /**
     * Metoda nasatví název modulu
     * @param string $module -- název modulu
     * @return Url_Link_ModuleStatic
     */
    public function jsplugin($name){
      $this->pluginname = strtolower($name);
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
      $this->category = 'cat-'.Category::getSelectedCategory()->getId();
//      $this->paramsArray = self::$currentParams;
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
      $returnString.=$this->pluginname.URL_SEPARATOR;
      if($this->category != null) {
         $returnString.=$this->getCategory();
      }
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
?>