<?php
/**
 * Třída pro práci výstupem
 * Třida nastavuje a odesílá hlavičky pro daný výstup
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: template_output.class.php 646 2009-08-28 13:44:00Z jakub $ VVE3.9.4 $Revision: 646 $
 * @author        $Author: jakub $ $Date: 2009-08-28 15:44:00 +0200 (Pá, 28 srp 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-08-28 15:44:00 +0200 (Pá, 28 srp 2009) $
 * @abstract 		Třída pro obsluhu šablony
 */

class Template_Output {
   /**
    * Název typu výstupu
    * @var string
    */
   private $outputType = 'xhtml';

   /**
    * Pole s hlavičkami, připravenými k odeslání
    * @var array
    */
   private $headers = array();

   /*
    * ============= MAGICKÉ METODY
    */

   /**
    * Konstruktor třídy
    * @param string $outputType -- typ odesílaných dat (xhtml, js, css, json, ...)
    */
   public function __construct($outputType) {
      if($outputType == null AND $outputType == ""){
         $this->outputType = "xhtml";
      } else {
         $this->outputType = $outputType;
      }
      $this->prepareHeaders();
   }

   /**
    * Metoda připraví hlavičky pro zadaný výstup
    */
   private function prepareHeaders() {
      switch ($this->outputType) {
         case "json":
            break;
         case "js":
            array_push($this->headers, "Content-type: application/x-javascript");
            break;
         case "xhtml":
         default:
            array_push($this->headers, "Content-type: text/html");
            break;
      }
   }

   /**
    * Metoda přidá zadaou hlavičku k odesílaným hlavičkám
    * @param string $header
    */
   public function addHeader($header) {
      array_push($this->headers, $header);
   }

   /**
    * Metoda odešle hlavičky
    */
   public function sendHeaders() {
      foreach ($this->headers as $header) {
         header($header);
      }
   }

}
?>