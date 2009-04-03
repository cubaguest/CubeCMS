<?php
/**
 * Třída pro obsluhu chyb jádra a modulů
 * Třída implementuje shromažďování chyb v Jádře a jednotlivých module. Nejedná
 * se o chyby způsobené uživateli, ale o chyb způsobené chybným výpočtem. Využívá
 * třídy Exeption, pro zjišťování parametru chyby (řádek, hláška, atd.) Lze ji
 * využít při ladění aplikace, protože bude obsahovat jednotlivé vyjímky.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro obsluhu chyb jádra a modulů
 */
class CoreErrors {
   /**
    * pole s vyjímkami vyvolanými v aplikaci
    * @var array
    */
   private static $exceptionsArray = array();

   /**
    * Konstruktor třídy, přiřadí vyjímku do výstupu
    * @param Exception $e -- zachycená vyjímka
    */
   public function  __construct(Exception $e) {
      self::addException($e);
   }

   /**
    * Metoda přidá vijímku do pole vyjímek
    * @param Exception $exception -- zachycená vyjímka
    */
   public static function addException(Exception $exception){
      array_push(self::$exceptionsArray, $exception);
   }

   /**
    * Metoda vrací všechny zaznamenané vyjímky v poli
    * @return array -- pole vyjímek
    */
   public static function getErrors() {
      $errArray = new ArrayObject();

      foreach (self::$exceptionsArray as $exception) {
         $errArray->append(array('message'=>$exception->getMessage(),
                                 'file' => $exception->getFile(),
                                 'name' => get_class($exception),
                                 'line' => $exception->getLine(),
                                 'code' => $exception->getCode(),
                                 'trace' => $exception->getTrace()));
      }

      return $errArray;
   }

   /**
    * Metoda zjišťuje jestli je pole s vyjímkami prázdné
    * @return boolean -- true pokud je pole prázdné
    */
   public static function isEmpty() {
      if(empty (self::$exceptionsArray)){
         return true;
      }
      return false;
   }

   /**
    * Metoda vrací poslední chybu v enginu jako string
    * @return string -- poslední chyba
    */
   public static function getLastError() {
      return self::$exceptionsArray[0]->getMessage().' - '.self::$exceptionsArray[0]->getFile()
         .' > line: '.self::$exceptionsArray[0]->getLine();
   }
}
?>
