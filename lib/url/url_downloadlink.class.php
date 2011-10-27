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

class Url_DownloadLink {
   private static $dwfile = 'download.php';
   private static $urlParam = 'url';
   private static $fileParam = 'file';

   private $path = null;

   private $file = null;


   /**
    * Konstruktor pro vytvoření objektu k odkazu pro stažení
    * @param string $path -- cesta k souboru (absolutní) (může obsahovat název souboru)
    * @param string $file -- (option) název souboru
    */
   public function __construct($path, $file = null) {
      $this->path = $path;
      if($file != null){
         $this->file = $file;
      }
   }
   
   /**
    * Metoda nastaví soubor
    * @param string $file -- soubor
    * @return Url_DownloadLink 
    */
   public function file($file)
   {
      $this->file = $file;
      return $this;
   }

   /**
    * Metoda převede objekt na řetězec
    *
    * @return string -- objekt jako řetězec
    */
   public function __toString() {
      $returnString = Url_Request::getBaseWebDir();
      $returnString .= self::$dwfile.'?'.http_build_query(array(
         self::$urlParam => str_replace(array(AppCore::getAppWebDir(), DIRECTORY_SEPARATOR), array('', '/'), $this->path),
         self::$fileParam => $this->file));
      return $returnString;
   }
}
?>