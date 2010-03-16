<?php
/**
 * Třída implementující základní rozhraní pro kompresi řetězce
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		třída pro základní kompresi
 */
class Template_Compress implements Template_Compress_Interface {
   /**
    * Proměnná obsahuje komprimovaný řetězeec
    * @var string
    */
   protected $string = null;

   /**
    * Konstruktor třídy pro zadání řetězce
    * @param string $string -- (option) řetězec pro kompresi
    */
   public function __contructor($string = null){
      $this->setString($string);
   }

   /**
    * Metoda nastavuje řetězec, který se má komprimovat
    * @param string $string -- komprimovatelný řetězec
    * @param boolean $mergre -- (def. true) jestli se má předchozí řetězec smazat
    */
   public function setString($string, $mergre = true){
      if($mergre === true){
         $this->string .= $string;
      } else {
         $this->string = $string;
      }
   }

   /**
    * Metoda vrací zkomprimovaný string, připravený pro odeslání
    */
   public function pack(){
      return $this->string;
   }


   /**
    * Metoda pro kompresi souborů
    * @param string $file -- název soouboru
    */
   public function file($file){
      if(file_exists($file)){
         $this->setString(file_get_contents($file));
      } else {
         throw new InvalidArgumentException(sprintf(_('Soubor "%s" na serveru neexistuje'),$file));
      }
   }

}
?>
