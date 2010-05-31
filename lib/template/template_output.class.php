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
   private static $outputType = 'xhtml';

   /**
    * Pole s hlavičkami, připravenými k odeslání
    * @var array
    */
   private static $headers = array();

   /**
    * Obsahuje délku výstupu pro hlavičku
    * @var int
    */
   private static $cntLenght = null;
   
   /**
    * Pole s typy pro které se budou odesílat hlavičky jako html
    * @var array
    */
   private static $htmlOutputTypes = array('php','html','xhtml','phtml','php3', 'xml');

   /**
    * Pole s typy pro které se budou odesílat hlavičky jako html
    * @var array
    */
   private static $txtOutputTypes = array('php','html','xhtml','phtml','php3', 'txt', 'js', 'json', 'xml','csv');

   /**
    * Proměná určuje jestli se jedná o binární výstup
    * @var boolean
    */
   private static $isBinaryOutput = true;

   /**
    * Konstruktor třídy
    * @param string $outputType -- typ odesílaných dat (xhtml, js, css, json, ...)
    */
   public static function factory($outputType) {
      if($outputType == null AND $outputType == "") {
         self::$outputType = "xhtml";
      } else {
         self::$outputType = $outputType;
      }
      if(in_array($outputType, self::$txtOutputTypes)){
         self::$isBinaryOutput = false;
      }
   }

   /**
    * Metoda připraví hlavičky pro zadaný výstup
    */
   private static function prepareHeaders() {
      switch (self::$outputType) {
         case "json":
            self::addHeader('Content-type: application/json; charset=utf-8');
            break;
         case "xml":
            self::addHeader('Content-type: text/xml; charset=utf-8');
            break;
         case "rss":
//            self::addHeader('Content-type: application/rss+xml');
            self::addHeader('Content-type: application/xml; charset=utf-8');
//            self::addHeader('Content-type: text/xml');
            break;
         case "atom":
//            self::addHeader('Content-type: application/atom+xml');
            self::addHeader('Content-type: application/xml; charset=utf-8');
//            self::addHeader('Content-type: text/xml');
            break;
         case "txt":
            self::addHeader('Content-type: text/plain; charset=utf-8');
            break;
         case "csv":
            self::addHeader('Content-type: text/x-csv; charset=utf-8');
            break;
         case "js":
            self::addHeader("Content-type: application/x-javascript; charset=utf-8");
            break;
         case "pdf":
            self::addHeader("Content-type: application/pdf");
            break;
         case "html":
         case "xhtml":
         case "php":
         default:
            self::addHeader("Content-type: text/html; charset=utf-8");
//            self::addHeader("Content-type: application/xhtml+xml");
            break;
      }
      if(Auth::isLogin()) {
         self::addHeader( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
         self::addHeader( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
         self::addHeader( 'Cache-Control: no-store, no-cache, must-revalidate' );
         self::addHeader( 'Cache-Control: post-check=0, pre-check=0', false );
         self::addHeader( 'Pragma: no-cache' );
      }
   }

   /**
    * Metoda přidá zadaou hlavičku k odesílaným hlavičkám
    * @param string $header
    */
   public static function addHeader($header) {
      array_push(self::$headers, $header);
   }

   /**
    * Metoda nastaví délu výstupu
    * @param int $lenght -- délka výstupu
    */
   public static function setContentLenght($lenght) {
      self::$cntLenght = $lenght;
   }

   /**
    * Metoda odešle hlavičky
    */
   public static function sendHeaders() {
      self::prepareHeaders();
      foreach (self::$headers as $header) {
         header($header);
      }
      if(self::$cntLenght !== null) {
         header("Content-Length: ".self::$cntLenght);
      }
   }
   
   /**
    * Metoda vrcí html typy výstupu (přípony souborů)
    * @return array
    */
   public static function getHtmlTypes(){
      return self::$htmlOutputTypes;
   }

   /**
    * Metoda vraci jestli je výstup binární nebo ne (u binárnho výstupu je vypnut buffer)
    * @return bool -- true pro binární výstup
    */
   public static function isBinaryOutput(){
      return self::$isBinaryOutput;
   }

   /**
    * Metoda nastavní typ výstupu
    * @param string $type -- typ výstupu (xhtml, json, txt, atd.)
    */
   public static function setOutputType($type) {
      self::$outputType = $type;
   }

   /**
    * Metoda nastaví že stránky bude určena ke stažení
    * @param string $fileName -- název souboru
    */
   public static function setDownload($fileName) {
      self::addHeader('Content-Disposition: attachment; filename="'.$fileName.'"');
   }
}
?>