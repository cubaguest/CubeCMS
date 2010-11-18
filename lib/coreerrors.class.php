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
    * pole s chybami, varováními a poznámkami v aplikaci
    * @var array
    */
   private static $errorsArray = array();

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
   public static function addException(Exception $exception) {
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
      foreach (self::$errorsArray as $err) {
         $errArray->append(array('message'=>$err['message'],
             'file' => $err['file'],
             'name' => $err['name'],
             'code' => $err['code'],
             'line' => $err['line']));
      }

      return $errArray;
   }

   /**
    * Metoda zjišťuje jestli je pole s vyjímkami prázdné
    * @return boolean -- true pokud je pole prázdné
    */
   public static function isEmpty() {
      if(empty (self::$exceptionsArray) AND empty (self::$errorsArray)) {
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

   /**
    * Metoda pro zachytávání normálních chyb v enginu a modulech
    */
   public static function errorHandler($errno, $errstr, $errfile, $errline) {
      switch ($errno) {
         case E_USER_ERROR:
            $array = array();
            $array['name'] = "ERROR";
            $array['code'] = $errno;
            $array['message'] = $errstr;
            $array['file'] = $errfile;
            $array['line'] = $errline;
            break;

         case E_USER_WARNING:
            $array = array();
            $array['name'] = "WARNING";
            $array['code'] = $errno;
            $array['message'] = $errstr;
            $array['file'] = $errfile;
            $array['line'] = $errline;
            break;

         case E_USER_NOTICE:
            $array = array();
            $array['name'] = "NOTICE";
            $array['code'] = $errno;
            $array['message'] = $errstr;
            $array['file'] = $errfile;
            $array['line'] = $errline;
            break;

         default:
            $array = array();
            $array['name'] = "UNKNOWN ERROR";
            $array['code'] = $errno;
            $array['message'] = $errstr;
            $array['file'] = $errfile;
            $array['line'] = $errline;
            break;
      }
      array_push(self::$errorsArray, $array);
      return true;
   }

   public static function printErrors() {
      foreach (self::$exceptionsArray as $exception) {
         printf(_("%s(%d): %s v souboru %s, řádek %d")."<br />",get_class($exception),$exception->getCode(),
         $exception->getMessage(),$exception->getFile(),$exception->getLine());
      }
      foreach (self::$errorsArray as $err) {
         printf(_("%s(%d): %s v souboru %s, řádek %d")."<br />",$err['name'],$err['code'],
         $err['message'],$err['file'],$err['line']);
      }
   }

   public static function eraseErrors() {
      self::$exceptionsArray = array();
      self::$errorsArray = array();
   }

   /**
    * Metoda vrací hednorozměrné pole určené pro tisk
    * @return array
    */
   public static function getErrorsInArrayForPrint(){
      $re = array();
      foreach (self::$exceptionsArray as $exception) {
         if(VVE_DEBUG_LEVEL > 2) {
               $re [] = sprintf(_("%s(%d): %s v souboru %s, řádek %d"),get_class($exception),$exception->getCode(),
                  $exception->getMessage(),$exception->getFile(),$exception->getLine());
         } else {
            $re [] = $exception->getMessage();
         }
      }
      foreach (self::$errorsArray as $err) {
         if(VVE_DEBUG_LEVEL > 2) {
            $re [] = sprintf(_("%s(%d): %s v souboru %s, řádek %d"),$err['name'],$err['code'], $err['message'],$err['file'],$err['line']);
         } else {
            $re [] = $err['message'];
         }
      }
      return $re;
   }
}
?>
