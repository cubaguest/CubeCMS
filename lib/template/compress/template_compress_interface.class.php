<?php
/**
 * Rozhraní pro tvorbu tříd pro kompresi kódu v šablonách. Nejčastěji použití
 * pro kompresi javascriptu a css.
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Rozhraní pro tvorbu kompresních tříd
 */
interface Template_Compress_Interface {

   /**
    * Konstruktor třídy pro zadání řetězce
    * @param string $string -- (option) řetězec pro kompresi
    */
   public function __contructor($string = null);

   /**
    * Metoda nastavuje řetězec, který se má komprimovat
    * @param string $string -- komprimovatelný řetězec
    * @param boolean $mergre -- (def. true) jestli se má předchozí řetězec smazat
    */
   public function setString($string, $mergre = true);

   /**
    * Metoda vrací zkomprimovaný string, připravený pro odeslání
    */
   public function pack();

   /**
    * Metoda pro kompresi souborů
    * @param string $file -- název soouboru
    */
   public function file($file);
}
?>
