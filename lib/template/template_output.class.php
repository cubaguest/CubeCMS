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

   /**
    * Obsahuje délku výstupu pro hlavičku
    * @var int
    */
   private $cntLenght = null;
   /*
    * ============= MAGICKÉ METODY
   */
   
   /**
    * Pole s typy pro které se budou odesílat hlavičky jako html
    * @var array
    */
   private static $htmlOutputTypes = array('php','html','xhtml','phtml','php3');

   /**
    * Konstruktor třídy
    * @param string $outputType -- typ odesílaných dat (xhtml, js, css, json, ...)
    */
   public function __construct($outputType) {
      if($outputType == null AND $outputType == "") {
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
         case "xml":
            $this->addHeader('Content-type: text/xml; charset=utf-8');
            break;
         case "rss":
//            $this->addHeader('Content-type: application/rss+xml');
            $this->addHeader('Content-type: application/xml; charset=utf-8');
//            $this->addHeader('Content-type: text/xml');
            break;
         case "atom":
//            $this->addHeader('Content-type: application/atom+xml');
            $this->addHeader('Content-type: application/xml; charset=utf-8');
//            $this->addHeader('Content-type: text/xml');
            break;
         case "txt":
            $this->addHeader('Content-type: text/plain; charset=utf-8');
            break;
         case "js":
            $this->addHeader("Content-type: application/x-javascript; charset=utf-8");
            break;
         case "html":
         case "xhtml":
         case "php":
         default:
            $this->addHeader("Content-type: text/html; charset=utf-8");
//            $this->addHeader("Content-type: application/xhtml+xml");
            break;
      }
      if(Auth::isLogin()) {
         $this->addHeader( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
         $this->addHeader( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
         $this->addHeader( 'Cache-Control: no-store, no-cache, must-revalidate' );
         $this->addHeader( 'Cache-Control: post-check=0, pre-check=0', false );
         $this->addHeader( 'Pragma: no-cache' );
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
    * Metoda nastaví délu výstupu
    * @param int $lenght -- délka výstupu
    */
   public function setContentLenght($lenght) {
      $this->cntLenght = $lenght;
   }

   /**
    * Metoda odešle hlavičky
    */
   public function sendHeaders() {
      foreach ($this->headers as $header) {
         header($header);
      }
      if($this->cntLenght !== null) {
         header("Content-Length: ".$this->cntLenght);
      }
   }
   
   /**
    * Metoda vrcí html typy výstupu (přípony souborů)
    * @return array
    */
   public static function getHtmlTypes(){
      return self::$htmlOutputTypes;
   }
}
?>