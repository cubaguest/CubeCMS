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
   private static $outputType = 'html';

   /**
    * Pole s hlavičkami, připravenými k odeslání
    * @var array
    */
   public static $headers = array();

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
   private static $txtOutputTypes = array('php','html','xhtml','phtml','php3', 'txt', 'js', 'json', 'xml','csv','vcf');

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
      if(empty ($outputType)) {
         self::$outputType = "html";
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
         case "vcf":
            self::addHeader('Content-Type: text/x-vcard; charset=utf-8');
            break;
         case "js":
            self::addHeader("Content-type: application/x-javascript; charset=utf-8");
            break;
         case "pdf":
            self::addHeader("Content-type: application/pdf");
            break;
         case "jpg":
         case "jpeg":
            self::addHeader("Content-type: image/jpeg");
            break;
         case "html":
         case "xhtml":
         case "php":
         default:
            self::addHeader("Content-type: text/html; charset=utf-8");
//            self::addHeader("Content-type: application/xhtml+xml");
            break;
      }
   }

   /**
    * Metoda přidá zadaou hlavičku k odesílaným hlavičkám
    * @param string $header
    */
   public static function addHeader($header, $replace = true, $httpResponseCode = 200) {
      array_push(self::$headers, array('header' => $header, 'replace' => $replace, 'code' => $httpResponseCode));
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
      if(!headers_sent()){
         foreach (self::$headers as $header) {
            if($header['code'] == 200){
               header($header['header'], $header['replace']);
            } else {
               header($header['header'], $header['replace'], $header['code']);
            }
         }
         if(self::$cntLenght !== null) {
            header("Content-Length: ".self::$cntLenght);
         }
         header('Cache-Control: public, no-cache' );
         header('Cache-Control: max-age=60', false );
         if(Auth::isLogin()) {
            header('Cache-Control: store, no-cache, must-revalidate' ); //private,
            header('Cache-Control: post-check=0, pre-check=0', false );
            header("Cache-Control: max-age=1, s-maxage=1", false);
            header("ETag: PUB" . time());
            header("Last-Modified: " . gmdate("D, d M Y H:i:s", time()-10) . " GMT");
            header("Expires: " . gmdate("D, d M Y H:i:s", time() + 5) . " GMT");
            header("Pragma: no-cache");
         }
      } else {
//          throw new BadMethodCallException(_('Hlavičky již byly odeslány'));
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
    * Metoda vrací typ výstupu
    * @return string -- typ výstupu (xhtml, json, txt, atd.)
    */
   public static function getOutputType() {
      return self::$outputType;
   }

   /**
    * Metoda nastaví že stránky bude určena ke stažení
    * @param string $fileName -- název souboru
    */
   public static function setDownload($fileName) {
      self::addHeader('Content-Disposition: attachment; filename="'.$fileName.'"');
      self::addHeader('Content-Transfer-Encoding: binary');
      self::addHeader('Content-Description: File Transfer');
   }
}
?>