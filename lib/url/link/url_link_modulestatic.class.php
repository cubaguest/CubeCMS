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

class Url_Link_ModuleStatic extends Url_Link {
   const URL_REQUEST = 'module_s';

   private $moduleName = null;
   /*
    * VEŘEJNÉ METODY
    */

    /**
     * Metoda nastaví akci modulu
     * @param string $request -- název akce modulu
     * @param string $output -- typ výstupu (koncovka souboru)
     * @return Url_Link_ModuleStatic
     */
    public function action($request, $output = null) {
       $output == null ? $this->file($request."/") : $this->file($request.".".$output);
       return $this;
    }

    /**
     * Metoda nasatví název modulu
     * @param string $module -- název modulu
     * @return Url_Link_ModuleStatic
     */
    public function module($module){
      $this->moduleName = strtolower($module);
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
      $returnString = Url_Request::getBaseWebDir().($this->lang != null ? $this->getLang() : "")
         .self::URL_REQUEST."/".$this->moduleName."/";

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